<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Notificacoes;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cursoInformatica  = DB::table('curso_inscricao')
                ->where('curso_id', '=', 1)
                ->count();

        $cursoTelecomunicacao  = DB::table('curso_inscricao')
                ->where('curso_id', '=', 2)
                ->count();

        $cursoInfGestao  = DB::table('curso_inscricao')
                ->where('curso_id', '=', 3)
                ->count();

       
        $notificacoes = new Notificacoes();


        $geral = (Object) [
            "totInformatica" => $cursoInformatica,
            "totTelecom" => $cursoTelecomunicacao,
            "totGestao" => $cursoInfGestao,
            "notificacoes" => $notificacoes->geral(),
        ];

        return view('user.admin.index', compact('geral'));
    }

    public function notificacoes(){

        $notificacoes = new Notificacoes();

        $notifica = DB::table('users')
            ->join('inscricaos', 'users.id', '=', 'inscricaos.user_id')
            ->select('users.name','users.email', 'users.telefone', 'inscricaos.*')
            ->where('users.tipo', 'candidato')
            ->latest()
            ->get();
        
        $geral = (Object) [
            "notificacoes" => $notificacoes->geral(),
        ];

        return view('user.admin.notificacoes', compact('geral', 'notifica'));
    }

    public function lista()
    {
         $notificacoes = new Notificacoes();

         $geral = (Object) [
            "notificacoes" => $notificacoes->geral(),
        ];

        $admins = User::where('tipo', 'admin')->get();
        return view('user.admin.administradores', compact('geral', 'admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function conta()
    {
         $notificacoes = new Notificacoes();

         $geral = (Object) [
            "notificacoes" => $notificacoes->geral(),
        ];

        $admin = Auth::user();

        return view('user.admin.conta', compact('geral', 'admin'));
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
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $admin->name = $request->nome;
        $admin->morada = $request->morada;
        $admin->telefone = $request->telefone;
        $admin->save();

        return redirect()->route('admin.administradores');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }
}
