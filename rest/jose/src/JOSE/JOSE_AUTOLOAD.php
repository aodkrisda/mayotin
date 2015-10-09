<?php
class JOSE_AUTOLOAD {
    static function autoload($className){
    	$className = str_replace('\\','/',$className);
    	if(strpos($className,'.php')===false){
    	$className.='.php';
    	}
    	$fileName=__DIR__ . '/'.$className;
        if (file_exists($fileName)) {
            require $fileName;
        }
    }
    static function register_autoload(){
		spl_autoload_register("JOSE_AUTOLOAD::autoload");
	}
}