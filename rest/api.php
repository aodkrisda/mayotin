<?php
/* fix input of CGI/FastCGI */
if(strpos($_SERVER["SERVER_SOFTWARE"],'nginx/')!==false){
if(!isset($_SERVER['PATH_INFO'])){
	$a=explode('.php',$_SERVER["PHP_SELF"]);
	if((count($a)>1)){
		$_SERVER["SCRIPT_NAME"]=$a[0] .'.php';
		$_SERVER['PATH_INFO']=$a[1];
	}
	unset($a);
}
}
require_once 'Slim/Slim.php';
require_once 'NotORM/lib.php';
require_once 'Util/Session.php';
\Slim\Slim::registerAutoloader();


//create application
class AppUser{
	public $user;

	public function setUser($user){
		$this->user=$user;
	}
	public function getUser(){
		return $this->user;
	}
	public function getUserId(){
		if($this->user){
			return $this->user['user_id'];
		}
		return null;
	}
	public function getRoleId(){
		$str=null;
		if($this->user && isset($this->user['user_type'])){
			$str=$this->user['user_type'];
		}
		return $str;
	}	
	public function getRole(){
		$str=null;
		if($this->user && isset($this->user['user_type'])){
			switch($this->user['user_type']){
				case '0':
					$str= 'student';
					break;
				case '1':
					$str='admin';
					break;
				case '2':
					$str='teacher' ;
					break;
				case '3':
					$str='manager';
					break;
				case '4':
					$str='director';
					break;
			}
		}
		return $str;
	}
}

class App extends \Slim\Slim{
	public $API_PREFIX = 'api_';
	
	static public function Authenticate($route){
		$app=\Slim\Slim::getInstance();
		try{
			$args=$route->getParam('args');
			if($args && in_array($args[0], array('login','register','getlookups'))){
				return;
			}
		}catch(Exception $e){}
		
		if($app->auth->getUser()){
			return;
		}

		$username = $app->request()->headers('PHP_AUTH_USER');
		$password = $app->request()->headers('PHP_AUTH_PW');

		if (false && isset($username) && isset($password)) {
		    $rs=$app->orm->user->where(array('user_number'=>$username, 'password '=>$password))->limit(1);
		    if(count($rs)){  
		      $user=$app->orm->toArray($rs)[0];
		      if($user){
		        unset($user['password']);
		        $app->auth->setUser($user);
		        $app->session->set('_auth_',$app->auth->getUser());
		        return;
		      }
		    }
		}else{
			$user=$app->session->get('_auth_');
			if($user){
				$app->auth->setUser($user);
				return;
			}
		}
		$app->writeJSON(null, 401, 'Unauthorized');
	}
	
	public function __construct(array $userSettings = array()){
		parent::__construct($userSettings);
		$this->container->singleton('auth', function ($c) {
			return new AppUser();
		});
		$this->container->singleton('orm', function ($c) {
			return NotORM::getInstance();
		});
		$this->container->singleton('session', function ($c) {
			return new Session();
		});		
	}
	
	public function writeJSON($data,$status=200, $error=false){
		$response = $this->response();
		$response['Content-Type'] = 'application/json';
		
		$out=false;
		if($data && isset($data['__raw']) && ($data['__raw']===true)){
			unset($data['__raw']);
			if(isset($data['__code'])){
				$status=$data['__code'];
				unset($data['__code']);
			}
			$out=$data;
		}else{
			$out=array('error'=>true);
			if($error === false){
				$out['error']=false;
				$out['data']=$data;				
			}else{
				$out['message']=$error;
			}
		}
		$response->status($status);
		$response->body(json_encode($out,JSON_UNESCAPED_UNICODE));
		$this->stop();
	}
	public function api_require_fields($fds){
		$error_fields='';
		if(is_array($fds)){
			foreach ($fds as $field) {
				if (!isset($_POST[$field])) {
					if($error_fields) $error_fields.=', ';
					$error_fields .= $field;
				}
			}
		}
	
		if($error_fields){
			$this->writeJSON(array('__raw'=>true, 'error'=>true, 'message'=>'Required field(s) ' . $error_fields . ' is missing or empty'),400,'400 Bad Request');
		}
	}	
}


//create API Appication

$app = new App(array(
    'view' => new \Slim\Views\Twig()
));

$app->add(new \Slim\Middleware\ContentTypes());

$app->notFound(function () use ($app) {
	$app->writeJSON(null, 404, 'Not Found');
});

$app->error(function (\Exception $e) use ($app) {
	$app->writeJSON(null, 500, 'Internal Server Error');
});

$app->get( '/', function () use ($app) {
        echo "API SERVICES";
});

$app->map('/:ver/:args+/','App::Authenticate', function ($ver, $args) use ($app) {

	$main=__DIR__. '/'. $ver . '/main.php';
	if(count($args)){
		$_POST=$app->request->post();
		$app->arguments=array_values($args);
		$fname=array_shift($args);
		$method=strtolower($app->request->getMethod());
		array_unshift($args,$app);
		$app->api_version=$ver;
		if($fname && file_exists($main)){
			@require_once ($main);
			$func=$app->API_PREFIX . $method .'_'. $fname;
			if(!function_exists($func)){
				$func=$app->API_PREFIX. 'all_' . $fname;
				if(!function_exists($func)){
					$func=$app->API_PREFIX . $fname;
					if(!function_exists($func)){
						$func=null;
					}
				}
			}
			if($func){
				call_user_func_array ($func ,  $args);
				return true;
			}
		}
		
		//find external api
		if(($fname=='login') || ($app->auth->getUserId()!==null)){
			$fi=__DIR__. '/'. $ver . '/' . $fname .'.php';
			if($fname && file_exists($fi)){
				@require_once($fi);
				$func=$app->API_PREFIX . $method .'_'. $fname;
				if(!function_exists($func)){
					$func=$app->API_PREFIX . 'all_' . $fname;
					if(!function_exists($func)){
						$func=$app->API_PREFIX . $fname;
						if(!function_exists($func)){
							$func=null;
						}
					}
				}
				if($func){
					call_user_func_array ($func ,  $args);
					return true;
				}
			}
		}
	}
	$app->notFound();
})->via('GET', 'POST')->conditions(array('ver' =>'v\d{1,}([.]\d{1,}){0,1}'));
	
	
$app->run();
