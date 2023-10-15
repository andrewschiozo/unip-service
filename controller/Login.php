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
		$Usuario = new ModelUsuario;
		$Usuario->nome = Request::getData()->nome;
		$Usuario->email = Request::getData()->email;
		$Usuario->senha = Request::getData()->senha;
		$Usuario->uf = Request::getData()->regiao;
		
		$DaoUsuario = new DaoUsuario;
		$DaoUsuario->setModel($Usuario);
		
		if(count($DaoUsuario->getUsuario()) > 0)
		{
			Response::getInstance()
					->badRequest('O Usuário já existe');
		}

		try{
			$DaoUsuario->save();
			
			Response::getInstance()
					->created();

		} catch (\Exception $e) {
			Response::getInstance()
					->badRequest('Wooops! Não foi possível salvar.');
		}
	}
}
