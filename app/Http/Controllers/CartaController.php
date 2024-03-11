<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Carta;
use Illuminate\Validation\Rule;
use App\Http\Responses\ApiResponse;
use App\Http\Resources\CartaResource;
use Illuminate\Support\Facades\Validator;

class CartaController extends Controller
{
    public function __construct()
    {
        // Middleware para permitir el acceso a solo los usuarios autorizados y con role admin
        $this->middleware(['auth:api', 'role:admin']);
    }

    /**
     * Función que devolverá una vista donde aparecerán todas las cartas del juego
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // Obtengo todas las cartas
            $cartas = Carta::all();
            // Devuelvo una respuesta JSON con las cartas
            return ApiResponse::success(CartaResource::collection($cartas), 'Lista de cartas obtenida correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener la lista de cartas');
        }
    }

    /**
     * Añade la carta a la bbdd.
     *
     * @return \Illuminate\View\View
     */
    public function crear_carta(Request $request)
    {
        try {
            // Obtiene el archivo
            // $photoFile = $request->file('photo');

            // Realiza la validación
            $validator = Validator::make($request->all(), [
                'photo' => [
                    'required',
                    'string',
                    // 'image',
                    // 'mimes:jpeg,png,webp',
                    Rule::unique('cartas', 'photo'),
                ],
                'nombre' => ['required', 'string', 'max:40', Rule::unique('cartas', 'nombre')],
                'role' => ['required', 'string'],
                'coste_elixir' => ['required', 'min:0', 'max:10'],
            ], [
                'photo.required' => 'Por favor selecciona una foto para la carta.',
                'photo.image' => 'El archivo debe ser una imagen.',
                'photo.mimes' => 'La foto debe ser de tipo JPG, PNG o WebP.',
                'photo.unique' => 'La nueva imagen debe ser diferente a la actual.',
                // Resto de las reglas de validación...
            ]);

            // Si la validación falla, devuelve los errores en formato JSON
            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            // // Almacena la imagen en public/images con el nombre original
            // $photoFile->move(public_path('images'), $photoFile->getClientOriginalName());

            // Actualiza la carta con los datos nuevos
            $carta = new Carta();
            $carta->photo = $request->photo;
            // $carta->photo = $photoFile->getClientOriginalName();
            $carta->nombre = $request->nombre;
            $carta->role = $request->role;
            $carta->coste_elixir = $request->coste_elixir;

            // Guarda los cambios en la carta
            $carta->save();

            // Devuelve una respuesta JSON indicando éxito
            return ApiResponse::success(null, 'Carta creada correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al crear la carta');
        }
    }

    /**
     * Muestra formulario para editar carta que se selecciona en concreto.
     *
     * @return \Illuminate\View\View
     */
    public function edit(Carta $carta)
    {
        try {
            // Transformo la carta en un recurso para el formato JSON
            $cartaResource = new CartaResource($carta);

            // Devuelvo una respuesta JSON con la carta
            return ApiResponse::success($cartaResource, 'Carta obtenida para edición correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener la carta para edición');
        }
    }

    /**
     * Actualiza los datos de la carta en la base de datos
     *
     * @return \Illuminate\View\View
     */
    public function actualizar_carta(Request $request, Carta $carta)
    {
        try {
            // Obtiene el archivo
            $photoFile = $request->file('photo');

            // Realiza la validación
            $validator = Validator::make($request->all(), [
                'photo' => [
                    'required',
                    'string',
                    'nullable',
                    // 'image',
                    // 'mimes:jpeg,png,webp',
                    Rule::unique('cartas', 'photo')->ignore($carta->id),
                ],
                'nombre' => ['required', 'string', 'max:40', Rule::unique('cartas', 'nombre')->ignore($carta->id)],
                'role' => ['required', 'string'],
                'coste_elixir' => ['required', 'min:0', 'max:10'],
            ], [
                'photo.required' => 'Por favor selecciona una foto para la carta.',
                'photo.image' => 'El archivo debe ser una imagen.',
                'photo.mimes' => 'La foto debe ser de tipo JPG, PNG o WebP.',
                'photo.unique' => 'La nueva imagen debe ser diferente a la actual.',
                // Resto de las reglas de validación...
            ]);

            // Si la validación falla, devuelve los errores en formato JSON
            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors());
            }

            // // Almacena la imagen en public/images con el nombre original
            // $photoFile->move(public_path('images'), $photoFile->getClientOriginalName());

            // Actualiza la carta con los datos nuevos
            // $carta->photo = $photoFile->getClientOriginalName();
            $carta->photo = $request->photo;
            $carta->nombre = $request->nombre;
            $carta->role = $request->role;
            $carta->coste_elixir = $request->coste_elixir;

            // Guarda los cambios en la carta
            $carta->save();

            // Devuelve una respuesta JSON indicando éxito
            return ApiResponse::success(null, 'Carta actualizada correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al actualizar la carta');
        }
    }


    /**
     * Añade los datos actualizados de la carta a la base de datos
     *
     * @return \Illuminate\View\View
     */
    public function eliminar_carta(Carta $carta)
    {
        try {
            // Elimino la carta que se ha indicado
            $carta->delete();

            // Devuelvo una respuesta JSON indicando éxito
            return ApiResponse::success(null, 'Carta eliminada correctamente');
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al eliminar la carta');
        }
    }
}
