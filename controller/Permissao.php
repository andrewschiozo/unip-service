<?php
namespace controller;

use util\Request;
use util\Response;
use dao\Sistema\DaoPermissao;

class Permissao extends PrivateController
{

	public function get()
	{
		$DaoPermissao = new DaoPermissao();
		$permissoes = $DaoPermissao->get($id, $ativo);
        
		Response::getInstance()
				->setData($permissoes)
				->ok();
	}
}
