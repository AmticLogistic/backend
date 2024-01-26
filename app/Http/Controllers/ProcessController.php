<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessController extends Controller
{
    public function listRequerimientos($init, $end)
    {
        try {
            $data = DB::table('Requerimientos')
                ->join('Personas', 'Requerimientos.persona_id', '=', 'Personas.id')
                ->select(
                    'Requerimientos.*',
                    DB::raw('CONCAT("RE-", LPAD(Requerimientos.id, 6, "0")) as code'),
                    'Personas.nombres as nombre',
                    'Personas.apePaterno as paterno',
                    'Personas.apeMaterno as materno'
                )
                ->where('fecha', '>=', $init)
                ->where('fecha', '<=', $end)
                ->orderBy('Requerimientos.id', 'desc')
                ->get();
            if (count($data) > 0) {
                foreach ($data as $key => $row) {
                    $row->list = DB::table('RequerimientosMateriales')
                        ->where('Requerimientos_id', $row->id)
                        ->join('Materiales', 'Materiales.id', '=', 'RequerimientosMateriales.Materiales_id')
                        ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
                        ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
                        ->selectRaw('Materiales.material, RequerimientosMateriales.*, Marcas.marca as marca_nombre, Unidades.unidad as unidad_nombre, 0 as ckeck')
                        ->get();
                    $row->cantidadOrden = DB::table('OrdenesCompra as o')
                        ->join('OrdenesCompraMateriales as oc', 'oc.OrdenesCompra_id', '=', 'o.id')
                        ->where('o.Requerimiento_id', '=',  $row->id)
                        ->sum('oc.cantidad');

                    $row->cantidadTotal = 0;
                    if (count($row->list) > 0) {
                        foreach ($row->list as $key2 => $li) {
                            $row->cantidadTotal += $li->cantidad;
                        }
                    }
                }
            }
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveRequerimiento(Request $request)
    {
        $requerimientos = [
            'fecha' => $request->fecha ?? date("Y-m-d"),
            'areaSolicita' => $request->areaSolicita ?? null,
            'persona_id' => $request->persona_id,
            'observacion' => $request->observacion ?? null,
        ];
        try {
            DB::beginTransaction();
            if ($request->id != 0) {
                DB::table('Requerimientos')
                    ->where('id', $request->id)
                    ->update($requerimientos);
                DB::table('RequerimientosMateriales')
                    ->where('Requerimientos_id', $request->id)
                    ->delete();
                foreach ($request->detalles as $key => $value) {
                    DB::table('RequerimientosMateriales')->insert([
                        'cantidad' => $value['cantidad'],
                        'observacion' => $value['observacion'],
                        'Materiales_id' => $value['Materiales_id'] ?? $value['Material_id'],
                        'Requerimientos_id' => $request->id
                    ]);
                }

                $requerimiento = $request->id;
            } else {

                $requerimiento = DB::table('Requerimientos')->insertGetId($requerimientos);

                foreach ($request->detalles as $key => $value) {
                    DB::table('RequerimientosMateriales')->insert([
                        'cantidad' => $value['cantidad'],
                        'observacion' => $value['observacion'],
                        'Materiales_id' => $value['Material_id'],
                        'Requerimientos_id' => $requerimiento
                    ]);
                }
            }

            DB::commit();
            $response = ['status' => true, 'data' => $requerimiento];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function listInventory($init, $end)
    {
        try {
            $data = DB::table('Inventario')
                ->select(
                    'Inventario.*',
                    DB::raw('CONCAT("IN-", LPAD(Inventario.id, 6, "0")) as code')
                )
                ->where('fecha', '>=', $init)
                ->where('fecha', '<=', $end)
                ->where('estado', 1)
                ->orderBy('Inventario.id', 'desc')
                ->get();
            if (count($data) > 0) {
                foreach ($data as $key => $row) {
                    $row->list = DB::table('InventarioMateriales')
                        ->where('Inventario_id', $row->id)
                        ->join('Materiales', 'Materiales.id', '=', 'InventarioMateriales.Materiales_id')
                        ->join('Marcas', 'Materiales.Marcas_id', '=', 'Marcas.id')
                        ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
                        ->selectRaw('Materiales.material, InventarioMateriales.*, Marcas.marca as marca_nombre, Unidades.unidad as unidad_nombre')
                        ->get();
                }
            }
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveInventory(Request $request)
    {
        $requerimientos = [
            'fecha' => $request->fecha ?? date("Y-m-d"),
            'observacion' => $request->observacion ?? null,
        ];
        try {
            DB::beginTransaction();
            if ($request->id != 0) {
                DB::table('Inventario')
                    ->where('id', $request->id)
                    ->update($requerimientos);
                DB::table('InventarioMateriales')
                    ->where('Inventario_id', $request->id)
                    ->delete();
                foreach ($request->detalles as $key => $value) {
                    DB::table('InventarioMateriales')->insert([
                        'cantidad' => $value['cantidad'],
                        'observacion' => $value['observacion'],
                        'Materiales_id' => $value['Materiales_id'] ?? $value['Material_id'],
                        'Inventario_id' => $request->id
                    ]);
                }

                $requerimiento = $request->id;
            } else {

                $requerimiento = DB::table('Inventario')->insertGetId($requerimientos);

                foreach ($request->detalles as $key => $value) {
                    DB::table('InventarioMateriales')->insert([
                        'cantidad' => $value['cantidad'],
                        'observacion' => $value['observacion'],
                        'Materiales_id' => $value['Material_id'],
                        'Inventario_id' => $requerimiento
                    ]);
                }
            }

            DB::commit();
            $response = ['status' => true, 'data' => $requerimiento];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function deleteInventory($id)
    {
        try {
            $inventario = DB::table('Inventario')->where('id', $id)->update(['estado' => 0]);
            $response = ['status' => true, 'data' => $inventario];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveSolicitud(Request $request)
    {
        $requerimientos = [
            'fecha' => $request->fecha ?? date("Y-m-d"),
            'areaSolicita' => $request->areaSolicita ?? null,
            'persona_id' => $request->persona_id,
            'observacion' => $request->observacion ?? null,
            'Empresas_id' => $request->Empresas_id ?? null,
        ];
        try {
            DB::beginTransaction();


            $requerimiento = DB::table('Solicitudes')->insertGetId($requerimientos);

            foreach ($request->list as $key => $value) {
                if (!empty($value['check']) && $value['check'] == 'true') {
                    DB::table('SolicitudMateriales')->insert([
                        'cantidad' => $value['cantidad'] ?? null,
                        'observacion' => $value['observacion'] ?? null,
                        'Materiales_id' => $value['Materiales_id'] ?? null,
                        'Solicitudes_id' => $requerimiento
                    ]);
                }
            }

            DB::commit();
            $response = ['status' => true, 'data' => $requerimiento];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function deleteRequerimiento($id)
    {
        try {
            $requerimiento = DB::table('Requerimientos')->where('id', $id)->update(['Orden_id' => '-1']);
            $response = ['status' => true, 'data' => $requerimiento];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveOrden(Request $request)
    {
        $requerimientos = [
            'fecha' => $request->fecha ?? date("Y-m-d"),
            'total' => $request->total,
            'sitioEntrega' => $request->sitioEntrega,
            'revisadoPor' => $request->revisadoPor,
            'aprobadoPor' => $request->aprobadoPor,
            'observacion' => $request->observacion,
            'Monedas_id' => $request->Monedas_id,
            'Empresas_id' => $request->Empresas_id,
            'Requerimiento_id' => $request->requerimiento_id ??  $request->Requerimiento_id,
            'MetodosPago_id' => $request->MetodosPago_id,
        ];

        try {
            DB::beginTransaction();

            if ($request->id && $request->id != 0) {
                DB::table('OrdenesCompra')->where('id', $request->id)->update($requerimientos);
                $orden = $request->id;
                DB::table('OrdenesCompraMateriales')->where('OrdenesCompra_id', $request->id)->delete();
            } else {
                $orden = DB::table('OrdenesCompra')->insertGetId($requerimientos);
            }

            foreach ($request->detalles as $value) {
                if ($value['pu'] != 0) {
                    $detalles = DB::table('OrdenesCompraMateriales')->insertGetId([
                        'cantidad' => $value['cantidad'],
                        'precioUnitario' => $value['pu'],
                        'subtotal' => $value['subtotal'],
                        'observacion' => $value['observacion'],
                        'OrdenesCompra_id' => $orden,
                        'Materiales_id' => $value['Materiales_id']
                    ]);
                }
            }

            if ($request->requerimiento_id) {
                DB::table('Requerimientos')->where('id', $request->requerimiento_id)->update(['Orden_id' => $orden]);
            }

            DB::commit();

            $response = ['status' => true, 'data' => $orden];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }

        return response()->json($response, $codeResponse);
    }

    public function saveInput(Request $request)
    {
        $entrada = [
            'fecha' => $request->fecha ? $request->fecha . ' ' . now()->format('H:i:s') : now()->format('Y-m-d H:i:s'),
            'total' => $request->total,
            'puestoEn' => $request->puestoEn,
            'guiaRemision' => $request->guiaRemision,
            'serieComprobante' => $request->serieComprobante,
            'correlativoComprobante' => $request->correlativoComprobante,
            'observacion' => $request->observacion,
            'Proveedores_id' => $request->Proveedores_id,
            'Transportistas_id' => $request->Transportistas_id,
            'Monedas_id' => $request->Monedas_id,
            'OrdenCompra_id' => $request->OrdenCompra_id ?? null,
        ];
        try {
            DB::beginTransaction();
            $register = DB::table('Entradas')->insertGetId($entrada);
            foreach ($request->detalles as $key => $value) {
                $detalles = DB::table('EntradasMateriales')->insertGetId([
                    'cantidad' => $value['cantidad'],
                    'precioUnitario' => $value['precioUnitario'],
                    'observacion' =>  $value['observacion'],
                    'Materiales_id' => $value['Materiales_id'],
                    'Entradas_id' => $register,
                ]);
            }
            DB::commit();
            $response = ['status' => true, 'data' => $register];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function deleteInput($id)
    {
        try {
            $requerimiento = DB::table('Entradas')->where('id', $id)->update(['estado' => 0]);
            $response = ['status' => true, 'data' => $requerimiento];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveOutput(Request $request)
    {
        $salida = [
            'fecha' => $request->fecha ? $request->fecha . ' ' . now()->format('H:i:s') : now()->format('Y-m-d H:i:s'),
            'observacion' => $request->observacion,
            'Personas_id' => $request->Personas_id,
            'TiposMovimientos_id' => 2,
            'CCostosPrimarios_id' => $request->CCostosPrimarios_id,
        ];
        try {
            DB::beginTransaction();
            $register = DB::table('Salidas')->insertGetId($salida);
            foreach ($request->detalles as $key => $value) {
                $detalles = DB::table('SalidasMateriales')->insertGetId([
                    'cantidad' => $value['cantidad'],
                    'observacion' =>  null,
                    'Materiales_id' => $value['Material_id'],
                    'Salidas_id' => $register,
                ]);
            }
            DB::commit();
            $response = ['status' => true, 'data' => $register];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }

    public function deleteOutput($id)
    {
        try {
            $salida = DB::table('Salidas')->where('id', $id)->update(['estado' => 0]);
            $response = ['status' => true, 'data' => $salida];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }

    public function listOutput($init, $end)
    {
        try {
            $data = DB::table('Salidas')
                ->join('Personas', 'Salidas.personas_id', '=', 'Personas.id')
                ->join('CCostosPrimarios', 'Salidas.CCostosPrimarios_id', '=', 'CCostosPrimarios.id')
                ->select(
                    'Salidas.*',
                    DB::raw('CONCAT("SA-", LPAD(Salidas.id, 6, "0")) as code'),
                    'CCostosPrimarios.centroCosto as cc',
                    'Personas.nombres as nombre',
                    'Personas.apePaterno as paterno',
                    'Personas.apeMaterno as materno',
                )
                ->where('fecha', '>=', $init . " 00:00:00")
                ->where('fecha', '<=', $end . " 23:59:59")
                ->orderBy('Salidas.id', 'desc')
                ->get();
            if (count($data) > 0) {
                foreach ($data as $row) {
                    $row->list = DB::table('SalidasMateriales')
                        ->where('Salidas_id', $row->id)
                        ->join('Materiales', 'Materiales.id', '=', 'SalidasMateriales.Materiales_id')
                        ->select('Materiales.material', 'SalidasMateriales.cantidad')
                        ->get();
                }
            }
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }

    public function listOrden($init, $end)
    {
        try {
            $data = DB::table('OrdenesCompra')
                ->join('Empresas', 'OrdenesCompra.Empresas_id', '=', 'Empresas.id')
                ->join('Monedas', 'OrdenesCompra.Monedas_id', '=', 'Monedas.id')
                ->select(
                    'OrdenesCompra.*',
                    DB::raw('CONCAT("OC-", LPAD(OrdenesCompra.id, 6, "0")) as code'),
                    DB::raw('CONCAT("RE-", LPAD(OrdenesCompra.Requerimiento_id, 6, "0")) as codeReq'),
                    'Empresas.razonSocial as empresa',
                    'Monedas.moneda as moneda',
                )
                ->where('fecha', '>=', $init)
                ->where('fecha', '<=', $end)
                ->orderBy('OrdenesCompra.id', 'desc')
                ->get();
            if (count($data) > 0) {
                foreach ($data as $key => $row) {
                    $row->list = DB::table('OrdenesCompraMateriales')
                        ->where('OrdenesCompra_id', $row->id)
                        ->join('Materiales', 'Materiales.id', '=', 'OrdenesCompraMateriales.Materiales_id')
                        ->select('Materiales.material', 'OrdenesCompraMateriales.*')
                        ->get();
                    $row->cantidadEntrada = DB::table('Entradas as e')
                        ->join('EntradasMateriales as em', 'em.Entradas_id', '=', 'e.id')
                        ->where('e.OrdenCompra_id', '=',  $row->id)
                        ->sum('em.cantidad');


                    $row->cantidadTotal = 0;
                    if (count($row->list) > 0) {
                        foreach ($row->list as $key2 => $li) {
                            $row->cantidadTotal += $li->cantidad;
                        }
                    }
                }
            }
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function listInputs()
    {
        try {
            $data = DB::table('Requerimientos')
                ->join('Personas', 'Requerimientos.persona_id', '=', 'Personas.id')
                ->select(
                    'Requerimientos.*',
                    'Personas.nombres as nombre',
                    'Personas.apePaterno as paterno',
                    'Personas.apeMaterno as materno',
                )
                ->get();
            if (count($data) > 0) {
                foreach ($data as $key => $row) {
                    $row->list = DB::table('EntradasMateriales')
                        ->where('Entradas_id', $row->id)
                        ->join('Materiales', 'Materiales.id', '=', 'EntradasMateriales.Materiales_id')
                        ->select('Materiales.material', 'EntradasMateriales.*')
                        ->get();
                }
            }
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function obtenerEntradas($init, $end)
    {
        try {
            $data = DB::table('Entradas as e')
                ->where('fecha', '>=', $init . " 00:00:00")
                ->where('fecha', '<=', $end . " 23:59:59")
                ->select('e.*', DB::raw('CONCAT("EN-", LPAD(e.id, 6, "0")) as code'),)
                ->selectRaw('(SELECT em.razonSocial FROM Empresas AS em WHERE em.id = e.Proveedores_id LIMIT 1) AS proveedor')
                ->selectRaw('(SELECT em.razonSocial FROM Empresas AS em WHERE em.id = e.Transportistas_id LIMIT 1) AS transportista')
                ->selectRaw('CASE
                                WHEN e.Monedas_id = 1 THEN "SOLES"
                                WHEN e.Monedas_id = 2 THEN "DOLARES"
                                ELSE NULL
                            END AS moneda')
                ->orderBy('e.id', 'desc')
                ->get();
            if (count($data) > 0) {
                foreach ($data as $key => $row) {
                    $row->list = DB::table('EntradasMateriales')
                        ->where('Entradas_id', $row->id)
                        ->join('Materiales', 'Materiales.id', '=', 'EntradasMateriales.Materiales_id')
                        ->select('Materiales.material', 'EntradasMateriales.*')
                        ->get();
                }
            }
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
}
