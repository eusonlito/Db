<?php
namespace ANS\Db;

class Db
{
    static private $settings = array();

    private $Cache;
    private $Mapper;

    public function __construct ($settings)
    {
        $this->settings = $settings;

        if (!$this->settings['db']['database'] || !$this->settings['db']['user']) {
            return false;
        }

        $this->setSettings();
    }

    /**
     * private function setSettings ([array $cache])
     *
     * return none
     */
    private function setSettings () {
        $this->setCache();
        $this->setLanguages();
        $this->setLanguage();
    }

    /**
     * public function setCache ([array $cache])
     *
     * return none
     */
    private function setCache ($cache = array())
    {
        if (!$cache) {
            $cache = $this->settings['cache'];
        }

        if ($cache['expire'] && $cache['interface']) {
            $this->Cache = new \ANS\Cache\Cache($cache);
        } else {
            $this->Cache = false;
        }
    }

    /**
     * public function setLanguages ([null $languages])
     *
     * return none
     */
    public function setLanguages ($languages = null)
    {
        $languages = is_null($languages) ? $this->settings['languages'] : $languages;

        if ($languages) {
            $this->settings['languages'] = is_array($languages) ? $languages : array($languages);
        } else {
            $this->settings['languages'] = array();
        }
    }

    /**
     * public function setLanguage ([null $language])
     *
     * return string
     */
    public function setLanguage ($language = null)
    {
        $language = is_null($language) ? $this->settings['language'] : $language;

        if ($language && in_array($language, $this->settings['languages'])) {
            $this->settings['language'] = $language;
        }
    }

    /**
     * public function getLanguage ([null $language])
     *
     * return string
     */
    public function getLanguage ($language = null)
    {
        $language = is_null($language) ? $this->settings['language'] : $language;

        if ($language && in_array($language, $this->settings['languages'])) {
            return $language;
        } else {
            return '';
        }
    }

    /**
    * public function getAvailableDrivers ()
    *
    * return array
    */
    public function getAvailableDrivers ()
    {
        return \PDO::getAvailableDrivers();
    }

    /**
     * public function connect (void)
     *
     * return boolean
     */
    public function connect ()
    {
        if ($this->PDO) {
            return true;
        }

        if (!$this->settings['db']['database'] || !$this->settings['db']['user'] || !$this->settings['db']['driver']) {
          throw new \InvalidArgumentException('Does not exists database configuration');
        }

        if (!in_array($this->settings['db']['driver'], $this->getAvailableDrivers())) {
            throw new \InvalidArgumentException('Sorry but "%s" database is not supported', $this->settings['db']['driver']);
        }

        $class = '\\ANS\\Db\\Drivers\\'.ucfirst($this->settings['db']['driver']);

        if (class_exists($class)) {
            $this->Driver = new $class($this);
        } else {
            throw new \InvalidArgumentException(sprintf('Sorry but don\'t exists any interface with name %s', $this->settings['db']['driver']));
        }

        try {
            $this->Mapper = new \Respect\Relational\Mapper(new \PDO(
                $this->Driver->getDSN($this->settings['db']),
                $this->settings['db']['user'],
                $this->settings['db']['password'],
                $this->settings['db']['options']
            ));
        } catch (\PDOException $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        return true;
    }

    /**
    * private function getCacheKey (array $options)
    *
    * return boolean
    */
    private function getCacheKey ($options)
    {
        if ($this->Cache) {
            return md5(serialize($options));
        }
    }

    /**
    * private function existsCache (string $cache_key)
    *
    * return boolean
    */
    private function existsCache ($cache_key)
    {
        if ($this->Cache) {
            return $this->Cache->exists($cache_key);
        }
    }

    /**
    * private function getCache (array $options)
    *
    * return boolean
    */
    private function getCache ($options)
    {
        if (!$this->Cache) {
            return false;
        }

        if (!isset($options['cache'])) {
            $options['cache'] = $this->Cache->getSettings('expire');
        }

        if (!$options['cache']) {
            return false;
        }

        $cache_key = $this->getCacheKey($options);

        if (!$this->existsCache($cache_key)) {
            return false;
        }

        return $this->Cache->get($cache_key);
    }

    /**
    * private function putCache (array $options, array $result)
    *
    * return boolean
    */
    private function putCache ($options, $result)
    {
        if (!$this->Cache) {
            return false;
        }

        if (!isset($options['cache'])) {
            $options['cache'] = $this->Cache->getSettings('expire');
        }

        if (!$options['cache']) {
            return false;
        }

        $cache_key = $this->getCacheKey($options);

        return $this->Cache->set($cache_key, $result, $options['cache']);
    }

    /**
     * public function select (array $options)
     *
     * return array
     */
    public function select ($options = array())
    {
        if (!$this->connect()) {
            return false;
        }

        if ($cached = $this->getCache($options)) {
            return $cached;
        }

        if ($options['limit'] == 1) {
            $result = current($result);
        }

        $this->putCache($options, $result);

        return $result;
    }

    /**
     * function queryRegister ([int $offset], [int $length])
     *
     * Return executed queries
     *
     * return array
     */
    public function queryRegister ($offset = 0, $length = null)
    {
        if ($offset || $length) {
            return array_slice($this->query_register, $offset, $length, true);
        }

        return $this->query_register;
    }

    /**
     * function clearQueryRegister ()
     *
     * Clear all queries registered
     */
    public function clearQueryRegister ($offset = 0, $length = null)
    {
        $this->query_register = array();
    }
}

if (!spl_autoload_functions()) {
    include (__DIR__.'/../../autoload.php');
}
