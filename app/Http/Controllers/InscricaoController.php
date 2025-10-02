<?php

namespace App\Http\Controllers;

use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Http\Request;

use App\Services\Notificacoes;

use Illuminate\Support\Facades\DB;

class InscricaoController extends Controller
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

        $inscricoes = DB::table('users')
            ->join('inscricaos', 'users.id', '=', 'inscricaos.user_id')
            ->select('users.name', 'inscricaos.*')
            ->where('users.tipo', 'candidato')
            ->orderBy('inscricaos.id', 'desc')
            ->get();

        return view('user.admin.inscricoes', compact('geral', 'inscricoes'));
    }

    public function inscricao_usuario(){

        $inscritos = User::where('users.tipo', 'candidato')->count();

        return view('user.candidato.index', compact('inscritos'));
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
    public function show(Inscricao $inscricao)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inscricao $inscricao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inscricao $inscricao)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inscricao $inscricao)
    {
        //
    }
}
