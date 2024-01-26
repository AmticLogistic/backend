<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;

class DocumentController extends Controller
{
    public function getRequired($id)
    {
        $data = DB::table('Requerimientos')
            ->join('Personas', 'Requerimientos.persona_id', '=', 'Personas.id')
            ->join('Usuarios', 'Usuarios.id', '=', 'Requerimientos.user_id')
            ->select(
                'Requerimientos.*',
                DB::raw('CONCAT("RE-", LPAD(Requerimientos.id, 6, "0")) as code'),
                'Personas.nombres as nombre',
                'Personas.apePaterno as paterno',
                'Personas.apeMaterno as materno',
                'Usuarios.usuario as user'
            )
            ->where('Requerimientos.id', $id)
            ->first();

        $detalles = DB::table('RequerimientosMateriales')
            ->join('Materiales', 'Materiales.id', '=', 'RequerimientosMateriales.Materiales_id')
            ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
            ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
            ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
            ->select(
                'RequerimientosMateriales.*',
                'Materiales.material',
                'Marcas.marca as marca_nombre',
                'Unidades.unidad as unidad_nombre',
                'Categorias.categoria as categoria_nombre'
            )
            ->where('Requerimientos_id', $id)
            ->get();
        $pdf = PDF::loadView('documents.required', ['data' => $data, 'detalles' => $detalles]);
        return $pdf->stream();
    }
    public function getSolicitude($id)
    {
        $data = DB::table('Solicitudes')
            ->join('Personas', 'Solicitudes.persona_id', '=', 'Personas.id')
            ->join('Usuarios', 'Usuarios.id', '=', 'Solicitudes.user_id')
            ->join('Empresas', 'Solicitudes.Empresas_id', '=', 'Empresas.id')
            ->select(
                'Solicitudes.*',
                DB::raw('CONCAT("CO-", LPAD(Solicitudes.id, 6, "0")) as code'),
                'Personas.nombres as nombre',
                'Personas.apePaterno as paterno',
                'Personas.apeMaterno as materno',
                'Usuarios.usuario as user',
                'Empresas.razonSocial as empresa',
            )
            ->where('Solicitudes.id', $id)
            ->first();

        $detalles = DB::table('SolicitudMateriales')
            ->join('Materiales', 'Materiales.id', '=', 'SolicitudMateriales.Materiales_id')
            ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
            ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
            ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
            ->select(
                'SolicitudMateriales.*',
                'Materiales.material',
                'Marcas.marca as marca_nombre',
                'Unidades.unidad as unidad_nombre',
                'Categorias.categoria as categoria_nombre'
            )
            ->where('Solicitudes_id', $id)
            ->get();
        $pdf = PDF::loadView('documents.solicitude', ['data' => $data, 'detalles' => $detalles]);
        return $pdf->stream();
    }
    public function getRequiredEdit($id)
    {

        try {
            $data = DB::table('Requerimientos')
                ->join('Personas', 'Requerimientos.persona_id', '=', 'Personas.id')
                ->join('Usuarios', 'Usuarios.id', '=', 'Requerimientos.user_id')
                ->select(
                    'Requerimientos.*',
                    DB::raw('CONCAT("RE-", LPAD(Requerimientos.id, 6, "0")) as code'),
                    'Personas.nombres as nombre',
                    'Personas.apePaterno as paterno',
                    'Personas.apeMaterno as materno',
                    'Usuarios.usuario as user'
                )
                ->where('Requerimientos.id', $id)
                ->first();

            $detalles = DB::table('RequerimientosMateriales')
                ->join('Materiales', 'Materiales.id', '=', 'RequerimientosMateriales.Materiales_id')
                ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
                ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
                ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
                ->select(
                    'RequerimientosMateriales.*',
                    'Materiales.material',
                    'Marcas.marca as marca_nombre',
                    'Unidades.unidad as unidad_nombre',
                    'Categorias.categoria as categoria_nombre',
                    DB::raw('(select ocm.OrdenesCompra_id from OrdenesCompraMateriales as ocm INNER JOIN OrdenesCompra as oc ON oc.id = ocm.OrdenesCompra_id  where  RequerimientosMateriales.Materiales_id = ocm.Materiales_id and RequerimientosMateriales.cantidad = ocm.cantidad AND oc.Requerimiento_id = '.$id.' LIMIT 1) AS useRow')
                )
                ->where('Requerimientos_id', $id)
                ->get();
            
            foreach ($detalles as $key => $value) {
                $value->pu = 0;
                $value->subtotal = 0;
            }
            $data->detalles = $detalles;
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function getOrderBuy($id)
    {
        $data = DB::table('OrdenesCompra')
            ->join('Empresas', 'OrdenesCompra.Empresas_id', '=', 'Empresas.id')
            ->join('Monedas', 'OrdenesCompra.Monedas_id', '=', 'Monedas.id')
            ->select(
                'OrdenesCompra.*',
                DB::raw('CONCAT("OC-", LPAD(OrdenesCompra.id, 6, "0")) as code'),
                'Empresas.razonSocial as empresa',
                'Empresas.numDocIdentificacion as ruc',
                'Empresas.correo as correo',
                'Empresas.direccionFiscal as direccionFiscal',
                'Monedas.moneda as moneda',
            )
            ->where('OrdenesCompra.id', $id)
            ->first();
        $detalles = DB::table('OrdenesCompraMateriales')
            ->join('Materiales', 'Materiales.id', '=', 'OrdenesCompraMateriales.Materiales_id')
            ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
            ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
            ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
            ->select(
                'OrdenesCompraMateriales.*',
                'Materiales.material',
                'Marcas.marca as marca_nombre',
                'Unidades.unidad as unidad_nombre',
                'Categorias.categoria as categoria_nombre'
            )
            ->where('OrdenesCompraMateriales.OrdenesCompra_id', $id)
            ->get();
        $pdf = PDF::loadView('documents.order', ['data' => $data, 'detalles' => $detalles]);
        return $pdf->stream();
    }
    public function getOrderBuyEdit($id)
    {

        try {
            $data = DB::table('OrdenesCompra')
                ->join('Empresas', 'OrdenesCompra.Empresas_id', '=', 'Empresas.id')
                ->join('Monedas', 'OrdenesCompra.Monedas_id', '=', 'Monedas.id')
                ->select(
                    'OrdenesCompra.*',
                    DB::raw('CONCAT("OC-", LPAD(OrdenesCompra.id, 6, "0")) as code'),
                    'Empresas.razonSocial as empresa',
                    'Empresas.numDocIdentificacion as ruc',
                    'Empresas.direccionFiscal as direccionFiscal',
                    'Monedas.moneda as moneda',
                )
                ->where('OrdenesCompra.id', $id)
                ->first();
            $detalles = DB::table('OrdenesCompraMateriales')
                ->join('Materiales', 'Materiales.id', '=', 'OrdenesCompraMateriales.Materiales_id')
                ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
                ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
                ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
                ->select(
                    'OrdenesCompraMateriales.*',
                    'OrdenesCompraMateriales.precioUnitario as pu',
                    'Materiales.material',
                    'Marcas.marca as marca_nombre',
                    'Unidades.unidad as unidad_nombre',
                    'Categorias.categoria as categoria_nombre'
                )
                ->where('OrdenesCompraMateriales.OrdenesCompra_id', $id)
                ->get();
            $data->detalles = $detalles;
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function getInput($id)
    {
        $data = DB::table('Entradas as e')
            ->select('e.*', DB::raw('CONCAT("RC-", LPAD(e.id, 6, "0")) as code'),  DB::raw('CONCAT("OC-", LPAD(e.OrdenCompra_id, 6, "0")) as code2'))
            ->selectRaw('(SELECT em.razonSocial FROM Empresas AS em WHERE em.id = e.Proveedores_id LIMIT 1) AS proveedor')
            ->selectRaw('(SELECT em.numDocIdentificacion FROM Empresas AS em WHERE em.id = e.Proveedores_id LIMIT 1) AS proveedordoc')
            ->selectRaw('(SELECT em.direccionFiscal FROM Empresas AS em WHERE em.id = e.Proveedores_id LIMIT 1) AS dir')
            ->selectRaw('(SELECT em.razonSocial FROM Empresas AS em WHERE em.id = e.Transportistas_id LIMIT 1) AS transportista')
            ->selectRaw('(SELECT em.numDocIdentificacion FROM Empresas AS em WHERE em.id = e.Transportistas_id LIMIT 1) AS transportistadoc')
            ->selectRaw('CASE
                            WHEN e.Monedas_id = 1 THEN "SOLES"
                            WHEN e.Monedas_id = 2 THEN "DOLARES"
                            ELSE NULL
                        END AS moneda')
            ->where('e.id', $id)
            ->first();
        $detalles = DB::table('EntradasMateriales')
            ->join('Materiales', 'Materiales.id', '=', 'EntradasMateriales.Materiales_id')
            ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
            ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
            ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
            ->select(
                'EntradasMateriales.*',
                'Materiales.material',
                'Marcas.marca as marca_nombre',
                'Unidades.unidad as unidad_nombre',
                'Categorias.categoria as categoria_nombre'
            )
            ->where('EntradasMateriales.Entradas_id', $id)
            ->get();
        foreach ($detalles as $key => $row) {
            $row->subtotal = $row->cantidad * $row->precioUnitario;
        }
        $pdf = PDF::loadView('documents.input', ['data' => $data, 'detalles' => $detalles]);
        return $pdf->stream();
    }
    public function getOutput($id)
    {
        $data = DB::table('Salidas')
            ->join('Personas', 'Salidas.personas_id', '=', 'Personas.id')
            ->join('Usuarios', 'Usuarios.id', '=', 'Salidas.user_id')
            ->join('CCostosPrimarios', 'CCostosPrimarios.id', '=', 'Salidas.CCostosPrimarios_id')
            ->select(
                'Salidas.*',
                DB::raw('CONCAT("SA-", LPAD(Salidas.id, 6, "0")) as code'),
                'Personas.nombres as nombre',
                'Personas.apePaterno as paterno',
                'Personas.apeMaterno as materno',
                'Usuarios.usuario as user',
                'CCostosPrimarios.centroCosto as cc'
            )
            ->where('Salidas.id', $id)
            ->first();

        $detalles = DB::table('SalidasMateriales')
            ->join('Materiales', 'Materiales.id', '=', 'SalidasMateriales.Materiales_id')
            ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
            ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
            ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
            ->select(
                'SalidasMateriales.*',
                'Materiales.material',
                'Marcas.marca as marca_nombre',
                'Unidades.unidad as unidad_nombre',
                'Categorias.categoria as categoria_nombre'
            )
            ->where('Salidas_id', $id)
            ->get();
        $pdf = PDF::loadView('documents.output', ['data' => $data, 'detalles' => $detalles]);
        return $pdf->stream();
    }
}
