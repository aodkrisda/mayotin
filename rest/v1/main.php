<?php
require_once(__DIR__.'/include.php');


function api_all_zip($app){
$app->orm->join_table('`table1`','left join `table2` on table1.field = table2.field');
//var_dump($app->orm->user()->select('user.*,level.*')->toArray());
	echo $app->orm->escape_like('hell%ow 5% worl%d',false,false);
	exit();
}
function api_post_login($app){
	$req=$app->request;
	$username = $req->post('user_number');
	$password = $req->post('password');

	$rs=$app->orm->user()->where(array('user_id'=>$username, 'password '=>$password))->limit(1);
	if(count($rs)){
		$u=$rs->fetch();
		$user=$u->toArray();
		if($user){
			if($user['active']==1){
				$nw=new NotORM_Literal("NOW()");
				$u->update(array('date_last_used'=>$nw));
				$rs->where('(date_first_used IS NULL) OR (date_first_used=?)','0000-00-00')->update(array('date_first_used'=>$nw));
				unset($user['password']);
				$app->auth->setUser($user);
				$app->session->set('_auth_',$app->auth->getUser());
				$key=md5($app->session->id().':'. $app->auth->getUserId());
				$app->session->set('_authkey_', $key);
				$app->writeJSON(array('api_key'=>$key, 'user'=>$user));
				return;
			}else{
				$app->writeJSON(null, 401, 'เลขประจำตัวผู้ใช้นี้ ยังไม่ได้รับอนุญาติให้ใช้งาน ต้องรอการอนุญาติจากผู้ดูแลระบบก่อน');
				return;
			}
			
		}
	}
	$app->writeJSON(null, 401, 'เลขประจำตัวผู้ใช้ หรือรหัสผ่าน ไม่ถูกต้อง');
}
function api_post_usermodified($app){
	$user=$app->auth->getUser();
	$ret=null;
	if($user){
		$r=$app->orm->user()->where('user_id', $user['user_id'])->fetch();
		if($r){
			$ret=$r['date_modified'];
		}
	}
	$app->writeJSON(array('date_modified'=>$ret));
}


function api_post_register($app){
	$action='add';
	$cls=new NGTABLE_ADMIN_USER($app,'user','user_id',true);
	$rs=$cls->process($action);
	if($rs && isset($rs['error']) && ($rs['error']===true)){
		$rs['error2']=true;
	}
	$app->writeJSON($rs); 
}

function api_post_logout($app){
	Session::destroy();
	$app->writeJSON(true);
}
	
function api_post_getlookups($app){

	$groups=$app->orm->toArray($app->orm->{'`group`'}()->limit(1000));
	$levels=$app->orm->toArray($app->orm->level()->limit(1000));
	$subjects=$app->orm->toArray($app->orm->subject()->limit(1000));
	$teachers=$app->orm->toArray($app->orm->user()->select('user_id,title,first_name,last_name,group_id,level_id')->where('user_type','2')->order('title,first_name')->limit(1000));
	$roles=array(
			array('role_id'=> '0', 'name'=> 'นักเรียน'),
			array('role_id'=> '1', 'name'=> 'ผู้ดูแลระบบ' ),
			array('role_id'=> '2', 'name'=> 'ครูผู้สอน' ),
			array('role_id'=> '3', 'name'=> 'คณะกรรมการ' ),
			array('role_id'=> '4', 'name'=> 'ผู้บริหาร')
	);
	$titles=array(
			array( 'name'=> 'นาย'),
			array('name'=> 'นาง' ),
			array('name'=> 'นางสาว' ),
			array('name'=> 'ด.ช.' ),
			array('name'=> 'ด.ญ.' )
	);
	$types=array(
			array('type_id'=>'1', 'name'=> 'สื่อประเภทวีดีโอ','icon'=>'fa-youtube-play', 'prefix'=>'VDO'),
			array('type_id'=>'2','name'=> 'สื่อประเภทเสียง' ,'icon'=>'fa-volume-up','prefix'=>'MP3'),
			array('type_id'=>'3','name'=> 'สื่อประเภทเอกสาร' ,'icon'=>'fa-file-text','prefix'=>'DOC')
	);
	$rs=array('server_date'=>$app->orm->now() , 'group'=>&$groups, 'role'=>&$roles, 'type'=>&$types, 'level'=>&$levels, 'subject'=>&$subjects,'teacher'=>&$teachers, 'title'=>&$titles);
	$app->writeJSON($rs);
}

