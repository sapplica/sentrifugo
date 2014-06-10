<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/
 
class sapp_HttpClient
{
	const URI = 'http://localhost/stubstats/services/services';
	const HTTP_METHOD = 'POST';
	
	protected $client;
	protected $secretKey;
	protected $appName;
	
	public function __construct($appName,$secretKey)
	{
		$this->client = new Zend_Http_Client();
		$this->appName = $appName;
		$this->secretKey = $secretKey;			
	}
	public function call($method,$args)
	{
		//http://localhost/stubstats/services/test/
		
		//echo self::URI ."/". $method;exit;
		
		$this->client->setUri(self::URI ."/". $method);
		
		$this->client->setParameterPost('appName',$this->appName);
	
		foreach($args as $key => $val)
		{
			$this->client->setParameterPost($key,$val);
		}
		
		$this->client->setParameterPost('auth', $this->signArguments($args));
		
		
		$result = $this->client->request(self::HTTP_METHOD);	

		//echo "<pre>";print_r($result);exit;
		
		return Zend_Json_Decoder::decode($result);
		
	}
	public function signArguments($args)
	{
		/* $args['appName'] = $this->appName;
		ksort($args);
		$signature = '';
		foreach($args as $key => $val)
		{
			$signature .= $key . $val;
		}
		
		//echo $this->signature;exit;
		return md5($this->secretKey . $signature); */		
		ksort($args);
		$signature = '';
		foreach($args as $key => $val)
		{
			$signature .= $key;
		}
		 
		//f229e1329e659c4011540b04a22a29d3
		//echo $signature.$this->secretKey;exit;
		//echo md5($signature.$this->secretKey);exit;
		return md5($signature.$this->secretKey);
	}
	
}

