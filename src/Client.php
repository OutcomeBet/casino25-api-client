<?php
namespace outcomebet\casino25\api\client;

use JsonRPC\HttpClient;

class Client
{
	/**
	 * @var \JsonRPC\Client
	 */
	private $_client;

	/**
	 * Client constructor.
	 *
	 * Config array:
	 *  - url              string JSON-RPC 2.0 Server
	 *  - ssl_verification boolean Certificate verification of HTTP connection over TLS
	 *
	 * @param array $config
	 * @throws Exception
	 */
	public function __construct($config)
	{
		if(!array_key_exists('url', $config))
		{
			throw new Exception("You must specify url for API");
		}
		$http = new HttpClient($config['url']);

		if(array_key_exists('ssl_verification', $config) && $config['ssl_verification'] == false)
		{
			$http->withoutSslVerification();
		}
		$http->withSslLocalCert($config['sslKeyPath']);
		$this->_client = new \JsonRPC\Client(null, false, $http);
	}

	/**
	 * @return \JsonRPC\Client
	 */
	private function getClient()
	{
		return $this->_client;
	}

	/**
	 * @param string $method
	 * @param array $params
	 * @return array
	 */
	private function execute($method, $params = array())
	{
		return $this->getClient()->execute($method, $params);
	}

	/**
	 * Returns a list of available games
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#gamelist
	 *
	 * @return array
	 */
	public function listGames()
	{
		return $this->execute('Game.List');
	}

	/**
	 * Creates a bank group
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#bankgroupcreate
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
	 * Applies a template to a bank group
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#bankgroupapplysettingstemplate
	 *
	 * @param array $bankGroup
	 * @return array
	 */
	public function applySettingsTemplate($bankGroup)
	{
		Helper::requiredParam($bankGroup, 'BankGroupId', ParamType::STRING);
		Helper::requiredParam($bankGroup, 'SettingsTemplateId', ParamType::STRING);

		return $this->execute('BankGroup.ApplySettingsTemplate', $bankGroup);
	}

	/**
	 * Creates a player
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#playercreate
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
	 * Returns current player balance
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#balanceget
	 *
	 * @param array $player
	 * @return array
	 */
	public function getBalance($player)
	{
		Helper::requiredParam($player, 'PlayerId', ParamType::STRING);

		return $this->execute('Balance.Get', $player);
	}

	/**
	 * Changes a specified player balance
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#balancechange
	 *
	 * @param array $player
	 * @return array
	 */
	public function changeBalance($player)
	{
		Helper::requiredParam($player, 'PlayerId', ParamType::STRING);
		Helper::requiredParam($player, 'Amount', ParamType::INTEGER);

		return $this->execute('Balance.Change', $player);
	}

	/**
	 * Creates a game session
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#sessioncreate
	 *
	 * @param array $session
	 * @return array
	 */
	public function createSession($session)
	{
		Helper::requiredParam($session, 'PlayerId', ParamType::STRING);
		Helper::requiredParam($session, 'GameId', ParamType::STRING);
		Helper::optionalParam($session, 'RestorePolicy', ParamType::STRING, function($params, $key, $type)
		{
			Helper::strictValues($params, $key, array('Restore', 'Create', 'Last'));
		});
		Helper::optionalParam($session, 'StaticHost', ParamType::STRING);

		return $this->execute('Session.Create', $session);
	}

	/**
	 * Creates a demo session
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#sessioncreatedemo
	 *
	 * @param array $demoSession
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
	 * Closes a specified session
	 *
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#sessionclose
	 *
	 * @param array $session
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
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#sessionget
	 *
	 * @param array $session
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
	 * @link https://github.com/OutcomeBet/casino25-api-client/wiki/API-Documentation#sessionlist
	 *
	 * @param array $filters
	 * @return array
	 */
	public function listSessions($filters = array())
	{
		Helper::optionalParam($filters, 'CreateTimeFrom', ParamType::TIMESTAMP);
		Helper::optionalParam($filters, 'CreateTimeTo', ParamType::TIMESTAMP);
		Helper::optionalParam($filters, 'CloseTimeFrom', ParamType::TIMESTAMP);
		Helper::optionalParam($filters, 'CloseTimeTo', ParamType::TIMESTAMP);
		Helper::optionalParam($filters, 'Status', ParamType::STRING, function($params, $key, $type)
		{
			Helper::strictValues($params, $key, array('Open', 'Closed'));
		});
		Helper::optionalParam($filters, 'PlayerIds', ParamType::STRINGS_ARRAY);
		Helper::optionalParam($filters, 'BankGroupIds', ParamType::STRINGS_ARRAY);

		return $this->execute('Session.List', $filters);
	}
}
