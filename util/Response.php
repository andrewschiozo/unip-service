<?php

namespace util;

class Response
{
	private static $instance;

	private $status = true;
	private $statusCode = 200;
	private $message = [];
	private $data = [];

	protected function __construct() {}
	private function __clone() {}
	public function __wakeup() {}

	public static function getInstance()
    {
        if(is_null(self::$instance))
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

	public function addData($data, $index = null)
	{
		if(is_null($index))
		{
			$this->data[] = $data;
			return $this;
		}
		$this->data[$index] = $data;
		return self::$instance;
	}

	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}

	public function addMessage($message)
	{
		$this->message[] = $message;
		return self::$instance;
	}

	public function ok()
	{
		$this->statusCode = 200;
		$this->output();
	}

	public function created()
	{
		$this->statusCode = 201;
		$this->output();
	}

	public function badRequest($message = null)
	{
		$this->status = false;
		$this->statusCode = 400;
		if(!is_null($message))
		{
			$this->addMessage($message);
		}
		$this->output();
	}

	public function unauthorized($message = null)
	{
		$this->status = false;
		$this->statusCode = 401;
		if(!is_null($message))
		{
			$this->addMessage($message);
		}
		$this->output();
	}

	public function serviceUnavailable($message = null)
	{
		$this->status = false;
		$this->statusCode = 503;
		if(!is_null($message))
		{
			$this->addMessage($message);
		}
		$this->output();
	}

	private function output()
	{
		$response = new \stdclass();
		$response->status = $this->status;
		$response->message = $this->message;
		$response->data = $this->data;
		header('Content-Type: application/json', true, $this->statusCode);
		echo json_encode($response);
		exit();
	}

}