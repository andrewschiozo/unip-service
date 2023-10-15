<?php
namespace controller;

use util\Request;
use util\Response;
use util\Ldap;
use util\JWT;
use util\Config;

use dao\Sistema\DaoUsuario;
use model\Sistema\ModelUsuario;

class Login extends Controller
{
	public function __construct()
	{
        parent::__construct();
	}

	public function post()
	{
		$Usuario = new ModelUsuario;
		$Usuario->email = Request::getData()->email;
		$Usuario->senha = Request::getData()->senha;
		$DaoUsuario = new DaoUsuario;
		$login = $DaoUsuario->setModel($Usuario)->login();
		if(!$login)
		{
				Response::getInstance()
				->addMessage('Credenciais inválidas')
				->unauthorized();
		}
		
		$DaoUsuario = $login;

		$Now = new \DateTime();
		//Dados do Token
		$payload = [
			'nbf' => $Now->getTimestamp()
			,'exp' => $Now->add(new \DateInterval('P1D'))->getTimestamp()
			,'data' => [
				'user' => $DaoUsuario->email
				,'name' => $DaoUsuario->nome
				,'id' => $DaoUsuario->id
				,'regiao' => $DaoUsuario->uf
			]
		];
		
		$jwToken = JWT::encode($payload, Config::JWTKEY);
		Response::getInstance()
				->addData($jwToken, 'token')
				->ok();
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
