<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * Affiche la page de documentation / tutoriels
     */
    public function index()
    {
        return view('documentation.index');
    }
}
