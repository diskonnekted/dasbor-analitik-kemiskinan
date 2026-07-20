<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

abstract class Controller
{
    /**
     * Render view khusus mobile bila perangkat terdeteksi mobile dan
     * view "mobile.{name}" tersedia. Selain itu pakai view desktop biasa.
     * Query "?view=mobile" atau "?view=desktop" dapat memaksa mode tertentu.
     */
    protected function responsiveView(string $name, array $data = [])
    {
        if ($this->prefersMobile() && View::exists("mobile.{$name}")) {
            return view("mobile.{$name}", $data);
        }

        return view($name, $data);
    }

    protected function prefersMobile(): bool
    {
        $forced = request()->query('view');
        if ($forced === 'mobile') {
            return true;
        }
        if ($forced === 'desktop') {
            return false;
        }

        $agent = (string) request()->header('User-Agent');
        if ($agent === '') {
            return false;
        }

        return (bool) preg_match(
            '/Android|iPhone|iPod|Windows Phone|BlackBerry|Opera Mini|IEMobile|Mobile Safari/i',
            $agent
        );
    }
}
