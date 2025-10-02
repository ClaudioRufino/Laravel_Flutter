<?php

namespace App\Http\Controllers;

use App\Models\User;

use App\Services\Candidato;
use App\Models\Documento;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Services\Notificacoes;
use Illuminate\Support\Facades\DB;


use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function verDocumento($id)
    {
        $path = Documento::where('user_id', $id)->first()->path;
        return view('user.candidato.verCertificado', compact('path'));
    }

    public function ficha($id)
    {
        $dados = DB::table('users')
            ->join('inscricaos', 'inscricaos.user_id', '=','users.id')
            ->join('pagamentos', 'inscricaos.user_id', '=', 'pagamentos.inscricao_id')
            ->select('users.*', 'inscricaos.*', 'pagamentos.*')
            ->where('users.id', $id)
            ->first();
        

        $idInscricao = $dados->inscricao_id;

        $cursos = DB::table('curso_inscricao')
            ->join('cursos', 'curso_inscricao.curso_id', '=', 'cursos.id')
            ->join('inscricaos', 'curso_inscricao.inscricao_id', '=', 'inscricaos.id')
            ->select('cursos.nome')
            ->where('inscricaos.id', $idInscricao)
            ->get();
        
         if(count($cursos) == 2){
            $opcao1 = $cursos[0]->nome;
            $opcao2 = $cursos[1]->nome;
        }
        else{
             $opcao1 = $cursos[0]->nome;
             $opcao2 = "Nenhum";
         }


        $cursos = (Object)[
            'opcao1' => $opcao1,
            'opcao2' => $opcao2
        ];

        $pdf = Pdf::loadView('user.candidato.comprovativo', compact('dados', 'cursos'));
        return $pdf->download('Ficha.pdf');
        return $pdf->stream('Ficha.pdf');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

            $emailExistente = User::where('email', $request->email)->first();

            if($emailExistente){
                return back()->withErrors([
                'email' => 'Já existe uma conta com esse email!',
                ])->onlyInput('email');
            }
        
            $cadastro = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => 'candidato',
            ]);

            return back()->with('success', 'Conta do usuário criado com sucesso');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $usuario = User::find($id);

        if($usuario){

            $usuario = new Candidato();

            $pessoais = $usuario->dadosPessoais($id);
            $academicos = $usuario->dadosAcademicos($id);
            $inscricoes = $usuario->dadosInscricoes($id);
            $pagamentos = $usuario->dadosPagamento($id);

            return view('user.candidato.perfil', compact('pessoais', 'academicos', 'inscricoes', 'pagamentos'));
        }


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
