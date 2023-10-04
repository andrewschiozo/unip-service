<?php
namespace util;

use controller;

class FTP
{
	private $conn;
	private $server;
	private $user;
	private $pass;

	public function __construct($server, $user, $pass)
	{
		try
		{
			$this->conn = ftp_connect($server);
			$login = ftp_login($this->conn, $user, $pass);
			ftp_pasv($this->conn, true);
		}
		catch (\Exception $e)
		{
			var_dump($e);
		}
	}

	public function listItems($path = '.')
	{
		$file_list = ftp_nlist($this->conn, $path);
		return $file_list;
	}

	public function get($server_file, $local_file)
	{
		if(@ftp_get($this->conn, $local_file, $server_file, FTP_ASCII)){
			return $local_file;
		}
		else
		{
			throw new \Exception("Error downloading.\n" . error_get_last()['message']);
		}
	}

	public function __destruct()
	{
		ftp_close($this->conn);	
	}
}