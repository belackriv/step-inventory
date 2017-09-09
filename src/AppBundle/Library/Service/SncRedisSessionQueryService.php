<?php
namespace AppBundle\Library\Service;

use AppBundle\Entity\Organization;
use AppBundle\Library\Session\LoadableMetadataBag;

use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;

class SncRedisSessionQueryService
{
	const SESSION_PREFIX = 'session';
	const INVALID_SESSION_SUFFIX = '_INVALID';

	protected $redisClient;

	public function __construct(\Predis\Client $redisClient)
	{
		$this->redisClient = $redisClient;
	}

	public function getSessionCount(Organization $org)
	{
		$sessionLimitEntries = $this->redisClient->hgetall($this->getOrgKey($org));
		$sessionCount = 0;
		$thirtyMinutesAgo = new \DateTime('-30 minute');

		foreach($sessionLimitEntries as $sessionKey => $timeStr){
			if($timeStr !== 'true'){
				$time = new \DateTime($timeStr);
				if($time < $thirtyMinutesAgo){
					$this->redisClient->hdel($this->getOrgKey($org), $sessionKey);
				}else{
					$sessionCount++;
				}
			}
		}

		return $sessionCount;
	}

	public function getSessionKeys(Organization $org)
	{
		$sessionKeys = [];
		$sessionLimitEntries = $this->redisClient->hgetall($this->getOrgKey($org));
		foreach($sessionLimitEntries as $sessionKey => $timeStr){
		  $sessionKeys[] = $sessionKey;
		}

		return $sessionKeys;
	}

	public function getSessions(\SessionHandlerInterface $sessionHandler, SessionStorageInterface $sessionStorage, Organization $org)
	{
		$sessions = [];
		$sessionKeys = $this->getSessionKeys($org);
		foreach($sessionKeys as $sessionKey){
			$sessionStr = $sessionHandler->read($sessionKey);
			$sessionData = $this->unserializeSession($sessionStr);

			if( isset($sessionData['_sf2_meta']) and
				isset($sessionData['_sf2_attributes']) and
				isset($sessionData['_sf2_attributes']['_security_site'])
			){
				$startedAt = new \DateTime();
				$startedAt->setTimestamp($sessionData['_sf2_meta'][MetadataBag::CREATED]);

				$updatedAt = new \DateTime();
				$updatedAt->setTimestamp($sessionData['_sf2_meta'][MetadataBag::UPDATED]);

				$forUsername = null;
				$userToken = unserialize($sessionData['_sf2_attributes']['_security_site']);
				if($userToken){
					$forUsername= $userToken->getUser()->getUsername();
				}

				$sessions[] = [
					'id' => $sessionKey,
					'startedAt' => $startedAt,
					'updatedAt' => $updatedAt,
					'forUsername' => $forUsername
				];
			}
		}
		return $sessions;
	}

	public static function unserializeSession($sessionStr) {
		$method = ini_get("session.serialize_handler");
		switch($method){
			case "php":
				return self::unserializePhpSessionString($sessionStr);
				break;
			case "php_binary":
				return self::unserializePhpSessionBinary($sessionStr);
				break;
			case 'php_serialize':
				return unserialize($sessionStr);
				break;
			default:
				throw new \Exception("Unsupported session.serialize_handler: " . $method . ". Supported: php, php_binary");
		}
	}

	private static function unserializePhpSessionString($sessionStr)
	{
		$sessionData = [];
		$offset = 0;
		while ($offset < strlen($sessionStr)) {
			if (!strstr(substr($sessionStr, $offset), '|')) {
				throw new \Exception('invalid data, remaining: ' . substr($sessionStr, $offset));
			}
			$pos = strpos($sessionStr, '|', $offset);
			$num = $pos - $offset;
			$varname = substr($sessionStr, $offset, $num);
			$offset += $num + 1;
			$data = unserialize(substr($sessionStr, $offset));
			$sessionData[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $sessionData;
	}

	private static function unserializePhpSessionBinary($sessionStr){
		$sessionData = [];
		$offset = 0;
		while ($offset < strlen($sessionStr)) {
			$num = ord($sessionStr[$offset]);
			$offset += 1;
			$varname = substr($sessionStr, $offset, $num);
			$offset += $num;
			$data = unserialize(substr($sessionStr, $offset));
			$sessionData[$varname] = $data;
			$offset += strlen(serialize($data));
		}
		return $sessionData;
	}

	public function destroySession($sessionKey, Organization $org)
	{
		$this->redisClient->hdel($this->getOrgKey($org), $sessionKey);
		$this->redisClient->set(self::SESSION_PREFIX.$sessionKey.self::INVALID_SESSION_SUFFIX, 'true');
		$this->redisClient->del(self::SESSION_PREFIX.$sessionKey);
	}

	public function isSessionInvalid($sessionKey)
	{
		return $this->redisClient->exists(self::SESSION_PREFIX.$sessionKey.self::INVALID_SESSION_SUFFIX);
	}

	public function getOrgKey(Organization $org)
	{
		return $key = 'org#'.$org->getId();
	}
}
