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

		foreach(Request::getData()->imagens as $imagem)
		{
			if(!is_null($imagem))
			{
				$time = time();
				$filename = ($time + rand(1, 9)) . substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 4);
				$Produto->imagens = $this->base64_to_jpeg($imagem, $filename . '.jpg');
			}
		}
		
		try{
			$idProduto = $DaoProduto->save();
			
			Response::getInstance()
					->created();

		} catch (\Exception $e) {
			Response::getInstance()
					->badRequest('Wooops! Não foi possível salvar.');
		}		
	}

	private function base64_to_jpeg($base64_string, $output_file) {
		// open the output file for writing
		$ifp = fopen( '../app/www/img/' . $output_file, 'wb' ); 

		// split the string on commas
		// $data[ 0 ] == "data:image/png;base64"
		// $data[ 1 ] == <actual base64 string>
		$data = explode( ',', $base64_string );
	
		
		// we could add validation here with ensuring count( $data ) > 1
		fwrite( $ifp, base64_decode(str_replace(' ', '+', $data[1])));
		
		// clean up the file resource
		fclose( $ifp ); 
	
		return $output_file; 
	}
}
