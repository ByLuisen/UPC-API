<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api', 'role:admin']);
    }

    public function list()
    {

        try {
            $miembros = MemberResource::collection(Member::all());
            // Devolver una respuesta JSON con los miembros
            return ApiResponse::success(['miembros' => $miembros]);
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener la lista de miembros');
        }
    }


    public function eliminarMiembro(Member $miembro)
    {
        try {
            // Llamar al método delete() para eliminar el miembro
            $miembro->delete();

            // Devolver una respuesta JSON indicando que se eliminó el miembro
            return ApiResponse::success([], 'Miembro eliminado correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al eliminar el miembro');
        }
    }

    /**
     * Actualiza la información de un miembro en la base de datos.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP entrante.
     * @param \App\Models\Member $miembro El miembro a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige al usuario después de la actualización.
     */
    public function actualizarMiembro(Request $request, Member $miembro)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'role' => 'required|string|max:255',
                'desc' => 'required|string',
                'photo' => 'required|string',
                'website' => 'required|string|url',
                'email' => [
                    'required',
                    'string',
                    'max:255',
                    'email',
                    Rule::unique('members', 'email')->ignore($miembro->id),
                    Rule::unique('users', 'email')->ignore($miembro->id),
                ],
                'linkedin' => 'nullable|string|url',
                'dribbble' => 'nullable|string|url',
            ], [
                'name.required' => 'El campo nombre es obligatorio',
                'name.string' => 'El campo nombre debe ser una cadena de texto',
                // ... Añade mensajes personalizados para otras reglas y campos
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->first());
            }

            // Se actualiza el miembro con la información proporcionada en la solicitud.
            $miembro->update([
                'name' => $request->input('name'),
                'role' => $request->input('role'),
                'desc' => $request->input('desc'),
                'photo' => $request->input('photo'),
                'website' => $request->input('website'),
                'email' => $request->input('email'),
                'linkedin' => $request->input('linkedin'),
                'dribbble' => $request->input('dribbble'),
            ]);

            // Devolver una respuesta JSON indicando que se actualizó el miembro
            return ApiResponse::success([], 'Miembro actualizado correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al actualizar el miembro');
        }
    }
}
