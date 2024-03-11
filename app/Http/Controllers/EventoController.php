<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventoResource;
use App\Http\Responses\ApiResponse;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class EventoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware de autenticación requerido para acceder a las rutas de este controlador
        $this->middleware('auth:api');
    }

    /**
     * Muestra todos los eventos.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Obtiene todos los eventos
            $eventos = Evento::all();

            // Retorna una respuesta JSON con los eventos
            return ApiResponse::success(EventoResource::collection($eventos), 'Lista de eventos obtenida correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener la lista de eventos');
        }
    }

    /**
     * Muestra los eventos del usuario autenticado.
     *
     * @return \Illuminate\View\View
     */
    public function mis_eventos()
    {
        try {
            // Busca al usuario autenticado
            $eventos = Evento::where('user_id', Auth::id())->get();

            // Retorna una respuesta JSON con los eventos del usuario
            return ApiResponse::success(EventoResource::collection($eventos), 'Eventos del usuario obtenidos correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener los eventos del usuario');
        }
    }

    public function eventos_inscritos()
    {
        try {
            // Busca al usuario autenticado
            $usuario = User::find(Auth::id());

            // Si el usuario existe, obtiene sus eventos
            if ($usuario) {
                $eventos = $usuario->eventos;
                return ApiResponse::success(EventoResource::collection($eventos), 'Eventos inscritos obtenidos correctamente');
            } else {
                // Si el usuario no existe, devuelve un mensaje de error
                return ApiResponse::error('Usuario no encontrado');
            }
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener los eventos inscritos del usuario');
        }
    }

    /**
     * Guarda un nuevo evento creado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function crear_evento(Request $request)
    {
        // Define las reglas de validación
        $rules = [
            'nombre' => ['required', 'string', 'max:40', 'unique:eventos'],
            'tipo' => ['required', 'string'],
            'fecha_inicio' => ['required', 'date_format:d/m/Y H:i'],
            'duracion' => ['required', 'string'],
        ];

        // Define los mensajes de error personalizados
        $messages = [
            'fecha_inicio.date_format' => 'El formato de la fecha de inicio debe ser dd/mm/yyyy HH:mm.',
        ];

        // Crea el validador con las reglas y mensajes personalizados
        $validator = Validator::make($request->all(), $rules, $messages);

        // Verifica si la validación falla
        if ($validator->fails()) {
            // Retorna una respuesta JSON con los errores de validación
            return ApiResponse::validationError($validator->errors()->first());
        }

        try {
            // Crea un nuevo evento con los datos del formulario
            $evento = new Evento();
            $evento->user_id = Auth::user()->id;
            $evento->nombre = $request->nombre;
            $evento->tipo = $request->tipo;
            $evento->fecha_inicio = $request->fecha_inicio;
            $evento->duracion = $request->duracion;

            // Guarda el evento en la base de datos
            $evento->save();

            // Asocia el evento al usuario autenticado
            $evento->usuarios()->attach(Auth::user()->id);

            // Retorna una respuesta JSON indicando que el evento se ha creado correctamente
            return ApiResponse::success(new EventoResource($evento), 'Evento creado correctamente.');
        } catch (\Exception $e) {
            // Loguea el error o realiza otras acciones según tus necesidades
            return ApiResponse::error('Error al crear el evento');
        }
    }

    /**
     * Muestra el formulario para editar un evento existente.
     *
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\View\View
     */
    public function edit(Evento $evento)
    {
        try {
            // Retorna los datos del evento en formato JSON
            return ApiResponse::success(new EventoResource($evento), 'Datos del evento obtenidos correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener los datos del evento');
        }
    }

    /**
     * Actualiza un evento existente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function actualizar_evento(Request $request, Evento $evento)
    {
        // Define las reglas de validación
        $rules = [
            'nombre' => ['required', 'string', 'max:40'],
            'tipo' => ['required', 'string'],
            'fecha_inicio' => ['required', 'date_format:d/m/Y H:i'],
            'duracion' => ['required', 'string'],
        ];

        // Define los mensajes de error personalizados
        $messages = [
            'fecha_inicio.date_format' => 'El formato de la fecha de inicio debe ser dd/mm/yyyy HH:mm.',
        ];

        // Crea el validador con las reglas y mensajes personalizados
        $validator = Validator::make($request->all(), $rules, $messages);

        // Verifica si la validación falla
        if ($validator->fails()) {
            // Retorna una respuesta JSON con los errores de validación
            return ApiResponse::validationError($validator->errors()->first());
        }

        try {
            // Actualiza el evento con los datos del formulario
            $evento->nombre = $request->nombre;
            $evento->tipo = $request->tipo;
            $evento->fecha_inicio = $request->fecha_inicio;
            $evento->duracion = $request->duracion;

            // Guarda los cambios en el evento
            $evento->save();

            // Retorna una respuesta JSON indicando que el evento se ha actualizado correctamente
            return ApiResponse::success(null, 'Evento actualizado correctamente.');
        } catch (\Exception $e) {
            // Loguea el error o realiza otras acciones según tus necesidades
            return ApiResponse::error('Error al actualizar el evento');
        }
    }

    /**
     * Elimina un evento.
     *
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Evento $evento)
    {
        try {
            // Elimina el evento
            $evento->delete();

            // Retorna una respuesta JSON indicando que el evento se eliminó correctamente
            return ApiResponse::success(null, 'Evento eliminado correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al eliminar el evento');
        }
    }

    /**
     * Desuscribe al usuario autenticado del evento especificado.
     *
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function desuscribirse(Evento $evento)
    {
        try {
            // Obtiene al usuario autenticado
            $usuario = Auth::user();

            // Verifica si el usuario está inscrito en el evento
            if ($evento->usuarios()->where('user_id', $usuario->id)->exists()) {
                // Desasocia al usuario del evento
                $evento->usuarios()->detach($usuario->id);

                // Retorna una respuesta JSON indicando que el usuario se ha desuscrito correctamente
                return ApiResponse::success(null, 'Te has desuscrito del evento correctamente.');
            } else {
                // Retorna una respuesta JSON con un mensaje de error si el usuario no está inscrito en el evento
                return ApiResponse::error('No estás inscrito en este evento.');
            }
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al desuscribirse del evento');
        }
    }

    /**
     * Inscribir al usuario autenticado en un evento especificado.
     *
     * @param  \App\Models\Evento  $evento
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inscribirse(Evento $evento)
    {
        try {
            // Obtiene al usuario autenticado
            $usuario = Auth::user();

            // Verifica si el usuario ya está inscrito en el evento
            if ($evento->usuarios()->where('user_id', $usuario->id)->exists()) {
                // Retorna una respuesta JSON con un mensaje de error si el usuario ya está inscrito en el evento
                return ApiResponse::error('Ya estás inscrito en este evento.');
            } else {
                // Asocia al usuario con el evento
                $evento->usuarios()->attach($usuario->id);

                // Retorna una respuesta JSON indicando que el usuario se ha inscrito correctamente
                return ApiResponse::success(null, 'Te has inscrito en el evento correctamente.');
            }
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al inscribirse en el evento');
        }
    }
}
