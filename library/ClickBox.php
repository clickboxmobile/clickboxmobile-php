<?php

if ( ! function_exists('curl_init'))
{
    throw new Exception('Para utilizar ClickBox PHP es necesaria la extensión CURL.');
}

if ( ! function_exists('json_decode'))
{
	throw new Exception('Para utilizar ClickBox PHP es necesaria la extensión JSON.');
}

if ( !function_exists('get_called_class'))
{
	throw new Exception('Para utilizar ClickBox PHP es necesaria la versión de PHP >= 5.6.0.');
}

require_once dirname(__FILE__) . '/ClickBox/Library.php';
require_once dirname(__FILE__) . '/ClickBox/Request.php';