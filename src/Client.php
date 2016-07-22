<?php
namespace outcomebet\casino25\api\client;

use JsonRPC\HttpClient;

class Client
{
	private $_client;

	public function __construct($config)
	{
		if(!array_key_exists('url', $config)) {
			throw new Exception("You must specify url for API");
		}
		$http = new HttpClient($config['url']);

		if(array_key_exists('ssl_verification', $config) && $config['ssl_verification'] == false) {
			$http->withoutSslVerification();
		}
		$http->withSslLocalCert($config['sslKeyPath']);
		$this->_client = new \JsonRPC\Client('', false, $http);
	}

	private function getClient() {
		return $this->_client;
	}

	public function listGames()
	{
		$response = $this->getClient()->execute('Game.List', []);
		return $response['Games'];
	}
}