function api_post_searh_media($app, $action=''){
	$cls=new NGTABLE_SEARCH_MEDIA($app);
	$rs=$cls->process($action);
	$app->writeJSON($rs);
}
function api_post_report_media($app, $action=''){
	$cls=new NGTABLE_REPORT_MEDIA($app);
	$rs=$cls->process($action);
	$app->writeJSON($rs);
}
function api_post_report_media2($app, $action=''){
	$cls=new NGTABLE_REPORT_MEDIA2($app);
	$rs=$cls->process($action);
	$app->writeJSON($rs);
}
function api_post_view_media($app, $action=''){
	$cls=new NGTABLE_SEARCH_MEDIA($app);
	$rs=$cls->process('view');
	$app->writeJSON($rs);
}

function api_post_like_media($app, $action=''){
	$cls=new NGTABLE_SEARCH_MEDIA($app);
	$rs=$cls->process('like');
	$app->writeJSON($rs);
}

function api_post_unlike_media($app, $action=''){
	$cls=new NGTABLE_SEARCH_MEDIA($app);
	$rs=$cls->process('unlike');
	$app->writeJSON($rs);
}

function api_post_rating_media($app, $action=''){
	$cls=new NGTABLE_SEARCH_MEDIA($app);
	$rs=$cls->process('rating');
	$app->writeJSON($rs);
}
	
function api_post_stat_media($app, $action=''){
	$cls=new NGTABLE_SEARCH_MEDIA($app);
	$rs=$cls->process('get');
	$app->writeJSON($rs);
}
		
function api_post_user($app, $action=''){
	$cls=new NGTABLE_ADMIN_USER($app);
	$rs=$cls->process($action);
	$app->writeJSON($rs);
}

function api_post_history($app, $action=''){
	$cls=new NGTABLE_HISTORY($app);
	$rs=$cls->process($action);
	$app->writeJSON($rs);
}
function api_post_history_evaluate($app, $action=''){
	$cls=new NGTABLE_HISTORY_EVALUATE($app);
	$rs=$cls->process($action);
	$app->writeJSON($rs);
}
function api_post_media_sumary($app, $action=''){
	if(isset($_POST['media_id'])){
		$usr=$app->auth->getUser();
		$user_id=($usr)?$usr['user_id']:'0'; 
		$r=$app->orm->join_table('media','inner join study on media.media_id=study.media_id')
			->where('media.user_id',$user_id)
			->where('media.media_id',$_POST['media_id']);
			
			$r2=$r->push()->where('study.view_count>?',0)
			->select('min(study.first_used) as first_used, max(study.last_used) as last_used');
			
			$data=array();
			if(count($r2)>0){
				foreach($r2->toArray()[0] as $a=>$b){
				$data[$a]=$b;
				}
			}

			$data['count_user']=count($r->push()->select('')->select('distinct(study.user_id)')->where('study.view_count>0'));
			$r->select('count(study.rating) as count_rating ,sum(study.view_count) as count_view, sum(study.like_count) as count_like, sum(study.unlike_count) as count_unlike,avg(study.rating) as user_score');
			if(count($r)>0){
				foreach($r->toArray()[0] as $a=>$b){
				$data[$a]=$b;
				}
			}
			$data['count_comment']=$app->orm->comment()->where('media_id',$_POST['media_id'])->count('*');
			$app->writeJSON($data);
			return;
		
	}
	$app->writeJSON(null,401,'ไม่พบข้อมูลที่คุณต้องการ');
}
function api_post_profile($app, $action=''){
	if($action=='changepassword'){
		$app->api_require_fields(array('password','password2'));
		$usr=$app->auth->getUser();
		$user_id=($usr)?$usr['user_id']:'0'; 
		$r=$app->orm->user()->where('user_id',$user_id)->where('password',$_POST['password']);
		if(count($r)>0){
			$r->update(array('password'=> $_POST['password2']));
			$app->writeJSON(true);
		}else{
			$app->writeJSON(null,400,'รหัสผ่านเดิมไม่ถูกต้อง');
		}
		return;
	}
	
	if($action=='get' || $action=='update'){
		$cls=new NGTABLE_ADMIN_USER($app,'user','user_id',true);
		$rs=$cls->process($action);
		if($rs && isset($rs['error']) && ($rs['error']===false)){
			unset($rs['data']['password']);
			if(isset($rs['data']['user_id'])){
				$app->session->set('_auth_',$rs['data']);
			}
		}
		$app->writeJSON($rs);
	}
	return false;
}

