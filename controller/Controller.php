<?php
namespace controller;

use interfaces\iController;
use util\Request;
use util\Response;

abstract class Controller implements iController
{

	public function __construct()
	{
		if (method_exists($this, Request::getResource())) {
            $this->{Request::getResource()}();
            return;
        }
	}

	public function get()
	{
		Response::getInstance()->badRequest("Não implementado");
	}

	public function post()
	{
		Response::getInstance()->badRequest("Não implementado");
	}

	public function put()
	{
		Response::getInstance()->badRequest("Não implementado");
	}

	public function delete()
	{
		Response::getInstance()->badRequest("Não implementado");
	}
}