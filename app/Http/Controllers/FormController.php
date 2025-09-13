<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;
class FormController extends Controller
{
    //

    public function index()
    {
        $forms = Form::all();
        return view('welcome', compact('forms'));
        // return view('welcome');
    }
    public function store(Request $request)
    {
        $request->validate([
            'schema' => 'required|array',
        ]);
        // dd($request->schema);
        $form = Form::create([
            'name' => $request->name,
            'schema' => $request->schema,
        ]);

        return response()->json(['message' => 'Form saved successfully', 'form' => $form], 201);
    }

    public function show($id)
    {
        $form = Form::findOrFail($id);
        return view('forms.show', compact('form'));
    }
}