function api_post_level($app, $action=''){
	$cls=new NGTABLE_ADMIN_LEVEL($app);
	$rs= $cls->process($action);
	$app->writeJSON($rs);
}

function api_post_group($app, $action=''){
	$cls=new NGTABLE_ADMIN_GROUP($app);
	$rs= $cls->process($action);
	$app->writeJSON($rs);
}

function api_post_type($app, $action=''){
	$cls=new NGTABLE_ADMIN_TYPE($app);
	$rs= $cls->process($action);
	$app->writeJSON($rs);
}

function api_post_evaluate_group($app, $action=''){
	$cls=new NGTABLE_ADMIN_EVALUATE_GROUP($app);
	$rs= $cls->process($action);
	$app->writeJSON($rs);
}

function api_post_evaluate_topic($app, $action=''){
	$cls=new NGTABLE_ADMIN_EVALUATE_TOPIC($app);
	$rs= $cls->process($action);
	$app->writeJSON($rs);
}
function api_post_subject($app, $action=''){
	$cls=new NGTABLE_ADMIN_SUBJECT($app);
	$rs= $cls->process($action);
	$app->writeJSON($rs);
}

function api_post_media($app, $action=''){
	$cls=new NGTABLE_TEACHER_MEDIA($app);
	$rs= $cls->process($action);
	$app->writeJSON($rs);
}
function api_post_admin_media($app, $action=''){
	$cls=new NGTABLE_ADMIN_MEDIA($app);
	$rs= $cls->process($action);
	$app->writeJSON($rs);
}
function api_post_user_comment($app, $action=''){
	$cls=new NGTABLE_USER_COMMENT($app);
	$rs=$cls->process($action);
	$app->writeJSON($rs);
}

function api_post_user_evaluate($app, $action=''){
	$param=$app->request->post();

	if($param){
	$tb=$app->orm->score()->where('user_id',$param['user_id'])->where('media_id',$param['media_id']);
	$result=false;
	if($action=='get'){
		if(count($tb)){
			$row = $tb->fetch();
			$result=$row->toArray();
		}
	}else if($action=='update'){
		if(isset($param['avg'])){
			$tm=$app->orm->study()->where('user_id',$param['user_id'])->where('media_id',$param['media_id']);
			if(count($tm)>0){
				$tm->update(array('rating'=>$param['avg']));
			}else{
				$tm->insert(array('rating'=>$param['avg'],'user_id'=>$param['user_id'],'media_id'=>$param['media_id']));
			}
			unset($param['avg']);
		}	
		if(count($tb)){
			$row = $tb->fetch();
			$result=true;
			$row->update($param);
		}else{
			$result=$tb->insert($param);
		}
		if($result){
			$result=true;	
		}
		if(!$result) $app->writeJSON(array('error'=>true ,'__raw'=>true,'message'=>'บันทึกข้อมูลไม่ได้'),400);
	}
	if($result){
		$app->writeJSON(array('error'=>false ,'__raw'=>true,'data'=>$result));
		return;
	}

	}
	
}

