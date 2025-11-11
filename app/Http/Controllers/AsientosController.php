<?php

namespace App\Http\Controllers;

use App\Models\Asientos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsientosController extends Controller
{
    public function index()
    {
        $Asientos = Asientos::all();
        return response()->json($Asientos);
    }
}
    