<?php

namespace dao;

use dao\Sistema\DB;
use model\ModelProduto;

class DaoProduto extends DB
{
    protected int $id;
	protected string $id_usuario;
	protected string $nome;
	protected string $descricao;
	protected string $categoria;
	protected string $uf;
	protected float $valor;

    public function __construct()
    {
        $this->table = 'produto';
        $this->model = new ModelProduto;
        parent::__construct();
    }

    public function setModel(ModelProduto $ModelProduto)
    {
        $this->model = $ModelProduto;
        return $this;
    }

    public function get($filtro = [], $allColumns = false)
    {
        $queryString = 'SELECT * FROM ' . $this->table . ' WHERE 1 = 1 ';
        $queryString .= array_key_exists('id', $filtro) ? ' AND id = ' . $filtro['id'] : '';
        $queryString .= array_key_exists('uf', $filtro) ? ' AND uf = "' . $filtro['uf'] . '"' : '';
        $queryString .= array_key_exists('categoria', $filtro) ? ' AND categoria = "' . $filtro['categoria'] . '"': '';
        $queryString .= ' ORDER BY id DESC';
        
        $result = [];
        try {
            $stmt = $this->getConn()->prepare($queryString);
            $stmt->execute();
            
            $result = $allColumns ?
                      $stmt->fetchAll(\PDO::FETCH_CLASS, 'dao\Sistema\DaoProduto') :
                      $stmt->fetchAll(\PDO::FETCH_CLASS, 'model\Sistema\ModelProduto');
        } catch (\PDOException $e) {
            echo ($e->getMessage());
        }
        return $result;
    }

    public function getUsuario()
    {
        $queryString = 'SELECT * FROM ' . $this->table;
        if (!is_null($this->model->email)) {
            $queryString .= ' WHERE email = \'' . $this->model->email . '\'';
        }
        $result = [];
        try {
            $stmt = $this->getConn()->prepare($queryString);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_CLASS, 'dao\Sistema\DaoUsuario');
        } catch (\PDOException $e) {
            return $e;
        }
        return $result;
    }

    public function save($id = null)
    {
        return $id ? $this->update($id) : $this->insert();
    }

    private function insert()
    {
        
        $queryUsuario = 'INSERT INTO ' . $this->table . ' (id_usuario, nome, descricao, categoria, uf, valor)
        VALUES (:id_usuario, :nome, :descricao, :categoria, :uf, :valor)';
        $produtoId = false;
        
        try {
            $this->getConn()->beginTransaction();

            $stmt = $this->getConn()->prepare($queryUsuario);
            $stmt->bindValue(':id_usuario', $this->model->id_usuario, \PDO::PARAM_STR);
            $stmt->bindValue(':nome', $this->model->nome, \PDO::PARAM_STR);
            $stmt->bindValue(':descricao', $this->model->descricao, \PDO::PARAM_STR);
            $stmt->bindValue(':categoria', $this->model->categoria, \PDO::PARAM_STR);
            $stmt->bindValue(':uf', $this->model->uf, \PDO::PARAM_STR);
            $stmt->bindValue(':valor', $this->model->valor, \PDO::PARAM_STR);

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
        $queryUsuario = 'UPDATE ' . $this->table . ' SET id_usuario = :id_usuario
                                                       ,nome = :nome
                                                       ,descricao = :descricao
                                                       ,categoria = :categoria
                                                       ,uf = :uf
                                                       ,valor = :valor
                                                    WHERE id = :id';
        
        try {
            $this->getConn()->beginTransaction();

            $stmt = $this->getConn()->prepare($queryUsuario);
            $stmt->bindValue(':id_usuario', $this->model->id_usuario, \PDO::PARAM_STR);
            $stmt->bindValue(':nome', $this->model->nome, \PDO::PARAM_STR);
            $stmt->bindValue(':descricao', $this->model->descricao, \PDO::PARAM_STR);
            $stmt->bindValue(':categoria', $this->model->categoria, \PDO::PARAM_STR);
            $stmt->bindValue(':uf', $this->model->uf, \PDO::PARAM_STR);
            $stmt->bindValue(':valor', $this->model->valor, \PDO::PARAM_STR);
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
