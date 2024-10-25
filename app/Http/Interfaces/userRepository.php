<?php
namespace App\Http\Interfaces;
// Interface criada para abstrair a conexão com o banco de dados, seguindo o princípio da inversão de dependêcia (D)
// Aqui também está presente o open-closed(O), pois é possível trocar facilmente o banco utilizado, mudando apenas a implementação
abstract class UserRepository {
  public function findById(int $id);
  public function create(array $data);
  public function select(array $columns = ['*'], int $perPage = 10);
}