function api_post_teacher_evaluate($app, $action=''){
	$param=$app->request->post();

	if($param){
		$ncount=$app->orm->score()->where('media_id',$param['media_id'])->count('*');
		$lnks=array();
		for($i=1;$i<=5;$i++){	
			$fd='score'. $i;
			$tb=$app->orm->score()->select("{$i} as id, {$fd} as score, count({$fd}) as n")->where('media_id',$param['media_id'])->group($fd);
			$tba=$tb->toArray();
			foreach($tba as $it){
				$key=$it['id']. '_'. $it['score'];
				$lnks[$key]=$it['n'];
			}
		}
		
		

	$tb=$app->orm->score()->where('user_id',$param['user_id'])->where('media_id',$param['media_id']);
	$result=false;
	if($action=='get'){
		if(count($tb)){
			$row = $tb->fetch();
			$result=$row->toArray();
		}
	}
	$result=array();
	$result[]=array('id'=>1,'score0'=>0,'score1'=>0,'score2'=>0,'score3'=>0,'score4'=>0);
	$result[]=array('id'=>2,'score0'=>0,'score1'=>0,'score2'=>0,'score3'=>0,'score4'=>0);
	$result[]=array('id'=>3,'score0'=>0,'score1'=>0,'score2'=>0,'score3'=>0,'score4'=>0);
	$result[]=array('id'=>4,'score0'=>0,'score1'=>0,'score2'=>0,'score3'=>0,'score4'=>0);
	$result[]=array('id'=>5,'score0'=>0,'score1'=>0,'score2'=>0,'score3'=>0,'score4'=>0);
	if($result){
		foreach($result as &$it){
			if($it){
				$key=$it['id'].'_0';
				if(isset($lnks[$key])) $it['score0']=$lnks[$key];
				$key=$it['id'].'_1';
				if(isset($lnks[$key])) $it['score1']=$lnks[$key];
				$key=$it['id'].'_2';
				if(isset($lnks[$key])) $it['score2']=$lnks[$key];
				$key=$it['id'].'_3';
				if(isset($lnks[$key])) $it['score3']=$lnks[$key];
				$key=$it['id'].'_4';
				if(isset($lnks[$key])) $it['score4']=$lnks[$key];																
			}		
		}
		$app->writeJSON(array('error'=>false ,'__raw'=>true,'count'=>$ncount, 'data'=>$result));
		return;
	}
	}
	
}

function api_post_user_evaluate_form($app, $action=''){
	$param=$app->request->post();

	if($param){

	$result=false;
	$form=null;
	if($action=='get'){
		$tb=$app->orm->join_table('evaluate_group','LEFT JOIN evaluate_topic ON evaluate_group.evaluate_group_id=evaluate_topic.evaluate_group_id')->select('evaluate_topic.evaluate_topic_id as id,evaluate_topic.name,evaluate_group.name as group_name, null as score')->order('evaluate_group.name,evaluate_topic.name');
		$items=$tb->toArray();	
		$tb=$app->orm->evaluate_form()->where('user_id',$param['user_id'])->where('media_id',$param['media_id']);
		if(count($tb)){
			$ax=$tb->fetch();
			$form=$ax->toArray();
			$tmp=$ax->evaluate_form_detail()->fetchPairs('evaluate_topic_id','score');
			foreach($items as &$it){
				if(isset($tmp[$it['id']])){
					$it['score']=intval($tmp[$it['id']]);
				}
			}
		}

		
		$result=array();
		$group=null;
		foreach($items as &$it){
			if(isset($it['group_name'])){
				if($it['group_name']!==$group){ 
					$group=$it['group_name'];
					$result[]=array('group'=>true, 'name'=>$group);
				}
				unset($it['group_name']);
			}
			$result[]=$it;
		}
	}else if($action=='update'){
		$tb=$app->orm->evaluate_form()->where('user_id',$param['user_id'])->where('media_id',$param['media_id']);
		if(count($tb)){
			$t=$tb->fetch();
		}else{
			$t=$tb->insert(array('media_id'=>$param['media_id'], 'user_id'=>$param['user_id'], 'avg_score'=>$param['avg_score'],'comment'=>$param['comment']));
		}
		if($t){
			$app->orm->transaction='BEGIN';
			$ts=$t->evaluate_form_detail();
			$rs=$ts->fetchPairs('evaluate_topic_id');
			foreach($param['items'] as $it){
				if(isset($rs[$it['id']])){
					$rs[$it['id']]->update(array('score'=> $it['score']));
				}else{
					$ts->insert(array('evaluate_topic_id'=>$it['id'], 'score'=> $it['score']));
				}
			}
			$t->update(array('avg_score'=>$param['avg_score'],'comment'=>$param['comment'],'date_modified'=>new NotORM_Literal("NOW()")));
			$t=$app->orm->evaluate_form()->where('media_id',$param['media_id'])->select('avg(avg_score) as n');

			if(count($t)){ 
				$r=$t->fetch();
				$at=($r['n']>=3)?1:0; 
				$n=$app->orm->evaluate_form()->where('media_id',$param['media_id'])->count('*'); 
				$app->orm->media()->where('media_id',$param['media_id'])->update(array('evaluate_score'=>$r['n'],'active'=>$at,'evaluate_count'=>$n));
			}
			$result=true;
			$app->orm->transaction='COMMIT';
		}
		
		if(!$result) $app->writeJSON(array('error'=>true ,'__raw'=>true,'message'=>'บันทึกข้อมูลไม่ได้'),400);
	}
	if($result){
		$app->writeJSON(array('error'=>false ,'__raw'=>true,'data'=>array('form'=>$form, 'items'=>$result)));
		return;
	}

	}
	
}


