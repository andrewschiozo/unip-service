<?php
namespace controller;

use util\Request;
use util\Response;
use util\Ldap;
use util\JWT;
use util\Config;

use model\ModelProduto;
use dao\DaoProduto;

class MeuBrecho extends Controller
{
    public function get()
    {
        echo 'opa';
        die();
    }

    public function getProdutos()
    {
        $filtro = [];
        if(property_exists(Request::getData(), 'regiao'))
            $filtro['uf'] = Request::getData()->regiao;
        
        if(property_exists(Request::getData(), 'categoria'))
            $filtro['categoria'] = Request::getData()->categoria;

        if(property_exists(Request::getData(), 'id'))
            $filtro['id'] = Request::getData()->id;

        
        $DaoProduto = new DaoProduto;
        $produtos = $DaoProduto->get($filtro, true);

        Response::getInstance()
				->setData($produtos)
				->ok();
    }
}
