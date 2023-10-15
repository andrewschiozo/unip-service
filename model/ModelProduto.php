<?php

namespace model;

use model\Sistema\Model;

class ModelProduto extends Model
{
    protected string $id_usuario;
    protected string $nome;
    protected string $descricao;
    protected string $categoria;
    protected string $uf;
    protected float $valor;
    private array $imagens;

	public function __set($attr, $val)
	{
		if(property_exists($this, $attr))
		{
            switch ($attr) {
				case 'imagens':
					$this->$attr[] = $val;
					break;
				
				default:
					$this->$attr = $val;
					break;
			}
		}
	}

    public function removerImagem($index)
    {
        if(array_key_exists($index, $this->imagens))
        {
            unset($this->imagens[$index]);
        }
    }
}