<?php

namespace ClickBox;

use \ClickBox\Clickbox;

abstract class Request
{

	private $_url;
	private $_endpoint;
	private $_user;
	private $_key;
	private $_method;
	private $_headers;
	private $_body;
	private $_response;
	private $_timeout = 10;

	public function __construct()
	{
		$this->_user = ClickBox::$apiUser;
		$this->_key  = ClickBox::$apiKey;
	}

	private function _config($headers = NULL, $body = NULL)
	{
		$this->_url = ClickBox::$apiUrl . $this->_endpoint;
		
		$this->_headers = [
			'API-USER: ' . $this->_user,
			'API-KEY: ' . $this->_key,
			'Accept: application/json'
		];
		
		switch ($this->_endpoint)
		{
			case 'sell/tae':
			case 'sell/packs':
			case 'sell/services':
				$this->_headers[] = 'Content-Type: application/json';
				$this->_method = 'POST';
				$this->_body = json_encode($body);

				break;

			case 'reports/sales':
			case 'reports/purchases':
				$start = (isset($headers['Start']) ? $headers['Start'] : NULL);
				$end = (isset($headers['End']) ? $headers['End'] : NULL);

				if (is_null($start) && ! is_null($end))
					throw new Exception('Debes establecer Start.');

				if (( ! is_null($start) && ! is_null($end)) && $start > $end)
					throw new Exception('Start debe ser menor que End.');

				if ( ! is_null($start) && ! is_null($end))
				{
					$diff = strtotime($end) - strtotime($start);
					$days = floor((($diff / 60) / 60) / 24);

					if ($days > 31)
					throw new Exception('El periodo de busqueda no puede ser mayor a 31 días.');
				}

				if ( ! is_null($start))
					$this->_headers[] = 'Start: ' . $start;
				
				if ( ! is_null($end))
					$this->_headers[] = 'End: ' . $end;
				else
					$this->_headers[] = 'End: ' . $start;

				$this->_method = 'GET';
				break;
			
			case 'query':
				if ( ! isset($headers['UUID']))
					throw new Exception('Debes enviar el UUID de la Transacción a Consultar.');

				$this->_headers[] = 'UUID: ' . $headers['UUID'];
				$this->_method = 'GET';
				break;
			
			case 'balance':
				$this->_method = 'GET';
				break;

			default:
				throw new Exception('Este no es un Endpoint válido.');
				break;
		}
	}

	private function _execute()
	{
		$curl = curl_init();

		$opts = [
			CURLOPT_URL            => $this->_url,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_CUSTOMREQUEST  => $this->_method,
			CURLOPT_HTTPHEADER     => $this->_headers,
			CURLOPT_TIMEOUT        => $this->_timeout
		];

		if ($this->_method === 'POST')
		{
			$opts[CURLOPT_POSTFIELDS] = $this->_body;
		}

		curl_setopt_array($curl, $opts);

		$responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$response     = curl_exec($curl);
		$error 	      = curl_error($curl);

		curl_close($curl);

		if ($error)
			throw new Exception('Ocurrió un Error: ' . $error);

		$response = json_decode($response);

		return $response;
	}

	public function create($endpoint)
	{
		$this->_endpoint = $endpoint;

		$this->_config();
		
		return $this->_execute();
	}
	
}