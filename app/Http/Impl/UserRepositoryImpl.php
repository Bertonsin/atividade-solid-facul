<?php
namespace App\Http\Impl;
// Implementando banco a partir da interface criada para abstrair a conexão com o banco de dados, seguindo o princípio da inversão de dependêcia (D)
// Aqui também está presente o open-closed(O), pois é possível trocar facilmente o banco utilizado, mudando apenas a implementação
use App\Interfaces\userRepository;
class UserRepositoryImpl implements UserRepository{
  public function findById(int $id)
  {
      return User::find($id);
  }

  public function create(array $data)
  {
    return User::create($data);
  } 

  public function select(array $columns = ['*'], int $perPage = 10)
  {
    return User::select($columns)->paginate($perPage);
  }
}