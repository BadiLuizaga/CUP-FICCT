<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Carrera;
use App\Models\Postulante;

class DashboardController
{
    public function index()
    {
        \require_login();

        try {
            $totalUsuarios = Usuario::contar();
            $usuariosActivos = Usuario::contarActivos();
            $totalRoles = Rol::contar();
            $totalCarreras = Carrera::contar();
            $totalCupos = Carrera::sumarCupos();
            $totalPostulantes = Postulante::contar();

            \view('dashboard.index', [
                'titulo' => 'Dashboard - SIGIE',
                'totalUsuarios' => $totalUsuarios,
                'usuariosActivos' => $usuariosActivos,
                'totalRoles' => $totalRoles,
                'totalCarreras' => $totalCarreras,
                'totalCupos' => $totalCupos,
                'totalPostulantes' => $totalPostulantes,
            ]);
        } catch (\Throwable $e) {
            \set_flash('error', 'Error al cargar el dashboard: ' . $e->getMessage());

            \view('dashboard.index', [
                'titulo' => 'Dashboard - SIGIE',
                'totalUsuarios' => 0,
                'usuariosActivos' => 0,
                'totalRoles' => 0,
                'totalCarreras' => 0,
                'totalCupos' => 0,
                'totalPostulantes' => 0,
            ]);
        }
    }
}