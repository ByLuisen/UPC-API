<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios ordenados por partidas ganadas.
     *
     * @return \Illuminate\View\View
     */
    public function list()
    {
        $rankings = User::all()->sortByDesc('partidas_ganadas');
        return ApiResponse::success(UserResource::collection($rankings));

        // return response()->json([
        //     'data' => $rankings,
        // ], 200);
    }

    public function list_users()
    {
        try {
            $usuarios = User::role('user')->get();

            // Devolver una respuesta JSON con los usuarios listados
            return ApiResponse::success(UserResource::collection($usuarios));
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener la lista de usuarios');
        }
    }

    /**
     * Busca un usuario por su nombre.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function search_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'exists:users,name'],
        ], [
            'name.required' => 'El campo jugador es obligatorio',
            'name.exists' => 'El jugador no existe',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $name = $request->input('name');

        $rankings = User::where('name', $name)->get();

        // Devolver una respuesta JSON con los usuarios encontrados
        return ApiResponse::success(UserResource::collection($rankings));
    }

    /**
     * Actualiza el nombre del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateName(Request $request, User $usuario)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        // Verificar si la validación ha fallado
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        // Actualizar el nombre del usuario
        $usuario->update([
            'name' => $request->input('name'),
            'password' => $request->input('name'), // Se actualiza también la contraseña por simplicidad (no recomendado en producción)
        ]);

        // Devolver una respuesta JSON con un mensaje de éxito
        return ApiResponse::success(['message' => 'Nombre actualizado correctamente']);
    }

    /**
     * Actualiza el correo electrónico del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEmail(Request $request, User $usuario)
    {
        // Validar los datos del formulario
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
        ]);

        // Verificar si la validación ha fallado
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        // Actualizar el correo electrónico del usuario
        $usuario->update([
            'email' => $request->input('email'),
        ]);

        // Devolver una respuesta JSON con un mensaje de éxito
        return ApiResponse::success(['message' => 'Correo electrónico actualizado correctamente']);
    }

    /**
     * Elimina la cuenta del usuario y cierra sesión.
     *
     * @param  \App\Models\User  $usuario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function eliminarCuenta(User $usuario)
    {
        try {
            // Llamar al método delete() para eliminar el usuario
            $usuario->delete();

            // Desconectar al usuario autenticado
            Auth::logout();

            // Devolver una respuesta JSON con un mensaje de éxito
            return ApiResponse::success(['message' => 'Cuenta eliminada correctamente']);
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al eliminar la cuenta');
        }
    }

    public function eliminarUsuario(User $usuario)
    {
        try {
            // Llamar al método delete() para eliminar el usuario
            $usuario->delete();

            // Devolver una respuesta JSON con un mensaje de éxito
            return ApiResponse::success(['message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al eliminar el usuario');
        }
    }

    /**
     * Actualiza la información de un usuario en la base de datos.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP entrante.
     * @param \App\Models\User $usuario El usuario a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige al usuario a una ruta específica después de la actualización.
     */
    public function actualizarUsuario(Request $request, User $usuario)
    {
        try {
            // Validar los datos del formulario
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
                'password' => 'required|max:255'
            ]);

            // Verificar si la validación ha fallado
            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            // Se actualiza el usuario con la información proporcionada en la solicitud.
            $usuario->update([
                'email' => $request->input('email'),
                'name' => $request->input('name'),
                'password' => $request->input('password'),
            ]);

            // Devolver una respuesta JSON con un mensaje de éxito
            return ApiResponse::success(['message' => 'Usuario actualizado correctamente']);
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al actualizar el usuario');
        }
    }

    public function buscar_usuario(Request $request)
    {
        try {
            // Validar los datos del formulario
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'exists:users,name'],
            ], [
                'name.required' => 'El campo jugador es obligatorio',
                'name.exists' => 'El jugador no existe',
            ]);

            // Verificar si la validación ha fallado
            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            $name = $request->input('name');

            $usuario = User::where('name', $name)->get();

            // Devolver una respuesta JSON con los usuarios encontrados
            return ApiResponse::success(UserResource::collection($usuario));
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al buscar el usuario');
        }
    }
}
