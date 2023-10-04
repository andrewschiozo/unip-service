<?php
namespace util;

class Ldap
{
	private $server;
	private $port;
	private $domain;
	private $user;
	private $pass;

	private $conn;
	private $auth = false;
	private $baseDn;

	/**
	 * Conecta com AD
	 * 
	 * @param string $server
	 * @param int 	 $port
	 * @param string $domain
	 * 
	 * @return void
	 */
	public function __construct($server, $port, $domain)
	{
		$this->server = $server;
		$this->port = $port;
		$this->domain = $domain;
		$this->baseDn = 'DC=' . implode(',DC=', explode(".", $domain));
		
		try
		{
			$this->conn = ldap_connect($this->server, $this->port);
			ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($this->conn, LDAP_OPT_REFERRALS, 0);
			ldap_set_option($this->conn, LDAP_OPT_SIZELIMIT, 0);
		} 
		catch(\Exception $e)
		{
			echo 'Erro ao conectar: ' . $e->getMessage();
		}
	}

	/**
	 * Autentica usuário
	 * 
	 * @param string $user Usuário do AD
	 * @param string $pass Senha do usuário
	 * 
	 * @return bool
	 */
	public function auth($user, $pass)
	{
		$this->user = $user;
		$this->pass = $pass;

		if(!@ldap_bind($this->conn, $user . '@' . $this->domain, $pass))
		{
			throw new \Exception('Credenciais inválidas');
		}
		
		$this->auth = true;
		return $this->auth;
	}

	/**
	 * Retorna as informações do usuário filtradas pelas chaves da variável $userKeys
	 * 
	 * @param string $user Usuário do AD a ser pesquisado
	 * @param array  $userKeys Itens do AD a serem retornados; * para todos ou vazio para $userKeyDefault
	 * 
	 * @return array
	 */
	public function getUserInfo($user, $userKeys = null)
	{
		$this->checkAuth();

		$userResult = [];
		$userKeysDefault = ['samaccountname', 'cn', 'name', 'mail', 'userprincipalname', 'title', 'description', 'company', 'department', 'manager', 'memberof'];
		$userKeys = (!is_null($userKeys) || $userKeys === '*') ? $userKeys : $userKeysDefault;

		$filter = '(&(samaccountname=' . $user . '))';
		$result = ldap_search($this->conn, $this->baseDn, $filter, $userKeys);
		$entries = ldap_get_entries($this->conn, $result);
		if($entries['count'] > 0)
		{
			$entries = $entries[0];
			foreach($userKeys as $key)
			{
				if($key === 'memberof')
				{
					$userResult[$key] = array_key_exists($key, $entries) ? $this->formatCNData($entries[$key]) : '';
					continue;
				}
				if($key === 'manager')
				{
					$userResult[$key] = array_key_exists($key, $entries) ? $this->formatCNData($entries[$key])[0] : '';
					continue;
				}
				$userResult[$key] = array_key_exists($key, $entries) ? $entries[$key][0] : '';
			}
		}
		return $userResult;
	}

	/**
	 * Retorna os grupos de um usuário do AD
	 * 
	 * @param string $user Usuário do AD a ser pesquisado
	 * 
	 * @return array
	 * */
	public function getUserMemberOf($user)
	{
		$this->checkAuth();

		$groups = [];
		$filter = '(&(samaccountname=' . $user . '))';
		$result = ldap_search($this->conn, $this->baseDn, $filter, ['memberof']);
		$entries = ldap_get_entries($this->conn, $result);
		if($entries['count'] > 0)
		{
			$entries = $entries[0]['memberof'];
			$groups = $this->formatCNData($entries);
		}
		return $groups;
	}

	/**
	 * Verifica se um usuário pertence a um grupo
	 * 
	 * @param string $user Usuário do AD
	 * @param string $group Grupo a verificar
	 * 
	 * @return bool 
	 */
	public function isMemberOf($user, $group)
	{
		$this->checkAuth();

		$groups = $this->getUserMemberOf($user);
		return in_array($group, $groups);
	}

	/**
	 * Formata strings que possuam prefixo CN= 
	 * 
	 * @param $data Array de strings a formatar
	 * 
	 * return array
	 */
	private function formatCNData($data)
	{
		$result = [];
		array_shift($data);
		foreach($data as $d)
		{
			$result[] = str_replace('CN=', '', explode(',', $d)[0]);
		}
		return $result;
	}

	/**
	 * Verifica se a conexão com o LDAP é autenticada
	 * 
	 * @return bool
	 */
	private function checkAuth()
	{
		if(!$this->auth)
		{
			throw new \Exception('Não autenticado');
		}
		return true;
	}
}