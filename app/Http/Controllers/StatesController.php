<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\State;

class StatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $states=State::all();

        return response()->json($states);
    }

    public function show($id)
    {
        $state=State::find($id);

        return response()->json($states);
    }

    public function towns($id)
    {
        $state=State::find($id);
        $towns=$state->towns;

        return response()->json($towns);
    }
}
