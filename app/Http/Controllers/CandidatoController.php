<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Inscricao;
use App\Models\Documento;
use App\Models\Pagamento;
use App\Services\Candidato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\Notificacoes;

use Illuminate\Support\Facades\DB;

class CandidatoController extends Controller
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

        $candidatos = User::where('tipo', 'candidato')->get();
        return view('user.admin.candidatos', compact('geral', 'candidatos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function upload(Request $request){

        if($request->hasFile('arquivo') && $request->file('arquivo')->isValid())
        {
            $arquivo = $request->file('arquivo');
            $idCandidato = $request->input('user_id');

            $nome = "Candidato" . $idCandidato . "." .$arquivo->getClientOriginalExtension();
            
             // Procura no banco um certificado existente para o usuário
            $documento = Documento::where('user_id', $idCandidato)
                                ->where('nome', 'Certificado')
                                ->first();

            // Armazena novo arquivo (substitui no storage com o mesmo nome)
            $path = $arquivo->storeAs('certificados', $nome, 'public');
            
            
            $usuario = User::find($idCandidato);

             if ($documento) {
                
                $documento->update([
                    'path' => $path
                ]);

                return response()->json(true);
                
            } else {

                Documento::create([
                    'nome' => "Certificado",
                    'path' => $path,
                    "user_id" => $idCandidato
                ]);

                return response()->json(true);
            }
        }
    }


    public function uploadComprovativo(Request $request){

        if($request->hasFile('comprovativo') && $request->file('comprovativo')->isValid())
        {
            $comprovativo = $request->file('comprovativo');
            $idCandidato = $request->input('user_id');
            $idPagamento = $request->input('idPagamento');
            
            $nome = "Candidato" . $idCandidato . "." .$comprovativo->getClientOriginalExtension();
            $path = $comprovativo->storeAs('comprovativos', $nome, 'public');
            
            $usuario = User::find($idCandidato);
            
            
            if($usuario){
                $pagamento = Pagamento::find($idPagamento);
                $pagamento->comprovativo = $path;
                $pagamento->save();

                return response()->json(true);
            }
            else{
                return response()->json(false);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $dados = json_decode($request->getContent(), true)["candidatosDados"];

        $candidato = new Candidato();

        $candidato->pessoais($dados);
        $candidato->academicos($dados);
        $candidato->inscricoes($dados);
        
        return response()->json(true);
    }

    public function findBI($bi){

        $existe = User::where("bi", $bi)->first();
        
        if($existe){
            return response()->json(true);
        }
        else{
            return response()->json(false);
        }
            
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        //
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
    public function destroy(int $id)
    {
        //
    }
}