function api_post_teacher_evaluate_form($app, $action=''){

	$param=$app->request->post();
	if($action=='get'){
		$ncount=$app->orm->evaluate_form()->where('media_id',$param['media_id'])->count('*');
		$tb=$app->orm->join_table('evaluate_form','INNER JOIN evaluate_form_detail ON evaluate_form.evaluate_form_id=evaluate_form_detail.evaluate_form_id')->select('evaluate_form_detail.evaluate_topic_id as id,evaluate_form_detail.score,count(evaluate_form_detail.score) as n')->where('evaluate_form.media_id',$param['media_id'])->group('evaluate_form_detail.evaluate_topic_id,evaluate_form_detail.score');
		$scores=$tb->toArray();	
		$lnks=array();
		foreach($scores as $it){
			$key=$it['id']. '_'. $it['score'];
			$lnks[$key]=$it['n'];
		}
		unset($scores);

		$tb=$app->orm->join_table('evaluate_group','LEFT JOIN evaluate_topic ON evaluate_group.evaluate_group_id=evaluate_topic.evaluate_group_id')->select('evaluate_topic.evaluate_topic_id as id,evaluate_topic.name,evaluate_group.name as group_name, 0 as score0, 0 as score1, 0 as score2, 0 as score3, 0 as score4')->order('evaluate_group.name,evaluate_topic.name');
		$items=$tb->toArray();	
		
		$item2=$app->orm->evaluate_form()->where('evaluate_form.media_id',$param['media_id'])->where('evaluate_form.comment!=?','')->select('evaluate_form.user_id,evaluate_form.comment,evaluate_form.date_modified,user.first_name, user.last_name,user.title')->order('evaluate_form.date_modified desc')->toArray();

		
		$result=array();
		$group=null;
		foreach($items as &$it){
			if(isset($it['group_name'])){
				if($it['group_name']!==$group){ 
					$group=$it['group_name'];
					$result[]=array('group'=>true, 'name'=>$group);
				}
				unset($it['group_name']);
			}
			if($it){
				$key=$it['id'].'_0';
				if(isset($lnks[$key])) $it['score0']=$lnks[$key];
				$key=$it['id'].'_1';
				if(isset($lnks[$key])) $it['score1']=$lnks[$key];
				$key=$it['id'].'_2';
				if(isset($lnks[$key])) $it['score2']=$lnks[$key];
				$key=$it['id'].'_3';
				if(isset($lnks[$key])) $it['score3']=$lnks[$key];
				$key=$it['id'].'_4';
				if(isset($lnks[$key])) $it['score4']=$lnks[$key];																
			}
			$result[]=$it;
		}
		$app->writeJSON(array('error'=>false ,'__raw'=>true,'data'=>array('count'=>$ncount,'comments'=>$item2, 'items'=>$result)));
	}
		
	
}
function api_get_phpinfo(){
	phpinfo();
	exit();
}

function api_get_test($app){
	$app->writeJSON(count($app->orm->get_tables()));
}

