<?php
namespace controller;

use interfaces\iController;
use util\Request;
use util\Response;
use util\JWT;
use util\Config;

abstract class PrivateController extends Controller
{

	final public function __construct()
	{
		$this->autenticar();
		parent::__construct();
	}

	public function autenticar()
	{
		$token = Request::getToken();
		try{
			$jwToken = JWT::decode($token,  Config::JWTKEY);

			if(!self::checkTokenExpiration($jwToken->exp))
			{
				Response::getInstance()
					->badRequest('Token expirado');
			}
		}
		catch(\Exception $e)
		{
			Response::getInstance()
					->unauthorized('Token inválido');
		}
	}

	public function autorizar()
	{

	}

	private function checkTokenExpiration($tokenExpiration)
	{
		$Now = new \DateTime();
		$Now = $Now->getTimestamp();
		return ($tokenExpiration > $Now);
	}
}