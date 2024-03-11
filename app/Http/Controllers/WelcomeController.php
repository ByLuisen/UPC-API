<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Http\Responses\ApiResponse;
use App\Models\Carta;
use App\Models\Member;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        try {
            $cartas = Carta::pluck('photo');
            $miembros = Member::all();

            // Devolver una respuesta JSON con las cartas y los miembros
            return ApiResponse::success(['cartas' => $cartas, MemberResource::collection($miembros)]);
        } catch (\Exception $e) {
            // Loguear el error o realizar otras acciones según tus necesidades
            return ApiResponse::error('Error al obtener datos para la página de inicio');
        }
    }
}
