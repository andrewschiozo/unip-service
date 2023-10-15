<?php
namespace controller;

use util\Request;
use util\Response;
use util\Ldap;
use util\JWT;
use util\Config;

use model\ModelProduto;
use dao\DaoProduto;
use dao\Sistema\DaoUsuario;

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
        $Produtos = $DaoProduto->get($filtro, true);
        
        $DaoUsuario = new DaoUsuario;
        foreach($Produtos as &$Produto)
        {
            $Produto->imagens = $DaoProduto->getImagens($Produto->id);

            $Produto->usuario = $DaoUsuario->get($Produto->id_usuario)[0];
            
            $Produto->usuario->removeAttr('senha')
                             ->removeAttr('uf')
                             ->removeAttr('email');
        }

        Response::getInstance()
				->setData($Produtos)
				->ok();
    }
}
