<?php
namespace controller;

use util\Request;
use util\Response;
use util\Ldap;
use util\JWT;
use util\Config;

use model\ModelProduto;
use dao\DaoProduto;

class Produto extends PrivateController
{
    public function get()
    {
        
    }

	public function post()
	{
		$Produto = new ModelProduto;
        $Produto->id_usuario = Request::getDecodedToken()->data->id;
        $Produto->nome = Request::getData()->nome;
        $Produto->descricao = Request::getData()->descricao;
        $Produto->categoria = Request::getData()->categoria;
        $Produto->uf = Request::getData()->regiao;
        $Produto->valor = Request::getData()->preco;

        // $Produto->imagens = Request::getData()->imagens;
		$DaoProduto = new DaoProduto;
		$DaoProduto->setModel($Produto);
		echo '<pre>';
		print_r(Request::getData());
		// print_r($_FILES);
		die();
		try{
			$DaoProduto->save();
			
			Response::getInstance()
					->created();

		} catch (\Exception $e) {
			Response::getInstance()
					->badRequest('Wooops! Não foi possível salvar.');
		}		
	}
}
