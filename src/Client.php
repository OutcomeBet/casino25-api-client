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
		$http->withDebug();

		if(array_key_exists('ssl_verification', $config) && $config['ssl_verification'] == false) {
			$http->withoutSslVerification();
		}
		$http->withSslLocalCert($config['sslKeyPath']);
		$this->_client = new \JsonRPC\Client(null, false, $http);
	}

	private function getClient()
	{
		return $this->_client;
	}

	/**
	 * @param string $method
	 * @param array $params
	 * @return array
	 */
	private function execute($method, $params)
	{
		return $this->getClient()->execute($method, $params);
	}

	/**
	 * Returns a list of available games
	 *
	 * @return array
	 */
	public function listGames()
	{
		return $this->getClient()->execute('Game.List', []);
	}

	/**
	 * Creates a bank group
	 *
	 * @param array $bankGroup
	 * @return array
	 */
	public function createBankGroup($bankGroup)
	{
		Helper::requiredParam($bankGroup, 'Id', ParamType::STRING);
		Helper::requiredParam($bankGroup, 'Currency', ParamType::STRING);

		return $this->execute('BankGroup.Create', $bankGroup);
	}

	/**
	 * Creates a player
	 *
	 * @param array $player
	 * @return array
	 */
	public function createPlayer($player)
	{
		Helper::requiredParam($player, 'Id', ParamType::STRING);
		Helper::optionalParam($player, 'Nick', ParamType::STRING);
		Helper::requiredParam($player, 'BankGroupId', ParamType::STRING);

		return $this->execute('Player.Create', $player);
	}

	/**
	 * Creates a game session
	 *
	 * @param array $session
	 * @return array
	 */
	public function createSession($session)
	{
		Helper::requiredParam($session, 'PlayerId', ParamType::STRING);
		Helper::requiredParam($session, 'GameId', ParamType::STRING);
		Helper::optionalParam($session, 'RestorePolicy', ParamType::STRING, function($params, $key, $type) {
			Helper::strictValues($params, $key, array('Restore', 'Create', 'Last'));
		});
		Helper::optionalParam($session, 'StaticHost', ParamType::STRING);

		return $this->execute('Session.Create', $session);
	}

	/**
	 * Creates a demo session
	 *
	 * @param $demoSession
	 * @return array
	 */
	public function createDemoSession($demoSession)
	{
		Helper::requiredParam($demoSession, 'GameId', ParamType::STRING);
		Helper::requiredParam($demoSession, 'BankGroupId', ParamType::STRING);
		Helper::optionalParam($demoSession, 'StartBalance', ParamType::INTEGER);
		Helper::optionalParam($demoSession, 'StaticHost', ParamType::STRING);

		return $this->execute('Session.CreateDemo', $demoSession);
	}

	/**
	 * Closes the specified session
	 *
	 * @param $session
	 * @return array
	 */
	public function closeSession($session)
	{
		Helper::requiredParam($session, 'SessionId', ParamType::STRING);

		return $this->execute('Session.Close', $session);
	}

	/**
	 * Provides information about specified session
	 *
	 * @param $session
	 * @return array
	 */
	public function getSession($session)
	{
		Helper::requiredParam($session, 'SessionId', ParamType::STRING);

		return $this->execute('Session.Get', $session);
	}

	/**
	 * Returns a filtered list of sessions
	 *
	 * @param $filters
	 * @return array
	 */
	public function listSessions($filters)
	{
		Helper::optionalParam($filters, 'CreateTimeFrom', ParamType::TIMESTAMP);
		Helper::optionalParam($filters, 'CreateTimeTo', ParamType::TIMESTAMP);
		Helper::optionalParam($filters, 'CloseTimeFrom', ParamType::TIMESTAMP);
		Helper::optionalParam($filters, 'CloseTimeTo', ParamType::TIMESTAMP);
		Helper::optionalParam($filters, 'Status', ParamType::STRING, function($params, $key, $type) {
			Helper::strictValues($params, $key, array('Open', 'Closed'));
		});
		Helper::optionalParam($filters, 'PlayerIds', ParamType::STRINGS_ARRAY);
		Helper::optionalParam($filters, 'BankGroupIds', ParamType::STRINGS_ARRAY);

		return $this->execute('Session.List', $filters);
	}
}
