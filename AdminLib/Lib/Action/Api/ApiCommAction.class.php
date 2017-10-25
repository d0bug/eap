<?php
include(CONF_PATH . '/const.php');
import('COM.SysUtil');
import('COM.Dao.Dao');
class ApiCommAction extends Action {
    public function __construct() {
        parent::__construct();
        if($_GET['run']) {
        	$args = explode('_', $_GET['run']);
        	$method = array_shift($args);
        	eval('print_r($this->' . $method . '("' . implode('","', $args) . '"));');
        	exit;
        }
    }
    
    final public function index() {
    	$apiGzip = C('API_GZIP');
    	$commClass = new ReflectionClass(__CLASS__);
    	$commMethods = $commClass->getMethods(ReflectionMethod::IS_PROTECTED);
    	$commMethodArray = array();
    	foreach ($commMethods as $method) {
    		$commMethodArray[$method->name] = true;
    	}
        $reflector = new ReflectionClass($this);
        $methods = $reflector->getMethods(ReflectionMethod::IS_PROTECTED);
        require_once(VENDOR_PATH . '/phpRPC/phprpc_server.php');
        $server = new PHPRPC_Server();
        $server->setCharset('UTF-8');  
        $server->setDebugMode(true);  
        $server->setEnableGZIP($apiGzip);
        
        foreach($methods as $method) {
            $actionName = $method->name;
            if(false == isset($commMethodArray[$actionName])) {
                $server->add($actionName, $this);
            }
        }
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
        	if (false == $_GET['key'] || $_GET['key'] != C('API_KEY')) {
        		die('error');
        	}
        }
        $server->start();
    }
    
    public function __call($method, $params) {
    	return call_user_method_array($method, $this,$params);
    }
}

?>
