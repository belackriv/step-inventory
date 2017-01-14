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
            $sessionData = unserialize($sessionStr);

            if(
                isset($sessionData['_sf2_meta']) and
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
