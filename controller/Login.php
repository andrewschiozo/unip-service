<?php
namespace controller;

use dao\Sistema\DaoUsuario;
use util\Request;
use util\Response;
use util\Ldap;
use util\JWT;
use util\Config;

use model\Sistema\ModelUsuario;

class Login extends Controller
{
	public function __construct()
	{
        parent::__construct();
	}

	public function post()
	{
       
	}

	public function primeiroAcesso()
	{
		echo '<pre>';
		var_dump(Request::getData());
		$Usuario = new ModelUsuario;
		$Usuario->nome = Request::getData()->nome;
		$Usuario->email = Request::getData()->email;
		$Usuario->senha = Request::getData()->senha;
		$Usuario->uf = Request::getData()->regiao;
		
		$DaoUsuario = new DaoUsuario;
		$DaoUsuario->setModel($Usuario);
		var_dump($DaoUsuario->getUsuario());

		die();
		Response::getInstance()
					->ok();
	}

}
