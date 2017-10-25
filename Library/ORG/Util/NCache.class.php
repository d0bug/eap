<?php
class NCache {
    private static $cachePools = array();
    private $instance = null;

    /**
     *
     * @param Array $cacheConfig
     * @return NCache
     * @throws Exception
     */
    public static  function getCache($cacheConfig=array()) {

        if (false == $cacheConfig) {
            $cacheConfig = C('CACHE_CONFIG');
        }
        $confKey = md5(serialize($cacheConfig));
        if (false == self::$cachePools[$confKey]) {
            $nCache = new NCache();
            try{
                $cache = Cache::getInstance($cacheConfig['cacheType'], $cacheConfig);
                $nCache->instance = $cache;
                self::$cachePools[$confKey] = $nCache;
            } catch(Exception $e){
                throw new Exception('缓存设置错误，无法获取缓存实例');
            }
        }
        return self::$cachePools[$confKey];
    }

    public function set($prefix, $name, $value) {
        $key = $this->getKey($prefix, $name);
        $value = array('time'=>time(), 'value'=>$value);
        $this->instance->set($key, $value);
    }

    public function get($prefix, $name) {
        $key = $this->getKey($prefix, $name);

        $value = $this->instance->get($key);
        if ($value && $value = $this->getValue($prefix, $value)) {
            return $value;
        }
        return null;
    }

    public function delete($prefix, $name=''){
        if ($name) {
            $key = $this->getKey($prefix, $name);
            $this->instance->rm($key);
        } else {
        	$prefix = $this->getPrefix($prefix);
            $nsKey = $this->getNsKey($prefix);
            $this->instance->set($nsKey, time());
        }
    }

    private function getValue($prefix, $value) {
        static $prefixTimes = array();
        $prefix = $this->getPrefix($prefix);
        if (false == $value['time']) {
            return $value;
        }

        if (false == $prefixTimes[$prefix]) {
            $nsKey = $this->getNsKey($prefix);
            $prefixTimes[$prefix] = $this->instance->get($nsKey);
        }
        $cacheTime = $value['time'];
        if ($cacheTime <= $prefixTimes[$prefix]) {
            return null;
        }
        return $value['value'];
    }

    private function getNsKey($prefix) {
        return strtoupper('NS_' . $prefix);
    }

    private function getPrefix($prefix) {
        return strtoupper(APP_NAME . '_' . $prefix);
    }

    private function getKey($prefix, $name) {
    	#return $this->getPrefix($prefix) . '_' . $name;
        return md5($this->getPrefix($prefix) . "\t" . strtoupper($name));
    }

};
?>
