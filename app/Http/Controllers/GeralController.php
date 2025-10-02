<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use Illuminate\Http\Request;

use App\Services\Notificacoes;

class GeralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notificacoes = new Notificacoes();

         $geral = (Object) [
            "notificacoes" => $notificacoes->geral(),
        ];

        $sistema = Sistema::all()->first();

        if(!$sistema){
            $sistema = (Object)[
                'nome' => "Instic-System",
                'versao' => "Versão 1.0",
                'ano_lectivo' => "2025/2026",
                'valor_por_curso' => "5000"
            ];
        }

        return view('user.admin.sistema', compact('geral', 'sistema'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(cr $cr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return "Clicou em geral";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, cr $cr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(cr $cr)
    {
        //
    }

    public function listas()
    {
        return view('listas');
    }

    public function ajuda()
    {
        return view('ajuda');
    }

    public function documentacao()
    {
        return view('topicos');
    }
}
