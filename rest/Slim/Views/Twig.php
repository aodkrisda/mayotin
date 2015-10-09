<?php

namespace Slim\Views;

class Twig extends \Slim\View
{
	static private $_instance=null;
    static public function getInstance(){
    	if(!self::$_instance){
	        $dir=__DIR__.'/';

		    //require_once ($dir. '../../Twig/Autoloader.php');
		    //Twig_Autoloader::register();

		    $loader = new \Twig_Loader_Filesystem($dir.'../../templates/');
		    self::$_instance = new \Twig_Environment($loader, array(
		        'xxxcache' => $dir.'/cache/',
		    ));
	    }
	    return self::$_instance;
    }
    protected function render($template, $data = null)
    {
    	$data = array_merge($this->data->all(), (array) $data);
		return self::getInstance()->render($template, $data);
    }
}
