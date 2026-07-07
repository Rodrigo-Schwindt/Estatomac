<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Parametro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParametroController extends Controller
{
    public function index()
    {
        $parametro      = Parametro::config();
        $sesionesActivas = $this->contarSesionesActivas();
        return view('admin.parametros.index', compact('parametro', 'sesionesActivas'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'email_soporte' => 'nullable|email|max:120',
            'int_fallidos'  => 'nullable|integer|min:1|max:20',
            'delay_error'   => 'nullable|integer|min:30|max:3600',
            'dias_passwd'   => 'nullable|integer|min:0|max:3650',
            'largo_passwd'  => 'nullable|integer|min:4|max:32',
        ]);

        $parametro       = Parametro::config();
        $estabaActivo    = (bool) $parametro->en_mantenimiento;
        $seActiva        = $request->boolean('en_mantenimiento');

        $parametro->en_mantenimiento = $seActiva;
        $parametro->email_soporte    = $request->email_soporte ?: null;
        if ($request->filled('int_fallidos')) {
            $parametro->int_fallidos = (int) $request->int_fallidos;
        }
        if ($request->filled('delay_error')) {
            $parametro->delay_error = (int) $request->delay_error;
        }
        if ($request->filled('dias_passwd')) {
            $parametro->dias_passwd = (int) $request->dias_passwd;
        }
        if ($request->filled('largo_passwd')) {
            $parametro->largo_passwd = (int) $request->largo_passwd;
        }
        $parametro->save();

        cache()->forget('parametros.en_mantenimiento');

        $mensaje = 'Parámetros actualizados.';

        if (!$estabaActivo && $seActiva && $request->boolean('cerrar_sesiones')) {
            $cerradas = $this->cerrarSesionesZona();
            $mensaje .= " Se cerraron {$cerradas} sesión/es activas de la zona privada.";
        }

        return redirect()->route('admin.parametros')->with('success', $mensaje);
    }

    private function contarSesionesActivas(): int
    {
        try {
            $umbral = now()->subMinutes(30)->timestamp;
            return DB::table('sessions')
                ->where('last_activity', '>=', $umbral)
                ->where('id', '!=', session()->getId())
                ->where(function ($q) {
                    $q->where('payload', 'like', '%login_cliente_%')
                      ->orWhere('payload', 'like', '%login_vendedor_%');
                })
                ->count();
        } catch (\Throwable) {
            return 0;
        }
    }

    private function cerrarSesionesZona(): int
    {
        try {
            return DB::table('sessions')
                ->where('id', '!=', session()->getId())
                ->where(function ($q) {
                    $q->where('payload', 'like', '%login_cliente_%')
                      ->orWhere('payload', 'like', '%login_vendedor_%');
                })
                ->delete();
        } catch (\Throwable) {
            return 0;
        }
    }
}
