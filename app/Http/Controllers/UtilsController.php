<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UtilsController extends Controller
{
    public function listCategorias()
    {
        try {
            $data = DB::table('Categorias')->where('estado', 1)->orderBy('categoria', 'asc')->get();
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveCategorias(Request $request)
    {
        try {
            if ($request->id == 0) {
                $qr = DB::table('Categorias')->insertGetId(['categoria' => $request->nombre, 'estado' => 1]);
            } else {
                DB::table('Categorias')
                    ->where('id', $request->id)
                    ->update(['categoria' => $request->nombre, 'estado' => 1]);
                $qr = $request->id;
            }

            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }

    public function editCategorias(Request $request)
    {
        try {
            $data = ['categoria' => $request->nombre, 'estado' => $request->estado];
            $qr = DB::table('Categorias')->where('id', $request->id)->update($data);
            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function listMarcas()
    {
        try {
            $data = DB::table('Marcas')->where('estado', 1)->orderBy('marca', 'asc')->get();
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveMarcas(Request $request)
    {
        try {
            if ($request->id == 0) {
                $qr = DB::table('Marcas')->insertGetId(['marca' => $request->nombre, 'estado' => 1]);
            } else {
                DB::table('Marcas')
                    ->where('id', $request->id)
                    ->update(['marca' => $request->nombre, 'estado' => 1]);
                $qr = $request->id;
            }
            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }

    public function editMarcas(Request $request)
    {
        try {
            $data = ['marca' => $request->nombre, 'estado' => $request->estado];
            $qr = DB::table('Marcas')->where('id', $request->id)->update($data);
            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }

    public function listMateriales()
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
                    DB::raw('(COALESCE((SELECT SUM(em.cantidad) FROM EntradasMateriales as em WHERE em.Materiales_id = Materiales.id), 0) - COALESCE((SELECT SUM(em.cantidad) FROM SalidasMateriales as em WHERE em.Materiales_id = Materiales.id), 0)) as stock'),
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

    public function oneMaterial($id)
    {
        try {
            $material = DB::table('Materiales')->where('id', $id)->first();
            $response = ['status' => true, 'data' => $material];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function onePersonal($id)
    {
        try {
            $data = DB::table('Personas')->where('id', $id)->first();
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function savePersonal(Request $request)
    {
        try {
            $data = [
                'TiposDocsIdentificacion_id' => $request->TiposDocsIdentificacion_id,
                'apeMaterno' => $request->apeMaterno,
                'apePaterno' => $request->apePaterno,
                'cargo' => $request->cargo ?? null,
                'correo' => $request->correo ?? null,
                'direccion' => $request->direccion ?? null,
                'fechaNacimiento' => $request->fechaNacimiento,
                'nombres' => $request->nombres,
                'numDocIdentificacion' => $request->numDocIdentificacion,
                'telefono' => $request->telefono,
                'urlImagen' => $request->urlImagen,
            ];

            if ($request->id) {
                $qr = DB::table('Personas')->where('id', $request->id)->update($data);
            } else {
                $qr = DB::table('Personas')->insertGetId($data);
            }
            $response = ['status' => true, 'mensaje' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function deletePersonal($id)
    {
        try {
            $data = ['estado' => 0];
            $qr = DB::table('Personas')->where('id', $id)->update($data);
            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function getMaterialDisponible($id)
    {
        try {
            $total = 0;
            $add = DB::table('EntradasMateriales')
                ->join('Entradas', 'Entradas.id', 'EntradasMateriales.Entradas_id')
                ->where('EntradasMateriales.Materiales_id', $id)
                ->where('Entradas.estado', 1)
                ->sum('EntradasMateriales.cantidad');
            $minus = DB::table('SalidasMateriales')
                ->join('Salidas', 'Salidas.id', 'SalidasMateriales.Salidas_id')
                ->where('SalidasMateriales.Materiales_id', $id)
                ->where('Salidas.estado', 1)
                ->sum('SalidasMateriales.cantidad');
            $response = ['status' => true, 'total' => ($add - $minus), 'add' => $add, 'minus' => $minus];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function deleteMaterial($id)
    {
        try {
            $data = ['estado' => 0];
            $qr = DB::table('Materiales')->where('id', $id)->update($data);
            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function listServicios()
    {
        try {
            $materiales = DB::table('Materiales')
                ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
                ->join('Categorias', 'Materiales.Categorias_id', '=', 'Categorias.id')
                ->select(
                    'Materiales.*',
                    'Unidades.unidad as unidad_nombre',
                    'Categorias.categoria as categoria_nombre'
                )
                ->where('Materiales.Unidades_id', '16')
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
    public function utils()
    {
        try {
            $data = [
                'marcas' => DB::table('Marcas')->where('estado', 1)->orderBy('marca', 'asc')->get(),
                'categorias' => DB::table('Categorias')->where('estado', 1)->orderBy('categoria', 'asc')->get(),
                'unidades' => DB::table('Unidades')->where('estado', 1)->orderBy('unidad', 'asc')->get(),
            ];

            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveMaterial(Request $request)
    {
        try {
            $data = [
                'material' => $request->nombre,
                'stock' => 0,
                'stockMinimo' => 0,
                'codBarras' => $request->codigo ?? null,
                'urlFichaTecnica' => $request->ficha ?? null,
                'urlImagen' => $request->imagen ?? null,
                'Marcas_id' => $request->marca,
                'Unidades_id' => $request->unidad,
                'Categorias_id' => $request->categoria,
            ];

            if ($request->id) {
                DB::table('Materiales')->where('id', $request->id)->update($data);
            } else {
                $qr = DB::table('Materiales')->insertGetId($data);
            }

            $response = ['status' => true, 'data' => $qr ?? null];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }

        return response()->json($response, $codeResponse);
    }

    public function getPersons()
    {
        try {
            $data = DB::table('Personas')->orderBy('apePaterno', 'asc')->where('estado', 1)->get();
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function getProviders()
    {
        try {
            $data = DB::table('Empresas')->where('estado', 1)->get();
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function oneProviders($id)
    {
        try {
            $data = DB::table('Empresas')->where('id', $id)->first();
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveProviders(Request $request)
    {
        try {
            $data = [
                'razonSocial' => $request->razonSocial,
                'direccionFiscal' => $request->direccionFiscal ?? null,
                'numDocIdentificacion' => $request->numDocIdentificacion ?? null,
                'actEconomica' => $request->actEconomica ?? null,
                'representanteLegal' => $request->representanteLegal ?? null,
                'correo' => $request->correo ?? null,
                'telefono' => $request->telefono ?? null,
                'esTransportista' => 0,
                'TiposDocsIdentificacion_id' => 1,
            ];

            // Verifica si se proporciona un ID válido
            if ($request->has('id') && $request->id != 0) {
                // Si hay un ID válido, actualizamos el registro existente
                DB::table('Empresas')->where('id', $request->id)->update($data);
                $qr = $request->id;
            } else {
                // Si no hay un ID válido, insertamos un nuevo registro
                $qr = DB::table('Empresas')->insertGetId($data);
            }

            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }

        return response()->json($response, $codeResponse);
    }

    public function deleteProvider($id)
    {
        try {
            $data = ['estado' => 0];
            $qr = DB::table('Empresas')->where('id', $id)->update($data);
            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveTransports(Request $request)
    {
        try {
            $data = [
                'razonSocial' => $request->razonSocial,
                'direccionFiscal' => $request->direccionFiscal ?? null,
                'numDocIdentificacion' => $request->numDocIdentificacion ?? null,
                'actEconomica' => $request->actEconomica ?? null,
                'representanteLegal' => $request->representanteLegal ?? null,
                'correo' => $request->correo ?? null,
                'telefono' => $request->telefono ?? null,
                'esTransportista' => 1,
                'TiposDocsIdentificacion_id' => 1,
            ];

            // Verifica si se proporciona un ID válido
            if ($request->has('id') && $request->id != 0) {
                // Si hay un ID válido, actualizamos el registro existente
                DB::table('Empresas')->where('id', $request->id)->update($data);
                $qr = $request->id;
            } else {
                // Si no hay un ID válido, insertamos un nuevo registro
                $qr = DB::table('Empresas')->insertGetId($data);
            }

            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }

        return response()->json($response, $codeResponse);
    }

    public function getCCostos()
    {
        try {
            $data = DB::table('CCostosPrimarios')->where('estado', 1)->orderBy('centroCosto', 'asc')->get();
            $response = ['status' => true, 'data' => $data];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function saveCCostos(Request $request)
    {
        try {
            if ($request->id == 0) {
                $qr = DB::table('CCostosPrimarios')->insertGetId([
                    'centroCosto' => $request->centroCostos,
                    'estado' => 1,
                ]);
            } else {
                DB::table('CCostosPrimarios')
                    ->where('id', $request->id)
                    ->update([
                        'centroCosto' => $request->centroCostos,
                        'estado' => 1,
                    ]);
                $qr = $request->id;
            }

            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }

    public function deleteCCostos(Request $request)
    {
        try {
            $data = ['estado' => $request->estado];
            $qr = DB::table('CCostosPrimarios')->where('id', $request->id)->update($data);
            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function getTransport()
    {
        try {
            $result = [
                "transport" => [],
                "business" => []  // Cambiado "bussiness" a "business" para corregir la falta de ortografía
            ];

            $data = DB::table('Empresas')->get();

            foreach ($data as $value) {
                if ($value->esTransportista == 1) {
                    array_push($result["transport"], $value);
                } else {
                    array_push($result["business"], $value);  // Cambiado "transport" a "business" para separar transportistas de negocios
                }
            }

            $response = ['status' => true, 'data' => $result];  // Devuelve el arreglo $result en lugar de $data
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];  // Cambiado 'true' a 'false' para reflejar el estado de error
            $codeResponse = 500;
        }

        return response()->json($response, $codeResponse);
    }
    public function getTransports()
    {
        try {
            $data = DB::table('Empresas')->where('esTransportista', 1)->where('estado', 1)->get();
            $response = ['status' => true, 'data' => $data];  // Devuelve el arreglo $result en lugar de $data
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];  // Cambiado 'true' a 'false' para reflejar el estado de error
            $codeResponse = 500;
        }

        return response()->json($response, $codeResponse);
    }

    public function getOrderOne($id)
    {
        try {
            $info = DB::table('OrdenesCompra')
                ->join('Empresas', 'OrdenesCompra.Empresas_id', '=', 'Empresas.id')
                ->select('OrdenesCompra.*', 'Empresas.razonSocial as empresa')
                ->where('OrdenesCompra.id', $id)
                ->first();

            $data = DB::table('OrdenesCompraMateriales')
                ->join('Materiales', 'OrdenesCompraMateriales.Materiales_id', '=', 'Materiales.id')
                ->join('Unidades', 'Materiales.Unidades_id', '=', 'Unidades.id')
                ->select('OrdenesCompraMateriales.*', 'Materiales.material as material', 'Unidades.unidad')
                ->where('OrdenesCompraMateriales.OrdenesCompra_id', $id)
                ->get();

            $response = ['status' => true, 'data' => $data, 'info' => $info];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function listInput(Request $request)
    {
        try {
            $qr = DB::table('Materiales')->insertGetId([
                'material' => $request->nombre,
                'stock' => 0,
                'stockMinimo' => 0,
                'codBarras' => $request->codigo ?? null,
                'urlFichaTecnica' => $request->ficha ?? null,
                'urlImagen' => $request->imagen ?? null,
                'Marcas_id' => $request->marca,
                'Unidades_id' => $request->unidad,
                'Categorias_id' => $request->categoria,
            ]);
            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function dashboard()
    {
        try {

            $panel1 = DB::select("call panel1()");;
            $panel2 = DB::select("call panel2()");;
            $panel3 = DB::table('Entradas')->where('estado', 1)->count();
            $panel4 = DB::table('Materiales')->where('estado', 1)->where('Materiales.Unidades_id', '<>', '16')->count();
            $inversion = DB::select("call circle()");
            $invetario = DB::select("call bar()");

            $response = [
                'status' => true,
                'panel1' => $panel1[0]->total,
                'panel2' => $panel2[0]->total,
                'panel3' => $panel3,
                'panel4' => $panel4,
                'invesion' => $inversion,
                'invetario' => $invetario,
            ];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
}
