<?php
/**
 * Created by PhpStorm.
 * User: behcetmutlu
 * Date: 29/09/15
 * Time: 10:24
 */

namespace EnuygunCom\DfpBundle\Model;


use Doctrine\DBAL\Connection;
use Symfony\Component\HttpKernel\Kernel;

class Settings extends TargetContainer
{
    const CacheLifeTime = 15 * 60; // 15 minutes
    protected $publisherId;
    protected $divClass;
    protected $enabled = true;
    protected $env = 'dev';
    protected $conn;
    protected $memcached;
    protected $cache = array();

    /**
     * @param int $publisherId
     * @param int $divClass
     * @param array $targets
     * @param Kernel $kernel
     * @param Connection $conn
     * @param \Memcached $memcached
     */
    public function __construct($publisherId, $divClass, array $targets = array(), Kernel $kernel, Connection $conn, \Memcached $memcached)
    {
        $this->setPublisherId($publisherId);
        $this->setDivClass($divClass);
        $this->setTargets($targets);
        $this->conn = $conn;
        $this->memcached = $memcached;

        $this->env = $kernel->getEnvironment();

        if($this->env == 'dev') {
            $this->enabled = false;
        }
    }

    /**
     * Get the publisher id.
     *
     * @return string
     */
    public function getPublisherId()
    {
        return $this->publisherId;
    }

    /**
     * Set the publisher id.
     *
     * @param string $publisherId
     */
    public function setPublisherId($publisherId)
    {
        $this->publisherId = $publisherId;
    }

    /**
     * Get the divClass.
     *
     * @return string
     */
    public function getDivClass()
    {
        return $this->divClass;
    }

    /**
     * Set the divClass.
     *
     * @param string $divClass
     */
    public function setDivClass($divClass)
    {
        $this->divClass = $divClass;
    }


    /**
     * This method applies target configuration overwriting the default target configuration
     *
     * @param array $targets
     * @return array
     */
    public function applyTargets($targets = array()) {

        $_targets = $this->getTargets();

        foreach($targets as $key => $value) {
            $_targets[$key] = $value;
        }

        return $_targets;
    }

    public function isActive($path, $targets, $init = null) {
        if(! $this->enabled)
            return false;

        $targets = $this->applyTargets($targets);

        $cacheKey = sprintf('dfp_settings.%s.%s.%s', $targets['modul'], $targets['sub_modul'], $path);
        $ownCacheKey = sprintf('dfp_settings.%s.%s', $targets['modul'], $targets['sub_modul']);

        $cache = $this->memcached->get($cacheKey);

        if($cache !== false) {
            return $cache == 'true' ? true : false;
        }

        $return = false;

        // self cache
        if(array_key_exists($ownCacheKey, $this->cache)) {

            $jsonSettings = $this->cache[$ownCacheKey];

        } else {

            $selectQuery = sprintf('SELECT * FROM dfp_settings WHERE modul = :modul AND sub_modul %s', $targets['sub_modul'] === null ? ' is null' : '= :sub_modul');

            $jsonSettings = $this->conn->fetchAssoc($selectQuery, $targets);

            $this->cache[$ownCacheKey] = $jsonSettings;
        }



        if(! empty($jsonSettings)) {
            $publishSettings = json_decode($jsonSettings['settings'], true);

            if(json_last_error() != false) {

                $return =  false;

            }
            else {

                if(array_key_exists($path, $publishSettings)) {
                    $return =  $publishSettings[$path] === true;

                } elseif($init !== null) {
                    $this->conn->executeUpdate('UPDATE dfp_settings SET settings = :settings WHERE modul = :modul and sub_modul '. ($targets['sub_modul'] === null ? ' is null' : '= :sub_modul') .' LIMIT 1', array_merge(array('settings' => json_encode(array_merge(array($path => $init ? true : false), $publishSettings))), $targets));

                    $return =  $init ? true : false;
                }
            }

        } elseif($init !== null) {
            $this->conn->executeUpdate('INSERT INTO dfp_settings (modul, sub_modul, settings) VALUES (:modul, :sub_modul, :settings)', array_merge(array('settings' => json_encode(array($path => $init ? true : false))), $targets));
            $return =  $init ? true : false;
        }

        $this->memcached->set($cacheKey, $return ? 'true' : 'false', self::CacheLifeTime);

        $publishSettings[$path] = $return;

        $this->cache[$ownCacheKey]['settings'] = json_encode($publishSettings);

        return $return;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function getEnvironment()
    {
        return $this->env;
    }
}