    <?php

    namespace App\Http\Controllers;

    use App\Http\Requests\UserCreateRequest;
    use App\Http\Requests\UserUpdateRequest;
    use App\Models\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;

    /**
     * Class UserController
     *
     * @package App\Http\Controllers
     * @author Vinícius Siqueira
     * @link https://github.com/ViniciusSCS
     * @date 2024-08-23 21:48:54
     * @copyright UniEVANGÉLICA
     */

    // Erros encontrados quanto à aplicação do solid nesse controller:

    // Uso direto do banco de dados em vez de uma abstração
    // Uso repetido de mesmas funcionalidades que poderiam ser métodos separados e reaproveitados
    // Métodos com mais de uma responsabilidade 

    // Correções:
    // Ao depender de uma abstração se usa do princípio da inversão de dependência (D), 
    // o que permite extender o código com implementações mais fáceis, aplicando assim também o princípio aberto fechado (O).
    // Separando em mais métodos as funcionalidaes, é possível segregar corretamente as responsabilidades seguindo assim o princípio de responsabilidade Única(S) 
    class UserController extends Controller
    {
        private $userRepository;
        
        // Utilizando implementação criada para abstrair a conexão com o banco de dados, seguindo o princípio da inversão de dependêcia (D)
        // Aqui também está presente o open-closed(O), pois é possível trocar facilmente o banco utilizado, mudando apenas a implementação
        public function __construct(UserRepository $userRepository)
        {
            $this->userRepository = $userRepository;
        }

        public function index()
        {
            $user = $this->userRepository->select('id', 'name', 'email', 'created_at')
                ->paginate('10');

            return [
                'status' => 200,
                'message' => 'Usuários encontrados!!',
                'user' => $user
            ];
        }

        /**
         * Show the form for creating a new resource.
         */
        public function me()
        {
            $user = Auth::user();

            return [
                'status' => 200,
                'message' => 'Usuário logado!',
                "usuario" => $user
            ];
        }

        /**
         * Store a newly created resource in storage.
         */
        public function store(UserCreateRequest $request)
        {
            $data = $this->getRequestedData($request);
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            return [
                'status' => 200,
                'message' => 'Usuário cadastrado com sucesso!!',
                'user' => $user
            ];
        }

        /**
         * Display the specified resource.
         */
        public function show(string $id)
        {
            $user = $this->findUser($id);

            return [
                'status' => 200,
                'message' => 'Usuário encontrado com sucesso!!',
                'user' => $user
            ];
        }

        /**
         * Show the form for editing the specified resource.
         */
        public function edit(string $id)
        {
            // 
        }

        // Criando método para abstrair lógica, seguindo o princípio da responsabilidade unica (S)
        public function getRequestedData(UserUpdateRequest $request){
            return $data = $request->all();
        }

         // Criando método para abstrair lógica, seguindo o princípio da responsabilidade unica (S)
        public function encryptPassword(string $password){
            return bcrypt($password); 
        }

        /**
         * Update the specified resource in storage.
         */
        public function update(UserUpdateRequest $request, string $id)
        {
            $data = $this->getRequestedData($request);
            $user = $this->findUser($id);

            // Verifica se a senha está presente nos dados da requisição
            if (isset($data['password'])) {
                $data['password'] = $this->encryptPassword($data['password']);  // Criptografa a senha antes de salvar
            }   

            $user->update($data);

            return [
                'status' => 200,
                'message' => 'Usuário atualizado com sucesso!!',
                'user' => $user
            ];
        }

        /**
         * Remove the specified resource from storage.
         */
        // Criando método para abstrair lógica, seguindo o princípio da responsabilidade unica (S)
        public function findUser(string $id){
            $user = $this->userRepository->findById($id);

            if(!$user){
                return [
                    'status' => 404,
                    'message' => 'Usuário não encontrado! Que triste!',
                    'user' => $user
                ];
            }
            return $user;
        }

        public function destroy(string $id)
        {
            $user = $this->findUser($id);
            $user->delete();
            return [
                'status' => 200,
                'message' => 'Usuário deletado com sucesso!!'
            ];
        }
    }
