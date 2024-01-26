<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use \Firebase\JWT\JWT;

class LoginController extends Controller
{
    public function signIn(Request $request)
    {
        $data = DB::table('Usuarios')->where('usuario', $request->username)->get();
        if (count($data) > 0) {
            $pass = hash('sha256', $request->password);
            if ($data[0]->password == $pass && $data[0]->estado == 1) {
                $persona = DB::table('Personas')->where('id', $data[0]->id)->first();
                $data[0]->person = $persona;

                $data[0]->password = "******";
                $payload = [
                    'iss' => $request->username, // Emisor del token
                    'sub' => 'subjet', // Sujeto del token
                    'iat' => time(), // Tiempo de emisión del token
                    'exp' => time() + 60 * 60 * 12 // Tiempo de expiración del token (1 hora)
                ];

                $secretKey = 'tu-clave-secreta';

                $token = JWT::encode($payload, $secretKey, 'HS256');
                $response = ['status' => true, 'user' => $data[0], 'token' =>  $token, "expirationTime" => time() + 60 * 60 * 12];
                $code = 200;
            } else {
                $response = ['status' => false, 'res' => 'La constraseña ingresada es incorrecta'];
                $code = 400;
            }
        } else {
            $response = ['status' => false, 'res' => 'El usuario ingresado no existe'];
            $code = 400;
        }
        return response()->json($response, $code);
    }
    public function createUser(Request $request)
    {
        $password = hash('sha256', $request->password);
        $user = [
            'Persona_id' => $request->person_id,
            'Roles_id' => $request->rol_id,
            'usuario' => $request->user,
            'password' => $password,
            'estado' => $request->state
        ];
        try {
            DB::beginTransaction();
            $result = DB::table('Usuarios')->insertGetId($user);
            DB::commit();
            $response = ['status' => true, 'data' => $result];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function editUser(Request $request)
    {
        $user = [
            'id' => $request->id,
            'usuario' => $request->usuario,
            'estado' => $request->estado
        ];
        try {
            DB::beginTransaction();
            $result = DB::table('Usuarios')->where('id', $request->id)->update($user);
            DB::commit();
            $response = ['status' => true, 'data' => $result];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function listUsers()
    {
        try {
            $data = DB::table('Usuarios')
                ->join('Roles', 'Roles.id', 'Usuarios.Roles_id')
                ->join('Personas', 'Personas.id', 'Usuarios.persona_id')
                ->where('Usuarios.estado', 1)
                ->select('Personas.*', 'Usuarios.*', 'Roles.rol')
                ->get();
            $response = ['status' => true, 'data' => $data];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['status' => false, 'res' => 'La constraseña ingresada es incorrecta'];
            $code = 400;
        }
        return response()->json($response, $code);
    }
    public function listRoles()
    {
        try {
            $data = DB::table('Roles')->get();
            $response = ['status' => true, 'data' => $data];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['status' => false, 'res' => 'La constraseña ingresada es incorrecta'];
            $code = 400;
        }
        return response()->json($response, $code);
    }
    public function saveRoles(Request $request)
    {
        try {
            if ($request->id == 0) {
                $qr = DB::table('Roles')
                    ->insert([
                        'rol' => $request->rol,
                        'estado' => 1
                    ]);
            } else {
                $qr = DB::table('Roles')
                    ->where('id', $request->id)
                    ->update([
                        'rol' => $request->rol,
                        'estado' => $request->estado,
                        'permisos' => $request->permisos
                    ]);
            }

            $response = ['status' => true, 'data' => $qr];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => false, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }

        return response()->json($response, $codeResponse);
    }

    public function listUtils()
    {
        try {
            $personal = DB::table('Personas')->get();
            $roles = DB::table('Roles')->get();
            $response = ['status' => true, 'personal' => $personal, 'roles' => $roles];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['status' => false, 'res' => 'La constraseña ingresada es incorrecta'];
            $code = 400;
        }
        return response()->json($response, $code);
    }
    public function generateUserConduc(Request $request)
    {
        $pass = hash('sha256', $request->user);
        $user = [
            'nombre' => $request->nombre,
            'user' => $request->user,
            'rang' => $request->rang,
            'password' => $pass,
            'estado' => 1
        ];
        try {
            DB::beginTransaction();
            $userId = DB::table('users')->insertGetId($user);
            $ret = DB::table('personal')->where('id', $request->personal_id)->update(['user' => $userId]);
            DB::commit();
            $response = ['status' => true, 'data' => $ret];
            $codeResponse = 200;
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
    public function changePassword(Request $request)
    {
        try {
            $pass = hash('sha256', $request->one);
            $respuesta = DB::table('users')->where('id', $request->user_id)->update(['password' => $pass]);
            $response = ['status' => true, 'data' => $respuesta];
            $codeResponse = 200;
        } catch (\Exception $e) {
            $response = ['status' => true, 'mensaje' => $e->getMessage(), 'code' => $e->getCode()];
            $codeResponse = 500;
        }
        return response()->json($response, $codeResponse);
    }
}
