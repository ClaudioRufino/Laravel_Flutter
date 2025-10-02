<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use Illuminate\Http\Request;

use App\Services\Notificacoes;

use Illuminate\Support\Facades\DB;

class PagamentoController extends Controller
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

        $pagamentos = DB::table('users')
            ->join('inscricaos', 'users.id', '=', 'inscricaos.user_id')
            ->join('pagamentos', 'inscricaos.user_id', '=', 'pagamentos.inscricao_id')
            ->select('users.name', 'inscricaos.*', 'pagamentos.*')
            ->where('users.tipo', 'candidato')
            ->limit(5)->get();

        return view('user.admin.pagamentos', compact('geral', 'pagamentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function verPagamento($id)
    {
        $pagamento = Pagamento::find($id);

        if($pagamento){
            return view('user.admin.verPagamento', compact('pagamento'));
        }
    }

    public function validarPagamento($id){
         $pagamento = Pagamento::find($id);

        if($pagamento){

            if($pagamento->comprovativo){
                $pagamento->estado = "Sucesso";
                $pagamento->save();
            }
            
            return redirect()->route('admin.pagamentos');
        }
    }

    public function rejeitarPagamento($id){
         $pagamento = Pagamento::find($id);

        if($pagamento){

            if($pagamento->comprovativo){
                $pagamento->estado = "Rejeitado";
                $pagamento->save();
            }
            
            return redirect()->route('admin.pagamentos');
        }
    }

   
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Pagamento $pagamento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pagamento $pagamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pagamento $pagamento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pagamento $pagamento)
    {
        //
    }
}