function api_get_download($app, $media_id=''){
	if($media_id){

		$rs=$app->orm->media[$media_id];
		$file_name =null;
		if($rs){
			$file_name = dirname(dirname(__DIR__)) . '/uploads/' . $rs['user_id'] . '/'. $rs['url'];
		}
		if($file_name && is_file($file_name)) {
			if(@ini_get('zlib.output_compression')) {@ini_set('zlib.output_compression', 'Off');	}
			switch(strtolower(substr(strrchr($file_name, '.'), 1))) {
				case 'pdf': $mime = 'application/pdf'; break;
				case 'mp4': $mime = 'video/mp4'; break;
				case 'flv': $mime = 'video/x-flv'; break;
				case 'mp3': $mime = 'audio/mpeg3'; break;
				default: $mime = 'application/force-download';
			}
			header('Pragma: public'); 	// required
			header('Expires: 0');		// no cache
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
			header('Cache-Control: private',false);
			header('Content-Type: '.$mime);

			$fname=$media_id . '.' . explode('.',basename($file_name))[1];
			header('Content-Disposition: attachment; filename="'. $fname .'"');
			header('Content-Transfer-Encoding: binary');
			header('Content-Length: '.filesize($file_name));
			header('Connection: close');
			readfile($file_name);
			exit();
		}
	}
	$app->notFound();
}

function api_all_upload($app, $media_id='',$suffix=''){

	$user=$app->auth->getUser();
	$baseDir=dirname(__DIR__);
	
	$fd=$user['user_id'];
	$is_photo=false;
	if($media_id=='photo'){
		$fd='photos';
		$is_photo=true;
	}
	
	$uploadsDir=dirname($baseDir) . '/uploads/' . $fd . '/';
	$chunksDir=dirname($baseDir) . '/.chunks/'. $fd . '/';

	require($baseDir.'/Flow/Autoloader.php');
	Flow\Autoloader::register($baseDir);

	$config = new Flow\Config();
	$config->setTempDir($chunksDir);
	$file = new Flow\File($config);

	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		if(!is_dir($uploadsDir)){
			@mkdir(dirname($uploadsDir));
			@mkdir($uploadsDir);
		}
		if(!is_dir($chunksDir)){
			@mkdir(dirname($chunksDir));
			@mkdir($chunksDir);
		}
		if ($file->checkChunk()) {
			header( $_SERVER["SERVER_PROTOCOL"]. " 200 Ok");
		} else {
			header( $_SERVER["SERVER_PROTOCOL"]. " 204 No Content");
			exit();
		}
	} else {
		if ($file->validateChunk()){
			$file->saveChunk();
		} else {
			// error, invalid chunk upload request, retry
			header( $_SERVER["SERVER_PROTOCOL"]. " 400 Bad Request");
			exit();
		}
	}
	$media_file=strtolower($file->getFileName());
	$profile=$media_id=='photo';
	if($media_id){
		if($suffix)$suffix='.'.$suffix;
		if($profile){
			$media_file=$user['user_id'] . '.'. pathinfo($media_file)['extension'];
		}else{
			$media_file=md5($media_id) . $suffix.  '.' . pathinfo($media_file)['extension'];
		}
	}
	if($is_photo){
		//update user
		$u=$app->orm->user()->where('user_id',$user['user_id'])->fetch();
		if($u) $u->update(array('date_modified'=> new NotORM_Literal("NOW()")));
	}else if($media_id){
		$u=$app->orm->media()->where('media_id',$media_id)->fetch();
		if($u) $u->update(array('date_modified'=> new NotORM_Literal("NOW()")));	
	}
	$media_file=preg_replace('/\.php$/','.php._',$media_file);
	if ($file->validateFile() && $file->save($uploadsDir . $media_file)) {
		if($profile){
			resize_image($uploadsDir . $media_file,$uploadsDir . $user['user_id'].  '.jpg',120);
			if($media_file!=($user['user_id'].'.jpg')){
				@unlink($uploadsDir . $media_file);
				$media_file=$user['user_id'].'.jpg';
			}
		}else if($suffix){
			resize_image($uploadsDir . $media_file);
			create_thumbnail($uploadsDir . $media_file);
		}
		echo $media_file;
		$files = glob($chunksDir . '*');
		foreach($files as $file){
			if(is_file($file)) @unlink($file);
		}
	} else {
		echo "ไฟล์ยังอัพโหลดไม่สมบูรณ์";
	}
}