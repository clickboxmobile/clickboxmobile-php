<?php

namespace ClickBox;

abstract class ClickBox
{

	public static $apiEnviroment;
	public static $apiSandBoxUrl = 'https://api.clickbox.app/ws/';
	public static $apiProductionUrl = 'https://api.clickbox.app/sandbox/';
	public static $apiUrl;
	public static $apiUser;
	public static $apiKey;
	public static $apiVersion = '1.0.0';

	public static function setEnviroment(string $enviroment)
	{
		self::$apiUrlEnviroment = $enviroment;

		switch($enviroment)
		{
			case 'sandbox':
				self::$apiUrl = self::$apiSandBoxUrl;
				break;
			
			case 'production':
				self::$apiUrl = self::$apiProductionUrl;
				break;

			default:
				throw new Exception('Este no es un Ambiente válido.');
				break;
		}
	}

	public static function setAuth(string $apiUser, string $apiKey)
	{
		self::$apiUser = $apiUser;
		self::$apiKey = $apiKey;
	}
	
}