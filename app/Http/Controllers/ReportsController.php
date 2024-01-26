<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class ReportsController extends Controller
{
    public function getReportInventory($init, $end)
    {
        try {
            $materiales = DB::table('Materiales')
                ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
                ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
                ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
                ->select(
                    'Materiales.*',
                    'Marcas.marca as marca_nombre',
                    'Unidades.unidad as unidad_nombre',
                    'Categorias.categoria as categoria_nombre',
                    DB::raw('CONCAT("MT-", LPAD(Materiales.id, 6, "0")) as id'),
                    DB::raw('(COALESCE((SELECT SUM(em.cantidad) FROM EntradasMateriales as em INNER JOIN Entradas as e ON e.id = em.Entradas_id AND e.estado=1 WHERE em.Materiales_id = Materiales.id AND e.fecha BETWEEN "' . $init . '" AND "' . $end . '"), 0) - COALESCE((SELECT SUM(sm.cantidad) FROM SalidasMateriales as sm INNER JOIN Salidas as s ON s.id = sm.Salidas_id AND s.estado=1 WHERE sm.Materiales_id = Materiales.id AND s.fecha BETWEEN "' . $init . '" AND "' . $end . '"), 0)) as stock'),
                )
                ->where('Unidades.unidad', '<>', 'SERV')
                ->where('Materiales.estado', 1)
                ->get();
            $response = ['status' => true, 'data' => $materiales];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function getReportInventoryPdf($init, $end)
    {

        $data = DB::table('Materiales')
            ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
            ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
            ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
            ->select(
                'Materiales.*',
                'Marcas.marca as marca_nombre',
                'Unidades.unidad as unidad_nombre',
                'Categorias.categoria as categoria_nombre',
                DB::raw('(COALESCE((SELECT SUM(em.cantidad) FROM EntradasMateriales as em INNER JOIN Entradas as e ON e.id = em.Entradas_id AND e.estado=1 WHERE em.Materiales_id = Materiales.id AND e.fecha BETWEEN "' . $init . '" AND "' . $end . '"), 0) - COALESCE((SELECT SUM(sm.cantidad) FROM SalidasMateriales as sm INNER JOIN Salidas as s ON s.id = sm.Salidas_id AND s.estado=1 WHERE sm.Materiales_id = Materiales.id AND s.fecha BETWEEN "' . $init . '" AND "' . $end . '"), 0)) as stock'),
            )
            ->where('Unidades.unidad', '<>', 'SERV')
            ->where('Materiales.estado', 1)
            ->get();
        foreach ($data as $key => $value) {
            $value->id = 'MAT-' . str_pad($value->id, 6, '0', STR_PAD_LEFT);
        };
        $pdf = PDF::loadView('reports.inventory', ['data' => $data , 'init' => $init, 'end' => $end]);
        return $pdf->stream();
    }
    public function getReportKardex($init, $end)
    {
        try {
            $resultado = DB::table(DB::raw('Materiales as m'))
            ->select(
                'e.fecha',
                'em.id as idg',
                DB::raw('CONCAT("MT-", LPAD(m.id, 6, "0")) as id'),
                DB::raw("'P/I Compra' as tipoDocumento"),
                DB::raw("CONCAT('EM-', LPAD(e.id, 6, '0')) as nDocumento"),
                DB::raw("CONCAT('MT-', LPAD(m.id, 6, '0')) as codigo"),
                DB::raw("(SELECT Empresas.razonSocial FROM Empresas WHERE Empresas.id = e.Proveedores_id LIMIT 1) as concepto"),
                'm.material',
                'Marcas.marca',
                'Unidades.unidad',
                'p.numDocIdentificacion as doc',
                'em.cantidad as entrada',
                DB::raw('NULL as salida'),
                DB::raw('(COALESCE((SELECT SUM(em1.cantidad) FROM EntradasMateriales as em1 INNER JOIN Entradas as e1 ON e1.id = em1.Entradas_id AND e1.estado=1 WHERE em1.Materiales_id = m.id AND e1.fecha <= e.fecha), 0) - COALESCE((SELECT SUM(sm1.cantidad) FROM SalidasMateriales as sm1 INNER JOIN Salidas as s1 ON s1.id = sm1.Salidas_id AND s1.estado=1 WHERE sm1.Materiales_id = m.id AND s1.fecha <= e.fecha), 0)) as saldo')
            )
            ->join('EntradasMateriales as em', 'em.materiales_id', '=', 'm.id')
            ->join('Entradas as e', 'e.id', '=', 'em.Entradas_id')
            ->join('Marcas', 'm.Marcas_id', '=', 'Marcas.id')
            ->join('Unidades', 'm.Unidades_id', '=', 'Unidades.id')
            ->leftJoin('Empresas as p', 'p.id', '=', 'e.id')
            ->where('e.fecha', '>=', $init.' 00:00:00')
            ->where('e.fecha', '<=',  $end.' 23:59:59')
            ->where('e.estado', '=', 1);

        $resultado->union(
            DB::table(DB::raw('Materiales as m'))
                ->select(
                    's.fecha',
                    'sm.id as idg',
                    DB::raw('CONCAT("MT-", LPAD(m.id, 6, "0")) as id'),
                    DB::raw("'Parte de Salida' as tipoDocumento"),
                    DB::raw("CONCAT('EM-', LPAD(s.id, 6, '0')) as nDocumento"),
                    DB::raw("CONCAT('MT-', LPAD(m.id, 6, '0')) as codigo"),
                    DB::raw("(SELECT CCostosPrimarios.centroCosto FROM CCostosPrimarios WHERE CCostosPrimarios.id = s.CCostosPrimarios_id LIMIT 1) as concepto"),
                    'm.material',
                    'Marcas.marca',
                    'Unidades.unidad',
                    DB::raw('NULL as doc'),
                    DB::raw('NULL as entrada'),
                    'sm.cantidad as salida',
                    DB::raw('(COALESCE((SELECT SUM(em1.cantidad) FROM EntradasMateriales as em1 INNER JOIN Entradas as e2 ON e2.id = em1.Entradas_id AND e2.estado=1 WHERE em1.Materiales_id = m.id AND e2.fecha <= s.fecha), 0) - COALESCE((SELECT SUM(sm1.cantidad) FROM SalidasMateriales as sm1 INNER JOIN Salidas as s2 ON s2.id = sm1.Salidas_id AND s2.estado=1 WHERE sm1.Materiales_id = m.id AND s2.fecha <= s.fecha), 0)) as saldo')
                )
                ->join('SalidasMateriales as sm', 'sm.materiales_id', '=', 'm.id')
                ->join('Salidas as s', 's.id', '=', 'sm.Salidas_id')
                ->join('Marcas', 'm.Marcas_id', '=', 'Marcas.id')
                ->join('Unidades', 'm.Unidades_id', '=', 'Unidades.id')
                ->where('s.fecha', '>=', $init.' 00:00:00')
                ->where('s.fecha', '<=', $end.' 23:59:59')
                ->where('s.estado', '=', 1)
        );
            
            

            $resultado = $resultado->orderBy('material')->orderBy('fecha')->get();
            foreach ($resultado as $key => $row) {
                $row->id = $key + 1;
            }
            $response = ['status' => true, 'data' => $resultado];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function getReportKardexPdf($init, $end)
    {

        $resultado = DB::table(DB::raw('Materiales as m'))
            ->select(
                'e.fecha',
                'em.id as idg',
                DB::raw("'P/I Compra' as tipoDocumento"),
                DB::raw("CONCAT('EM-', LPAD(e.id, 6, '0')) as nDocumento"),
                DB::raw("CONCAT('MT-', LPAD(m.id, 6, '0')) as codigo"),
                DB::raw("(SELECT Empresas.razonSocial FROM Empresas WHERE Empresas.id = e.Proveedores_id LIMIT 1) as concepto"),
                'm.material',
                'Marcas.marca',
                'Unidades.unidad',
                'p.numDocIdentificacion as doc',
                'em.cantidad as entrada',
                DB::raw('NULL as salida'),
                DB::raw('(COALESCE((SELECT SUM(em1.cantidad) FROM EntradasMateriales as em1 INNER JOIN Entradas as e1 ON e1.id = em1.Entradas_id AND e1.estado=1 WHERE em1.Materiales_id = m.id AND e1.fecha <= e.fecha), 0) - COALESCE((SELECT SUM(sm1.cantidad) FROM SalidasMateriales as sm1 INNER JOIN Salidas as s1 ON s1.id = sm1.Salidas_id AND s1.estado=1 WHERE sm1.Materiales_id = m.id AND s1.fecha <= e.fecha), 0)) as saldo')
            )
            ->join('EntradasMateriales as em', 'em.materiales_id', '=', 'm.id')
            ->join('Entradas as e', 'e.id', '=', 'em.Entradas_id')
            ->join('Marcas', 'm.Marcas_id', '=', 'Marcas.id')
            ->join('Unidades', 'm.Unidades_id', '=', 'Unidades.id')
            ->leftJoin('Empresas as p', 'p.id', '=', 'e.id')
            ->where('e.fecha', '>=', $init.' 00:00:00')
            ->where('e.fecha', '<=',  $end.' 23:59:59')
            ->where('e.estado', '=', 1);

        $resultado->union(
            DB::table(DB::raw('Materiales as m'))
                ->select(
                    's.fecha',
                    'sm.id as idg',
                    DB::raw("'Parte de Salida' as tipoDocumento"),
                    DB::raw("CONCAT('EM-', LPAD(s.id, 6, '0')) as nDocumento"),
                    DB::raw("CONCAT('MT-', LPAD(m.id, 6, '0')) as codigo"),
                    DB::raw("(SELECT CCostosPrimarios.centroCosto FROM CCostosPrimarios WHERE CCostosPrimarios.id = s.CCostosPrimarios_id LIMIT 1) as concepto"),
                    'm.material',
                    'Marcas.marca',
                    'Unidades.unidad',
                    DB::raw('NULL as doc'),
                    DB::raw('NULL as entrada'),
                    'sm.cantidad as salida',
                    DB::raw('(COALESCE((SELECT SUM(em1.cantidad) FROM EntradasMateriales as em1 INNER JOIN Entradas as e2 ON e2.id = em1.Entradas_id AND e2.estado=1 WHERE em1.Materiales_id = m.id AND e2.fecha <= s.fecha), 0) - COALESCE((SELECT SUM(sm1.cantidad) FROM SalidasMateriales as sm1 INNER JOIN Salidas as s2 ON s2.id = sm1.Salidas_id AND s2.estado=1 WHERE sm1.Materiales_id = m.id AND s2.fecha <= s.fecha), 0)) as saldo')
                )
                ->join('SalidasMateriales as sm', 'sm.materiales_id', '=', 'm.id')
                ->join('Salidas as s', 's.id', '=', 'sm.Salidas_id')
                ->join('Marcas', 'm.Marcas_id', '=', 'Marcas.id')
                ->join('Unidades', 'm.Unidades_id', '=', 'Unidades.id')
                ->where('s.fecha', '>=', $init.' 00:00:00')
                ->where('s.fecha', '<=', $end.' 23:59:59')
                ->where('s.estado', '=', 1)
        );

        $resultado = $resultado->orderBy('material')->orderBy('fecha')->get();

        $pdf = PDF::loadView('reports.kardex', ['data' => $resultado, 'init' => $init, 'end' => $end])->setPaper('letter', 'landscape');
        return $pdf->stream();
    }
    public function getReportOrdenes($id, $init, $end)
    {
        try {
            $empresa = DB::table('Empresas')->where('id', $id)->first();

            $data = DB::table('OrdenesCompra AS oc')
                ->join('OrdenesCompraMateriales AS ocm', 'ocm.OrdenesCompra_id', '=', 'oc.id')
                ->select(
                    DB::raw("'O/C Bienes' AS tipoDoc"),
                    DB::raw('CONCAT("OC-", LPAD(oc.id, 6, "0")) AS numDoc'),
                    'oc.fecha',
                    DB::raw('(SELECT CONCAT("MAT-", LPAD(mat.id, 6, "0")) FROM Materiales AS mat WHERE mat.id = ocm.Materiales_id LIMIT 1) AS codigo'),
                    DB::raw('(SELECT ma.material FROM Materiales AS ma WHERE ma.id = ocm.Materiales_id LIMIT 1) AS material'),
                    'ocm.cantidad AS cantidadPe',
                    DB::raw('COALESCE((SELECT SUM(em.cantidad) FROM EntradasMateriales as em INNER JOIN Entradas as e ON e.id = em.Entradas_id WHERE em.Materiales_id = ocm.Materiales_id AND oc.id = e.OrdenCompra_id), 0) AS cantidadAt'),
                    DB::raw('ocm.cantidad - COALESCE((SELECT SUM(em.cantidad) FROM EntradasMateriales as em INNER JOIN Entradas as e ON e.id = em.Entradas_id WHERE em.Materiales_id = ocm.Materiales_id AND oc.id = e.OrdenCompra_id), 0) AS cantidadTo'),
                    DB::raw('COALESCE((SELECT em.cantidad * em.precioUnitario FROM EntradasMateriales as em INNER JOIN Entradas as e ON e.id = em.Entradas_id WHERE em.Materiales_id = ocm.Materiales_id AND oc.id = e.OrdenCompra_id), 0) AS total')
                )
                ->where('oc.Empresas_id', $id)
                ->whereBetween('oc.fecha', [$init, $end])
                ->orderByDesc('oc.id')
                ->get();

            foreach ($data as $key => $row) {
                $row->id = $key + 1;
            }

            $response = ['status' => true, 'data' => $data, 'empresa' => $empresa];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }

        return response()->json($response, $codeResponse);
    }

    public function getReportOrdenesPDF($id, $init, $end)
    {
        $empresa = DB::table('Empresas')->where('id', $id)->first();
        $data = DB::table('OrdenesCompra AS oc')
            ->join('OrdenesCompraMateriales AS ocm', 'ocm.OrdenesCompra_id', '=', 'oc.id')
            ->select(
                DB::raw("'O/C Bienes' AS tipoDoc"),
                DB::raw('CONCAT("OC-", LPAD(oc.id, 6, "0")) AS numDoc'),
                'oc.fecha',
                DB::raw('(SELECT CONCAT("MAT-", LPAD(mat.id, 6, "0")) FROM Materiales AS mat WHERE mat.id = ocm.Materiales_id LIMIT 1) AS codigo'),
                DB::raw('(SELECT ma.material FROM Materiales AS ma WHERE ma.id = ocm.Materiales_id LIMIT 1) AS material'),
                'ocm.cantidad AS cantidadPe',
                DB::raw('COALESCE((SELECT SUM(em.cantidad) FROM EntradasMateriales as em INNER JOIN Entradas as e ON e.id = em.Entradas_id WHERE em.Materiales_id = ocm.Materiales_id AND oc.id = e.OrdenCompra_id), 0) AS cantidadAt'),
                DB::raw('ocm.cantidad - COALESCE((SELECT SUM(em.cantidad) FROM EntradasMateriales as em INNER JOIN Entradas as e ON e.id = em.Entradas_id WHERE em.Materiales_id = ocm.Materiales_id AND oc.id = e.OrdenCompra_id), 0) AS cantidadTo'),
                DB::raw('COALESCE((SELECT em.cantidad * em.precioUnitario FROM EntradasMateriales as em INNER JOIN Entradas as e ON e.id = em.Entradas_id WHERE em.Materiales_id = ocm.Materiales_id AND oc.id = e.OrdenCompra_id), 0) AS total')
            )
            ->where('oc.Empresas_id', $id)
            ->whereBetween('oc.fecha', [$init, $end])
            ->orderByDesc('oc.id')
            ->get();
        if (count($data) > 0) {
            $pdf = PDF::loadView('reports.orders', ['data' => $data, 'empresa' => $empresa])->setPaper('letter', 'landscape');
            return $pdf->stream();
        } else {
            return "sin datos";
        }
    }
}
