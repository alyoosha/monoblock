<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class MainController extends Controller
{
    public function index() {
        //перезаписываем переменную $isHome, которая задана в AppServiceProvider
        //Она нужна для отображения элементов шаблона header.blade
        $isHome = true;

        return view('pages.main', compact(
            'isHome'
        ));
    }
}
