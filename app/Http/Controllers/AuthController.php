<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\Pagamento;
use App\Models\Documento;
use App\Models\FormacaoAnterior;
use App\Models\Inscricao;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class AuthController extends Controller
{
    public function login(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'message' => 'Erro ao validar os dados',
                'success' => false,
            ]);
        }
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email ou senha incorretos.',
                'success' => false,
            ]);
        } else {

            $user = User::where('email', $request->email)->first();

            if ($user->tipo == "candidato") {
                if ($user->name != "") {
                    return response()->json([
                        'message' => 'sucesso',
                        'success' => true,
                        'destino' => 'perfil',
                        'token'   => $user->createToken('login-token')->plainTextToken,
                    ]);
                } else {
                    return response()->json([
                        'message' => 'sucesso',
                        'success' => true,
                        'destino' => 'registarCandidato',
                        'token'   => $user->createToken(md5($user->email) . $user->email)->plainTextToken,
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'Erro inesperado',
            'success' => false,
        ]);
    }

    public function registar(Request $request)
    {
        $emailExistente = User::where('email', $request->email)->first();

        if ($emailExistente) {

            return response()->json([
                'success' => false,
                'errors' => "Já existe uma conta com esse email!"
            ]);
        }

        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Criar o usuário
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo' => 'candidato'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuário registrado com sucesso'
        ], 201);
    }

    public function updateRegister(Request $request)
    {

        $id = auth()->id();

        if ($id) {

            $usuario = User::find($id);

            if ($usuario) {
                $usuario->name = $request->nome;
                $usuario->bi = $request->bi;
                $usuario->genero = $request->genero;
                $usuario->morada = $request->morada;
                $usuario->nomePai = $request->nomePai;
                $usuario->nomeMae = $request->nomeMae;
                $usuario->telefone = $request->telefone;
                $usuario->dataNascimento = $request->dataNascimento;
                $usuario->save();

                FormacaoAnterior::create([
                    'nomeEscola' => $request->nomeEscola,
                    'anoConclusao' => $request->anoConclusao,
                    'cursoConcluido' => $request->cursoConcluido,
                    'mediaCurso' => $request->mediaCurso,
                    "user_id" => $id
                ]);

                $inscricao = Inscricao::create([
                    'data' => now()->format('d/m/Y'),
                    'turno' => $request->turnoPreferido,
                    "user_id" => $id
                ]);

                if (!Curso::exists()) {
                    $cursos = ["Eng. Informática", "Eng. de Telecomunicação", "Informática de Gestão"];
                    foreach ($cursos as $nome) {
                        Curso::create([
                            'nome' => $nome,
                            'duracao' => '5 anos'
                        ]);
                    }
                }

                switch ($request->cursoPretendido) {
                    case 'Engenharia Informática':
                        $cursoId = 1;
                        $this->addCursoInscricao($inscricao, $cursoId);
                        break;
                    case 'Telecomunicação':
                        $cursoId = 2;
                        $this->addCursoInscricao($inscricao, $cursoId);
                        break;
                    case 'Informática de Gestão':
                        $cursoId = 3;
                        $this->addCursoInscricao($inscricao, $cursoId);
                        break;
                }

                $valor = 5000;

                if ($request->cursoPretendido2 && $request->cursoPretendido2 != 'Segunda Opção') {
                    switch ($request->cursoPretendido2) {
                        case 'Engenharia Informática':
                            $cursoId2 = 1;
                            $this->addCursoInscricao($inscricao, $cursoId2);
                            break;
                        case 'Telecomunicação':
                            $cursoId2 = 2;
                            $this->addCursoInscricao($inscricao, $cursoId2);
                            break;
                        case 'Informática de Gestão':
                            $cursoId2 = 3;
                            $this->addCursoInscricao($inscricao, $cursoId2);
                            break;
                    }

                    $valor = 10000;
                }

                Pagamento::create([
                    'valor' => $valor,
                    'estado' => 'Pendente',
                    'data' => Carbon::now()->toDateString(),
                    'prazoPagamento' => Carbon::now()->toDateString(),
                    'inscricao_id' => $inscricao->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Usuário Atualizado com sucesso'
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Usuário não encontrado'
        ], 200);
    }

    private function addCursoInscricao($inscricao, $cursoId)
    {
        DB::table('curso_inscricao')->insert([
            'inscricao_id' => $inscricao->id,
            'curso_id' => $cursoId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    public function getPersonalData(Request $request)
    {
        $id = auth()->id();

        if ($id) {

            $dados = User::select('bi', 'name', 'email', 'genero', 'morada', 'nomePai', 'nomeMae', 'telefone', 'dataNascimento')
                ->find($id);


            return response()->json([
                'success' => true,
                'dado' => $dados,
                'message' => 'Usuário Atualizado com sucesso'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'dado' => $id,
                'message' => 'Falha ao recuperar os dados'
            ], 200);
        }
    }
    public function getAcademicData(Request $request)
    {
        $id = auth()->id();

        if ($id) {

            $dados = DB::table('formacao_anteriors')
                ->join('users', 'formacao_anteriors.user_id', '=', 'users.id')
                ->select(
                    'formacao_anteriors.nomeEscola',
                    'formacao_anteriors.mediaCurso',
                    'formacao_anteriors.anoConclusao',
                    'formacao_anteriors.cursoConcluido'
                )
                ->where('users.id', $id)
                ->first();

            return response()->json([
                'success' => true,
                'dado' => $dados,
                'message' => 'Usuário Atualizado com sucesso'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'dado' => $id,
                'message' => 'Falha ao recuperar os dados'
            ], 200);
        }
    }

    public function getInscriptionData(Request $request)
    {
        $id = auth()->id();

        if ($id) {

            $inscricao = Inscricao::where('user_id', $id)->first();

            $inscricao->load('cursos')->cursos[0];

            $dados = (object)[
                'turno'  => $inscricao->turno,
                'curso1' => $inscricao->load('cursos')->cursos[0]->nome,
                'curso2' => isset($inscricao->load('cursos')->cursos[1]) ? $inscricao->load('cursos')->cursos[1]->nome : "Nenhum",
                'data'   => $inscricao->data

            ];

            return response()->json([
                'success' => true,
                'dado' => $dados,
                'message' => 'Usuário recuperado com sucesso'
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'dado' => $id,
                'message' => 'Falha ao recuperar os dados'
            ], 200);
        }
    }

    public function getPaymentData(Request $request)
    {

        $id = auth()->id();

        $usuario = User::find($id);
        if ($usuario) {
            $inscricao = Inscricao::where('user_id', $id)->first();

            $dados = $inscricao->load('pagamento')->pagamento;

            $pagamento = (object)[
                'valor' => $dados->valor,
                'estado' => $dados->estado,
                'prazoPagamento' => $dados->prazoPagamento,
                'comprovativo' => $dados->comprovativo ? 'Enviado' : 'Não enviado'
            ];

            return response()->json([
                'success' => true,
                'dado' => $pagamento,
                'message' => 'Pagamento recuperado com sucesso'
            ], 200);
        }
    }

    public function upload(Request $request)
    {

        if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {

            $arquivo = $request->file('arquivo');
            $idCandidato = auth()->id();
            $nome = "Candidato" . $idCandidato . "." . $arquivo->getClientOriginalExtension();

            // Procura no banco um certificado existente para o usuário
            $documento = Documento::where('user_id', $idCandidato)
                ->where('nome', 'Certificado')
                ->first();

            // Armazena novo arquivo (substitui no storage com o mesmo nome)
            $path = $arquivo->storeAs('certificados', $nome, 'public');

            if ($documento) {
                // Atualiza o documento existente
                $documento->update([
                    'path' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Certificado atualizado com sucesso'
                ], 200);
            } else {
                // Cria um novo documento
                Documento::create([
                    'nome' => "Certificado",
                    'path' => $path,
                    "user_id" => $idCandidato
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Certificado enviado com sucesso'
                ], 200);
            }
        }
    }

    public function uploadComprovativo(Request $request)
    {

        if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
            $arquivo = $request->file('arquivo');
            $idCandidato = auth()->id();

            $nome = "Candidato" . $idCandidato . "." . $arquivo->getClientOriginalExtension();
            $path = $arquivo->storeAs('comprovativos', $nome, 'public');

            $usuario = User::find($idCandidato);

            $inscricao = Inscricao::where('user_id', $idCandidato)->first();

            $idPagamento = $inscricao->load('pagamento')->pagamento->id;

            $pagamento = Pagamento::find($idPagamento);
            $pagamento->comprovativo = $path;
            $pagamento->save();

            return response()->json([
                'success' => true,
                'message' => 'Comprovativo enviado com sucesso'
            ], 200);
        }
    }

    public function getName(Request $request)
    {

        $id = auth()->id();

        $user = User::find($id);

        $nomeCompleto = $user->name;

        // Separa em partes
        $partes = explode(' ', trim($nomeCompleto));

        // Primeiro nome
        $primeiroNome = $partes[0];

        // // Último nome
        $ultimoNome = $partes[count($partes) - 1];

        // Nome Resumido
        $nomeReduzido = $primeiroNome . " " . $ultimoNome;

        return response()->json([
            'success' => true,
            'dado' => $nomeReduzido
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Todos os tokens foram revogados'
        ], 200);
    }
}
