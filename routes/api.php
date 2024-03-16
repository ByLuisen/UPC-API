<?php

use App\Http\Controllers\CartaController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ruta para obtener información del usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Grupo de rutas para autenticacion
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'App\Http\Controllers\AuthController@login');
    Route::post('register', 'App\Http\Controllers\AuthController@register');
    Route::get('logout', 'App\Http\Controllers\AuthController@logout');
    Route::post('refresh', 'App\Http\Controllers\AuthController@refresh');
    Route::post('perfil', 'App\Http\Controllers\AuthController@perfil');
});

// Grupo de rutas para controladores con middleware de autenticacion y namespace de controladores
Route::group(['middleware' => 'api', 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('/', [WelcomeController::class, 'index']);

    // RUTAS PARA MANEJAR RANKINGS
    // Listar el ranking de usuarios
    Route::get('rankings', [UserController::class, 'list']);
    // Muestra el ranking de un usuario en concreto
    Route::post('rankings', [UserController::class, 'search_user']);

    // RUTAS PARA MANEJAR USUARIOS
    // Ruta para actualizar el nombre de usuario4156+
    Route::put('/usuarios/{usuario}/actualizar-nombre', [UserController::class, 'updateName'])->middleware('auth:api');
    // Ruta para actualizar el correo electrónico del usuario
    Route::put('/usuarios/{usuario}/actualizar-correo', [UserController::class, 'updateEmail'])->middleware('auth:api');
    // Ruta para eliminar un usuario
    Route::delete('/usuarios/{usuario}/eliminar-cuenta', [UserController::class, 'eliminarCuenta'])->middleware('auth:api');
    // Ruta para listar todos los usuarios solamente, accesible para usuarios autorizados y con rol admin
    Route::get('/usuarios', [UserController::class, 'list_users'])->middleware(['auth:api', 'role:admin']);
    // Ruta para eliminar los usuarios solamente, accesible para usuarios autorizados y con rol admin
    Route::delete('/usuarios/{usuario}/eliminar-usuario', [UserController::class, 'eliminarUsuario'])->middleware(['auth:api', 'role:admin']);
    // Ruta para editar los usuarios solamente, accesible para usuarios autorizados y con rol admin
    Route::put('/usuarios/{usuario}/actualizar-usuario', [UserController::class, 'actualizarUsuario'])->middleware(['auth:api', 'role:admin']);
    // Ruta para buscar usuarios, accesible para usuarios autorizados y con rol admin
    Route::post('/usuarios', [UserController::class, 'buscar_usuario'])->middleware(['auth:api', 'role:admin']);

    // RUTA PARA MANEJAR EVENTOS
    // Ruta para mostrar los eventos del usuario autenticado
    Route::get('/mis_eventos', [EventoController::class, 'mis_eventos']);
    Route::get('/eventos_inscritos', [EventoController::class, 'eventos_inscritos']);
    // Ruta para mostrar todos los eventos y crear nuevos eventos
    Route::get('/eventos', [EventoController::class, 'index']);
    // Ruta para guardar un nuevo evento
    Route::post('/crear_evento', [EventoController::class, 'crear_evento']);
    // Ruta para eliminar un evento existente
    Route::delete('/eventos/{evento}/delete', [EventoController::class, 'destroy']);
    // Ruta para desuscribirse eventos
    Route::delete('/eventos/{evento}/desuscribirse', [EventoController::class, 'desuscribirse']);
    // Ruta para inscribirse a eventos
    Route::post('/eventos/{evento}/inscribirse', [EventoController::class, 'inscribirse']);
    // Ruta para editar un evento existente
    Route::get('/eventos/{evento}/edit', [EventoController::class, 'edit']);
    // Ruta para actualizar un evento existente
    Route::post('/eventos/{evento}/edit', [EventoController::class, 'actualizar_evento']);

    // RUTAS PARA MANEJAR CARTAS
    // Ruta para listar todas las cartas
    Route::get('/cartas', [CartaController::class, 'index']);
    // Ruta para añadir la carta a la bbdd
    Route::post('/crear_carta', [CartaController::class, 'crear_carta']);
    // Ruta para editar la carta seleccionada
    Route::get('/carta/{carta}/edit', [CartaController::class, 'edit']);
    // Ruta para actualizar un evento
    Route::post('/carta/{carta}/edit', [CartaController::class, 'actualizar_carta']);
    // Ruta para eliminar una carta
    Route::delete('/carta/{carta}/delete', [CartaController::class, 'eliminar_carta']);

    // RUTAS PARA MANEJAR MIEMBROS
    // Ruta para listar todos los miembros solamente, accesible para usuarios autorizados y con rol admin
    Route::get('/miembros', [MemberController::class, 'list']);
    // Ruta para eliminar los miembros solamente, accesible para usuarios autorizados y con rol admin
    Route::delete('/miembros/{miembro}/eliminar-miembro', [MemberController::class, 'eliminarMiembro']);
    // Ruta para editar los miembros solamente, accesible para usuarios autorizados y con rol admin
    Route::put('/miembros/{miembro}/actualizar-miembro', [MemberController::class, 'actualizarMiembro']);
});
