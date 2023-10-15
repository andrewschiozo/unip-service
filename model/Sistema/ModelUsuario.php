<?php

namespace model\Sistema;

class ModelUsuario extends Model
{
	protected string $nome;
	protected string $email;
	protected string $senha;
	protected string $uf;

	public function __set($attr, $val)
	{
		if(property_exists($this, $attr))
		{
			switch ($attr) {
				case 'email':
					$this->$attr = strtolower($val);
					break;

				case 'senha':
					$this->$attr = sha1($val);
					break;

                case 'uf':
                    $this->$attr = strtoupper($val);
                    break;
				
				default:
					$this->$attr = $val;
					break;
			}
		}
	}

	public function novaSenha()
	{
		$senha = bin2hex(random_bytes(8));
		$this->__set('senha', $senha);
		return $senha;
	}
}