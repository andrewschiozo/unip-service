<?php
namespace util;

use controller;

class Request
{
	const ALLOW_REQUEST_METHODS = ['POST', 'GET', 'PUT'];

	private $url;
	private static $data;
	private static $header;
	private static $token;
	private static $method;
	private static $resource;

	public function __construct()
	{
		if($this->checkMethod() && $this->checkController() && $this->checkHeader())
		{
			self::$data = new \stdClass;
			self::$token = str_replace('Bearer ', '', self::$header['Authorization']);
			$controller = $this->formatController();
			$method = strtoupper($_SERVER['REQUEST_METHOD']);
			self::$method = $method;
			$this->setData();
			$controller = new $controller();
			$controller->$method();
		}
		else
		{
			Response::getInstance()->badRequest("Requisição inválida");
		}
	}

	private function checkMethod()
	{
		return in_array(strtoupper($_SERVER['REQUEST_METHOD']), self::ALLOW_REQUEST_METHODS);
	}

	private function checkController()
	{
		$controller = $this->formatController();
		$filePath = str_replace('\\', DIRECTORY_SEPARATOR, $controller) . '.php';
		$class = $controller;
		return file_exists($filePath) && class_exists($controller);
	}

	private function checkHeader()
	{
		self::$header = apache_request_headers();
		self::$header['Authorization'] = array_key_exists('Authorization', self::$header) ? self::$header['Authorization'] : '';
		if (array_key_exists('Content-Type', self::$header) && self::$header['Content-Type'] == 'application/json') {
			return true;
		}
		return self::$header['Authorization'];
	}

	private function formatController()
	{
		$uri = explode('/' ,$_SERVER['REQUEST_URI']);
		$uri = array_slice($uri, 3);
		$uri = implode('\\', $uri);
		$controller = strtok($uri, '?');
		$controller = 'controller\\' . $controller;
		return $controller;
	}

	private function setData()
	{
		if(array_key_exists('REDIRECT_QUERY_STRING', $_SERVER) && self::$method === 'GET')
		{
			$rawData = json_decode(urldecode($_SERVER['REDIRECT_QUERY_STRING']));
		}
		else{
			$rawData = json_decode(urldecode(file_get_contents('php://input')));
		}
		
		//object {data: {}, resource: null}
		self::$data = property_exists($rawData, 'data') ? $rawData->data : new \stdClass;
		self::$resource = property_exists($rawData, 'resource') ? $rawData->resource : null;
	}
	public static function getData()
	{
		return self::$data;
	}

	public static function getResource()
	{
		return self::$resource;
	}

	public static function getHeader()
	{
		return self::$header;
	}

	public static function getToken()
	{
		return self::$token;
	}

	public static function getDecodedToken(){
		return JWT::decode(self::$token,  Config::JWTKEY);
	}

}