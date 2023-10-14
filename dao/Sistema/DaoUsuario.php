<?php

namespace dao\Sistema;

use model\Sistema\ModelUsuario;

class DaoUsuario extends DB
{
    protected int $id;
	protected string $nome;
	protected ?string $email;
	protected string $senha;
	protected string $uf;

    public function __construct()
    {
        $this->table = 'sys_usuario';
        $this->model = new ModelUsuario;
        parent::__construct();
    }

    public function setModel(ModelUsuario $ModelUsuario)
    {
        $this->model = $ModelUsuario;
        return $this;
    }

    public function get($id = null, $allColumns = false)
    {
        $queryString = 'SELECT * FROM ' . $this->table . ' WHERE 1 = 1 ';
        $queryString .= $id ? ' AND id = ' . $id : '';
        $queryString .= ' ORDER BY id DESC';
        
        $result = [];
        try {
            $stmt = $this->getConn()->prepare($queryString);
            $stmt->execute();
            
            $result = $allColumns ?
                      $stmt->fetchAll(\PDO::FETCH_CLASS, 'dao\Sistema\DaoUsuario') :
                      $stmt->fetchAll(\PDO::FETCH_CLASS, 'model\Sistema\ModelUsuario');
        } catch (\PDOException $e) {
            echo ($e->getMessage());
        }
        return $result;
    }

    public function getUsuario()
    {
        $queryString = 'SELECT * FROM ' . $this->table;
        if (!is_null($this->model->usuario)) {
            $queryString .= ' WHERE usuario = \'' . $this->model->usuario . '\'';
        }
        $result = [];
        try {
            $stmt = $this->getConn()->prepare($queryString);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_CLASS, 'dao\Sistema\DaoUsuario');
            
            switch (count($result)) {
                case 0:
                    throw new \Exception('Usuário não encontrado');
                    break;
                
                case 1:
                    return $result[0];
                    break;
                default:
                    throw new \Exception('Usuário duplicado');
                    break;
            }
        } catch (\PDOException $e) {
            return $e;
        }
        return $result;
    }

    public function login()
    {
        $queryString = 'SELECT * FROM ' . $this->table . 
                      ' WHERE usuario = :usuario
                          AND senha = :senha
                          AND sistema = :sistema';

        try {
            $stmt = $this->getConn()->prepare($queryString);
            $stmt->bindValue(':usuario', $this->model->usuario, \PDO::PARAM_STR);
            $stmt->bindValue(':senha', $this->model->senha, \PDO::PARAM_STR);
            $stmt->bindValue(':sistema', $this->model->sistema, \PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchObject('dao\Sistema\DaoUsuario');
            
            if(!$this->model->ativo)
            {
                throw new \Exception('Usuário inativo');
            }
        } catch (\PDOException $e) {
            echo ($e->getMessage());
        }
        return $result;
    }

    public function save($id = null)
    {
        return $id ? $this->update($id) : $this->insert();
    }

    private function insert()
    {
        
        $queryUsuario = 'INSERT INTO ' . $this->table . ' (nome, email, senha, uf)
        VALUES (:nome, :email, :senha, :uf)';
        $usuarioId = false;
        
        try {
            $this->getConn()->beginTransaction();

            $stmt = $this->getConn()->prepare($queryUsuario);
            $stmt->bindValue(':nome', $this->model->nome, \PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->model->email, \PDO::PARAM_STR);
            $stmt->bindValue(':senha', $this->model->senha, \PDO::PARAM_STR);
            $stmt->bindValue(':uf', $this->model->uf, \PDO::PARAM_STR);

            $stmt->execute();
            $usuarioId = $this->getConn()->lastInsertId();
            
            $this->getConn()->commit();            
        } catch (\PDOException $e) {
            $this->getConn()->rollback();
            echo ($e->getMessage());
        }
        return $usuarioId;
    }

    private function update($id)
    {
        $queryUsuario = 'UPDATE ' . $this->table . ' SET nome = :nome
                                                       ,email = :email
                                                       ,senha = :senha
                                                       ,uf = :uf
                                                    WHERE id = :id';
        
        try {
            $this->getConn()->beginTransaction();

            $stmt = $this->getConn()->prepare($queryUsuario);
            $stmt->bindValue(':nome', $this->model->nome, \PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->model->email, \PDO::PARAM_STR);
            $stmt->bindValue(':senha', $this->model->senha, \PDO::PARAM_STR);
            $stmt->bindValue(':uf', $this->model->uf, \PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

            $stmt->execute();
            
            $this->getConn()->commit();            
        } catch (\PDOException $e) {
            $this->getConn()->rollback();
            echo ($e->getMessage());
        }
        return $id;
    }

}
