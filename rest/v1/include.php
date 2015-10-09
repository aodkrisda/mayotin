<?php
global $UPLOADS_DIR;
$UPLOADS_DIR=dirname(dirname(__DIR__)) . '/uploads/';

function del_tree($dir) { 
	if($dir && is_dir($dir)){
	    $files = array_diff(scandir($dir), array('.','..')); 
	    foreach ($files as $file) { 
	      (is_dir("$dir/$file")) ? del_tree("$dir/$file") : @unlink("$dir/$file"); 
	    } 
	    return @rmdir($dir); 
    }
    return false;
} 
function api_import_user($app,$xls=''){
	if(isset($_FILES) && isset($_FILES['excel_file'])){
		$xls=$_FILES['excel_file']['tmp_name'];
		$result=importUserFromExcel($app, $xls, $_POST);
		@unlink($xls);
		$app->writeJSON($result);
		return;
	}
	$app->writeJSON(array('__raw'=>true, 'error'=>true, 'message'=>'Import Error.................'),400,'400 Bad Request');

}
function create_thumbnail($filename, $thumbdir='sm', $width=32,  $quality=90){
	try{
		$fd=dirname($filename) . '/'.$thumbdir;
		@mkdir($fd);
		$name=basename($filename);
		$thumb=$fd . '/'. $name;	
		return resize_image($filename, $thumb, $width, $quality);
	}catch(Exception $e){}
	return false;
}

function resize_image($filename, $destfile=null, $width=600, $quality=90){
	try{
		require_once (dirname(__DIR__) .'/Util/ImageResize.php');
		$img=new Eventviva\ImageResize($filename);
		$img->quality_jpg = $quality;
		if($img->getSourceWidth() > $img->getSourceHeight()){
			if($img->getSourceWidth()<$width){
				if($destfile==$filename){
					return false;
				}
			}else{
				$img->resizeToWidth($width);
			}
		}else{
			if($img->getSourceHeight()<$width){
				if($destfile==$filename){
					return false;
				}
			}else{	
				$img->resizeToHeight($width);
			}
		}
		if($destfile===null) $destfile=$filename;
		$img->save($destfile);
		return true;
	}catch(Exception $e){}
	return false;
}



/* NG-TABLE */

class NGTABLE{
  protected $tb;
  protected $app;
  protected $pk='id';
  protected $required=array();
  protected $uniques=array();
  protected $roles=array();
  protected $param=array();
  public $isRegister=false;
  
  function __construct($app, $table, $pk='id', $register=false) {
    $this->pk=$pk;
    $this->tb=$table;
    $this->app=&$app;
    $this->isRegister=$register;

    //check role
    if(!$this->isRegister){
    if(is_array($this->roles) && count($this->roles)){
      if (!in_array($this->app->auth->getRole(), $this->roles)) {
        $this->app->writeJSON(array('error'=>true, 'messages'=>'คุณไม่ได้รับอนุญาติให้เข้าหน้านี้'),400); 
        exit();
      }
    }
    }
    $this->param=$app->request->post();
  }
  function getTableName(){
  	return $this->tb;
  }
  function getDateRange($datestr){
	  $old=strtotime($datestr);
	  $datestr=date('Y-m-1',$old);
	  $tm=strtotime($datestr);
	  $tm2=strtotime('+1 months',$tm);
	  $year=intval(date('Y',$tm));
	  $month=intval(date('m',$tm));
	  $date=intval(date('d',$old));
	  return array('year'=>$year, 'month'=>$month, 'day'=>$date, 'begin'=>date('Y-m-d',$tm) ,'date'=>date('Y-m-d',$old), 'end'=>date('Y-m-d',$tm2));
  }  
  function can_delete(){
  	//return null, false($tm===null)
  	return true;
  }
  function delete_links($ids){
  }
  function buildFilter($rs, $fs){
  	
    if($rs){
      if($fs && isset($fs['search'])){
        $rs->where('name LIKE ?','%' . $fs['search']. '%');
      }     
    }
  }
  function beforeFilter($rs){
  }
  function afterFilter($rs){
  }  
  function query(){
       $rs=$this->app->orm->{$this->tb}();
       $rs->order($this->pk . ' desc');
	   $this->beforeFilter($rs);
       if(isset($this->param['filter']) && is_array($this->param['filter'])){
        $this->buildFilter($rs, $this->param['filter']);
       }else{
        $this->buildFilter($rs,null);
       }

       if(isset($this->param['sorting']) && is_array($this->param['sorting'])){
        $b=true;
        foreach($this->param['sorting'] as $k=>$v){
          if($b){
            $b=false;
            $rs->order('');
          }
          $rs->order($k . ' '  .$v);
        }
       }
       $this->afterFilter($rs);
       $rs->limit($this->param['count'],(($this->param['page']-1) * $this->param['count']));
       $n=$rs->count("*");

       $rows=$this->app->orm->toArray($rs);
       return array('error'=>false ,'__raw'=>true,'total'=>$n,  'page'=>$this->param['page'],'data'=>&$rows);    
  }
  function get($id=null){
  	  if($id===null) $id=$this->param[$this->pk];
      $rs=$this->app->orm->{$this->tb}()[$id];
      if($rs){
        return $this->app->orm->toArray($rs);
      }
      return false;
  }
  function update(){
      $this->app->api_require_fields($this->required);

      foreach($this->uniques as $fd=>$err){
        if(isset($this->param[$fd]) && $this->param[$fd]){
          $rs=$this->app->orm->{$this->tb}()->where($fd, $this->param[$fd])->where($this->pk .' != ?', $this->param[$this->pk])->limit(1);
          if(count($rs)){
			return array('__raw'=>true, 'error'=>true, 'message'=>$err, '__code'=>400);
          }
        }
      }
      
      $rs=$this->app->orm->{$this->tb}()->where($this->pk, $this->param[$this->pk])->limit(1);
      if(count($rs)){
        $obj=$rs->fetch();
        $affected = $obj->update($this->param);
        $r=array();
        if($affected>0){
          $rs=$this->app->orm->{$this->tb}()->where($this->pk, $this->param[$this->pk])->limit(1);
          $obj=$rs->fetch();
          foreach($this->param as $a=>$b){
            if(isset($obj[$a])){
              $r[$a]=$obj[$a];
            }
          }
          return $r;
        }else{
          $r[$this->pk]=$this->param[$this->pk];
          return $r;
        }
      }
      return false;
  }
  function add(){
      if($this->param){
        $this->app->api_require_fields($this->required);
        foreach($this->uniques as $fd=>$err){
          if(isset($this->param[$fd]) && $this->param[$fd]){
            $rs=$this->app->orm->{$this->tb}()->where($fd,$this->param[$fd])->limit(1);
            if(count($rs)){
              return array('__raw'=>true, 'error'=>true, 'message'=>$err, '__code'=>400);
            }
          }
        }        
     
        $obj=$this->app->orm->{$this->tb}()->insert($this->param);
        if($obj){
          $obj=$this->get($obj[$this->pk]);
          if($obj){
         	 if(isset($obj['password'])){
          	  unset($obj['password']);
         	 }
         	 return $obj;
          }
        }
      } 
      return false;
  }
  function before_delete($rs){
  }
  function delete(){
  	  $confirm=false;
  	  if(count($this->app->arguments)>=3){
  	  	$confirm=($this->app->arguments[2]=='1');
  	  }
  	  if(!$confirm){
	  	  $tm=$this->can_delete();
	  	  if(!$tm){
	  	 	 return array('__raw'=>true, 'error'=>false, 'confirm'=>($tm!==null),'data'=>array());
	  	  }
  	  }
      $rs=$this->app->orm->{$this->tb}()->where($this->pk,$this->param[$this->pk]);
      if($rs){
      	$this->before_delete($rs);
        $effected=$rs->delete();
        if($effected > 0){
          $this->delete_links($this->param[$this->pk]);
          return array($this->pk=>$this->param[$this->pk]);
        }
      }
      return array('__raw'=>true, 'error'=>true, 'message'=>'เกิดข้อผิดพลาด ไม่สามารถลบข้อมูลได้', '__code'=>400);
  }
  
  function process($action=''){
    if(($action=='get') || ($action=='delete') || ($action=='update')){
      $this->app->api_require_fields(array($this->pk));
    }  

    switch($action){
      case 'get':
        return $this->get();
        break;
      case 'add':
        return $this->add();
        break;
      case 'update':
        return $this->update();
        break;
      case 'delete':
        return $this->delete();
        break;
       case '':
        return $this->query();
        break;
       default:
       	return $this->custom_process($action);
       	break;
    }

    return false;
  }
  function custom_process($action=''){
    	return false;
  }
}

class NGTABLE_SEARCH_MEDIA extends NGTABLE{
  function __construct($app, $table='media', $pk='media_id') {
    parent::__construct($app, $table, $pk);
  }
  function buildFilter(&$rs, $fs){
  	if($rs){
  		if($fs){
  			foreach( array('level_id','group_id','user_id','subject_id','type_id') as $f){
  				if(isset($fs[$f]) && ($fs[$f]!='')){
  					$rs->where($f, $fs[$f]);
  				}
  			}
  		}
  		if(isset($fs['search']) && $fs['search']){
  			$str='%' . $fs['search']. '%';
  			$rs->where('(topic LIKE ?) OR (media_id LIKE ?) OR (description LIKE ?)',array($str,$str,$str));
  		}
		$usr=$this->app->auth->getUser();
		$user_id=($usr)?$usr['user_id']:'0'; 
		
		switch($this->app->auth->getRole()){
			case 'student':
			case 'admin':
				$rs->where('active',1);
				break;
			case 'teacher':
				$rs->where('((active=?) OR (user_id=?))',array(1,$user_id));
				break;
			case 'manager':
				if(isset($fs['evaluate'])){				
					if($fs['evaluate']=='1'){
						$rs2=$this->app->orm->evaluate_form()->select('media_id')->where('user_id',$user_id);
						$rs->and('NOT media_id',$rs2);					
					}
				}
				break;
		}
  		$rs->order('date_created desc,topic');
  	}
  }
function query(){

	       $params=null;
	       $rs=$this->app->orm->{$this->tb}();
	
	       if(isset($this->param['filter']) && is_array($this->param['filter'])){
	       	$params=$this->param['filter'];
	        $this->buildFilter($rs, $this->param['filter']);
	       }else{
	        $this->buildFilter($rs,null);
	       }
	
	       if(isset($this->param['sorting']) && is_array($this->param['sorting'])){
	        $b=true;
	        foreach($this->param['sorting'] as $k=>$v){
	          if($b){
	            $b=false;
	            $rs->order('');
	          }
	          $rs->order($k . ' '  .$v); 
	        }
	       }
	       
	       $meta=array();
	       if($this->app->auth->getRole()=='manager'){
			$usr=$this->app->auth->getUser();
			$user_id=($usr)?$usr['user_id']:'0'; 	       
	       	$meta['active']=$this->app->orm->{$this->tb}()->where('active',1)->where('media_id',$rs)->count("*");
	       	$meta['inactive']=$this->app->orm->{$this->tb}()->where('(active IS NULL) OR (active!=?)',1)->where('media_id',$rs)->count("*");
			$meta['checked']=$this->app->orm->evaluate_form()->where('media_id',$rs)->where('user_id',$user_id)->count('media_id');
	       	$meta['uncheck']=0; 
	       }
	       	       
	       $rs->limit($this->param['count'],(($this->param['page']-1) * $this->param['count']));
	       $n=$rs->count("*");
	       $rows=$this->app->orm->toArray($rs);
	       if(isset($meta['uncheck'])){
	       	$meta['uncheck']=$n- intval($meta['checked']);
	       	if($meta['uncheck']<0) $meta['uncheck']=0;
	       }
	       

	       if($this->app->auth->getRole()=='manager'){

	       
	        $fs=$params;
	        if(!isset($fs['evaluate'])) $fs['evaluate']='0';
			if(isset($fs['evaluate'])){				
					if($fs['evaluate']!='1'){
						$usr=$this->app->auth->getUser();
						$user_id=($usr)?$usr['user_id']:'0'; 					
						$rs2=$this->app->orm->evaluate_form()->select('media_id,avg_score')->where('user_id',$user_id);
						$rs2->where('media_id',$rs);
						if(count($rs2)){
		
						$links=$rs2->fetchPairs('media_id','avg_score');
						foreach($rows as &$r){

							if(isset($links[$r['media_id']])){
								$r['_score']=intval($links[$r['media_id']]);
							}
							
						}
						unset($links);
						}
					}
			}	
		   } 	      

	       return array('error'=>false ,'__raw'=>true,'total'=>$n, 'meta'=>$meta, 'page'=>$this->param['page'],'data'=>&$rows);    
	  }

  function add(){
    return false;
  }
  function update(){
    return false;
  }  
  function delete(){
    return false;
  }  
  function compare($a, $b) {
    if ($a['views'] == $b['views']) {
        return 0;
    }
    return ($a['views'] < $b['views']) ? 1 : -1;
  }
  function custom_process($action=''){
		  if($action=='home'){
		  			$result=array('new_items'=>array(), 'top_items'=>array());
		  			$r1=$this->app->orm->media()->where('active',1)->order('date_created desc')->limit(4);
		  			if(count($r1)){
		  				$rs=$r1->toArray();
		  				$ids=array();
		  				foreach($rs as &$r){
		  					$ids[]=$r['media_id'];
		  				}
		  				$tm=$this->app->orm->study()->where('media_id',$ids)->select('media_id,sum(view_count) as n')->group('media_id')->fetchPairs('media_id');
		  				foreach($rs as &$r){
		  					if(isset($tm[$r['media_id']])){
		  						$r['views']=intval($tm[$r['media_id']]['n']);
		  					}else{
		  						$r['views']=0;
		  					}
		  				}	
		  				usort($rs, array($this,'compare'));	  				
		  				$result['new_items']=$rs;
		  			}
		  			
		  			$r2=$this->app->orm->study()->select('media_id,sum(view_count) as n')->group('media_id','n >= 10')->order('n desc')->limit(10);
		  			
		  			if(count($r2)){
		  				$ids=$r2->fetchPairs('media_id');
		  				$r3=$this->app->orm->media()->where('active',1)->where('media_id',array_keys($ids))->limit(4);
		  				$rs=$r3->toArray();
		  				foreach($rs as &$r){
		  					if(isset($ids[$r['media_id']])){
		  						$r['views']=intval($ids[$r['media_id']]['n']);
		  					}else{
		  						$r['views']=0;
		  					}
		  				}
		  				usort($rs, array($this,'compare'));	
		  				$result['top_items']=$rs;
		  			}
		  			
		  			return array('__raw'=>true, 'error'=>false, 'data'=>$result);
		  	}  
  	if(isset($this->param['media_id'])){
		$usr=$this->app->auth->getUser();
		$user_id=($usr)?$usr['user_id']:'0'; 
		$row=$this->app->orm->study->where('media_id',$this->param['media_id'])->where('user_id',$user_id)->fetch();
		if(!$row){
			$this->app->orm->study->insert(array('user_id'=>$user_id, 'media_id'=>$this->param['media_id'],'view_count'=>0, 'rating'=>null, 'like_count'=>null, 'unlike_count'=>null));
			$row=$this->app->orm->study->where('media_id',$this->param['media_id'])->where('user_id',$user_id)->fetch();
		}
		if($row){
			$b=false;

		  	switch($action){

		  		case 'rating':
			  		if(isset($this->param['rating'])){
			  			$b=$this->app->orm->study->where(array('user_id'=>$user_id, 'media_id'=>$this->param['media_id']))->update(array('rating'=>$this->param['rating']));
			  		}
			  		break;		  	
		  		case 'view':
			  		$b=$this->app->orm->study->where(array('user_id'=>$user_id, 'media_id'=>$this->param['media_id']))->update(array('last_used'=>new NotORM_Literal("NOW()"), 'view_count'=>new NotORM_Literal("view_count + 1")));
			  		break;
		  		case 'like':
			  		$b=$this->app->orm->study->where(array('user_id'=>$user_id, 'media_id'=>$this->param['media_id']))->update(array('like_count'=>1, 'unlike_count'=>0));
			  		break;	
		  		case 'unlike':
			  		$b=$this->app->orm->study->where(array('user_id'=>$user_id, 'media_id'=>$this->param['media_id']))->update(array('like_count'=>0, 'unlike_count'=>1));	  		
			  		break;
			  	case 'stat':
			  		$b=true;
			  		break;
			  		
		  	}
		  	if($b){
		  		$rs=$this->app->orm->study->select('avg(rating) as ratings, sum(view_count) as views, sum(like_count) as likes, sum(unlike_count) as unlikes')->where(array('media_id'=>$this->param['media_id']));
		  		$r=false;
		  		if(count($rs)){
		  			$r=$rs->fetch();
		  			$r['study']=$this->app->orm->study->where(array('user_id'=>$user_id, 'media_id'=>$this->param['media_id']))->fetch();
		  			$r['users']=count($this->app->orm->study->select('distinct(user_id) as users')->where(array('media_id'=>$this->param['media_id']))->where('view_count>?',0));
	
		  		}
		  		$t=$this->app->orm->score()->where('media_id',$this->param['media_id'])->select('sum(score1 + score2 + score3 + score4 + score5) as `score`, count(*) as `n`');
				if(count($t)){
					
					$rt=$t->fetch();
					
					$r['evaluate_users']=$rt['n'];
					
					if($rt['n']>0){
					$r['evaluate_users_score']=$rt['score'] / ($rt['n'] * 20) * 4;
					}
					
				}
		  		$r['evaluate_forms_count']=$this->app->orm->evaluate_form()->where('media_id',$this->param['media_id'])->count('*');
			
		  		return array('__raw'=>true, 'error'=>false, 'data'=>$r);
		  	}
	  	}
  	}
	return false;
  }
}

class NGTABLE_REPORT_MEDIA extends NGTABLE{
  function __construct($app, $table='media', $pk='media_id') {
    parent::__construct($app, $table, $pk);
  }
  function buildFilter(&$rs, $fs){
  	if($rs){
  		if($fs){
  			foreach( array('level_id','group_id','user_id','subject_id','type_id') as $f){
  				if(isset($fs[$f]) && ($fs[$f]!='')){
  					$rs->where($f, $fs[$f]);
  				}
  			}
  			$str='date_from';
  			if(isset($fs[$str]) && $fs[$str]){
  				$df=$this->app->orm->date_info($fs[$str]);
  				$rs->where('date_created>=?',$df['begin']);
  			}
  			$str='date_to';
  			if(isset($fs[$str]) && $fs[$str]){
  				$df=$this->app->orm->date_info($fs[$str]);
  				$rs->where('date_created<=?',$df['end']);
  			}  			
  		}
  		if(isset($fs['search']) && $fs['search']){
  			$str='%' . $fs['search']. '%';
  			$rs->where('(topic LIKE ?) OR (media_id LIKE ?) OR (description LIKE ?)',array($str,$str,$str));
  		}
  		$rs->order('date_created desc,topic');
  	}
  }
function query(){

	       $params=null;
	       $rs=$this->app->orm->{$this->tb}();
	
	       if(isset($this->param['filter']) && is_array($this->param['filter'])){
	       	$params=$this->param['filter'];
	        $this->buildFilter($rs, $this->param['filter']);
	       }else{
	        $this->buildFilter($rs,null);
	       }
	
	       if(isset($this->param['sorting']) && is_array($this->param['sorting'])){
	        $b=true;
	        foreach($this->param['sorting'] as $k=>$v){
	          if($b){
	            $b=false;
	            $rs->order('');
	          }
	          $rs->order($k . ' '  .$v); 
	        }
	       }


		   //start evalchart
		   $rs2=$rs->push()->select('')->select('media_id')->order('')->limit(5000);
		   $evalcharts=array('title'=>array(
		            'text'=> ''
		        ),
				'options'=>array('chart'=>array('type'=>'bar'),
				'colors'=>array('#0000ff','#00bb00','#bb0000'),
				'plotOptions'=>array(
		                'bar'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} สื่อ'
		                    )
		            
		                )
		            )));

					
		   $tm=$this->app->orm->join_table('evaluate_form','inner join media on evaluate_form.media_id = media.media_id')->select('evaluate_form.user_id, count(evaluate_form.media_id) as n')->where('evaluate_form.media_id',$rs2)->group('evaluate_form.user_id')->toArray();
		   $rt=$this->app->orm->join_table('evaluate_form','inner join media on evaluate_form.media_id = media.media_id')->select('evaluate_form.user_id, count(evaluate_form.media_id) as n')->where('evaluate_form.avg_score>=3')->where('evaluate_form.media_id',$rs2)->group('evaluate_form.user_id')->toArray();
		  
		   $tm2=array();
		   if($rt){
			foreach($rt as $it){
				$tm2[$it['user_id']]=$it['n'];
			}
		   }
	
		   if($tm){
		  
				$c=array();
				$ids=array();
				$us=array();
				foreach($tm as $it){
					$ids[]=$it['user_id'];
				}
				$tm3=$this->app->orm->user()->where('user_id', $ids)->fetchPairs('user_id');
				foreach($tm as $it){
					$n=intval($it['n']);
				
					if(!isset($c[$it['user_id']])){
						$c[$it['user_id']]=array(0=>0,1=>0,2=>0);
						$na=$it['user_id'];
						if(isset($tm3[$na])){
							$na=sprintf('%s %s  %s',$tm3[$na]['title'],$tm3[$na]['first_name'],$tm3[$na]['last_name']);
						}
						$us[]=$na;
					}
					  
					$c[$it['user_id']][0]=$n;
					$n2=0;
					if(isset($tm2[$it['user_id']])){
						$n2=intval($tm2[$it['user_id']]);
					}
				 
					$c[$it['user_id']][0]=$n;
				
					$c[$it['user_id']][1]=$n2;
					$c[$it['user_id']][2]=$n - $n2;			}
			
				$series=array();
				$seri=array('name'=>'จำนวนสื่อทั้งหมดที่ได้ประเมิน', 'data'=>array());
				foreach($c as $id=>$it){
					$seri['data'][]=(isset($it[0]))? ($it[0]) : 0;
				}
				$series[]=$seri;
				$seri=array('name'=>'ประเมินให้ผ่าน', 'data'=>array());
				foreach($c as $id=>$it){
					$seri['data'][]=(isset($it[1]))? ($it[1]) : 0;
				}
				$series[]=$seri;
				$seri=array('name'=>'ประเมินให้ไม่ผ่าน', 'data'=>array());
				foreach($c as $id=>$it){
					$seri['data'][]=(isset($it[2]))? ($it[2]) : 0;
				}
				$series[]=$seri;
				$evalcharts['xAxis']=array('categories'=>$us);
				$evalcharts['yAxis']=array('title'=>array('text'=> 'จำนวนสื่อที่ประเมิน'), 'align'=> 'high' );
				$evalcharts['labels']=array('overflow' => 'justify');
				$evalcharts['plotOptions']=array('bar'=>array('dataLabels'=>array('enabled'=>true)));
				$evalcharts['series']=$series;

		   }
		
		   //end evalchart


	       
	       $meta=array('max_score'=>0,'min_score'=>0,'avg_score'=>0,'max_rating'=>0,'avg_rating'=>0,'min_rating'=>0,'max_views'=>0,'min_views'=>0,'avg_views'=>0);
			//sumary
	       $meta['active']=$this->app->orm->{$this->tb}()->where('active',1)->where('media_id',$rs)->count("*");
	       $meta['inactive']=$this->app->orm->{$this->tb}()->where('(active IS NULL) OR (active!=?)',1)->where('media_id',$rs)->count("*");
	       
	       $tm=$rs->push();
	       $rr=$tm->push()->select('')->select('type_id,count(media_id) as n')->group('type_id'); 
	       $types=$rr->fetchPairs('type_id');
	       
	       $rr=$tm->push()->where('evaluate_score>0')->select('')->select('max(evaluate_score) as max_score, min(evaluate_score) as min_score, avg(evaluate_score) as avg_score')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }
	       $rr=$this->app->orm->study()->where('media_id',$tm)->select('')->select('max(rating) as max_rating, avg(rating) as avg_rating, min(rating) as min_rating')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }
	       
	       $rr=$this->app->orm->study()->where('study.media_id',$tm)->select('sum(study.view_count) as views,count(study.user_id) as users')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }

	       $meta['users']=count($this->app->orm->study()->where('view_count>0')->where('media_id',$tm)->select('distinct(user_id)'));
	       $rr=$this->app->orm->{'(select media_id as media_id, sum(view_count) as view_count from study where view_count>0 group by media_id) as `tt`'}()->where('media_id',$tm)->select('max(view_count) as max_views, min(view_count) as min_views, avg(view_count) as avg_views')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }
	       $lup=$this->app->orm->group()->fetchPairs('group_id','name');
	       
		   $groups=array();
		   $rr=$this->app->orm->study()->where('media.media_id',$tm)->select('distinct(media.group_id) as group_id');
		   		   
		   foreach($rr as $a){
		  	 $rr2=$this->app->orm->study()->where('media.group_id', $a['group_id'])->where('media.media_id',$tm)->select('media.media_id, media.topic, media.group_id,media.user_id, sum(study.view_count) as view_count')->order('view_count desc')->group('study.media_id','view_count>0')->limit(10);
		
			 if(count($rr2)){

			 	if(!isset($groups[$a['group_id']])){
			 		$groups[$a['group_id']]=array();
			 	}
			 	foreach($rr2 as $r){
			 		$groups[$a['group_id']][]=$r->toArray();
			 	}
			 }
		   }

		   
		   $subs=(string) $this->app->orm->study()->where('study.media_id',$tm)->select('')->select('study.media_id, avg(study.rating) as rating,sum(view_count) as views,media.topic, media.type_id, media.group_id')->order('rating desc')->group('')->group('study.media_id','rating IS NOT NULL');
	       $xmeta=array();
	       $gs=$this->app->orm->{'('. $subs .') as `tt`'}()->select('distinct(tt.group_id) as group_id');
	       foreach($gs as $g){
	       	   $gid=$g['group_id'];
	 		   $rr= $this->app->orm->{'('. $subs .') as `tt`'}()->select('tt.*')->where('tt.group_id',$gid)->limit(10);
	 		   if(count($rr)){
	 		   $xmeta[$gid]=$rr->toArray();
	 		   }
	       }

	       
	       $rs->limit($this->param['count'],(($this->param['page']-1) * $this->param['count']));
	       $n=$rs->count("*");
	       $rows=$this->app->orm->toArray($rs);
	       if(count($rows)>0){
	       		$ids=array();
	       		foreach($rows as &$row){
	       		$ids[]=$row['media_id'];
	       		}
	       		$tm=$this->app->orm->study()->where('media_id',$ids)->select('count(user_id) as count_users, sum(view_count) as count_views, avg(rating) as count_rating,sum(like_count) as count_like, sum(unlike_count) as count_unlike')->group('media_id');
	       		$tm2=$tm->fetchPairs('media_id');
	       		foreach($rows as &$row){
	       			if(isset($tm2[$row['media_id']])){
	       				foreach($tm2[$row['media_id']] as $a=>$b){
	       					$row[$a]=$b;
	       				}
	       			}
	       		}
	       }
	       
			$meta['total']=$n;

	       //create charts
	       $meta['chart_views']=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงข้อมูลการเข้าดูสื่อ'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		            'colors'=>array('#00aa00','#0000aa','#aa0000'),
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} ครั้ง'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array('สถิติการเข้าดู'),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนครั้งที่เข้าดู'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> array(
		        	array(
		            'name'=> 'สูงสุด',
		            'data'=> array(intval($meta['max_views']))
		            ),
		        	array(
		            'name'=> 'เฉลี่ย',
		            'data'=> array(intval($meta['avg_views']))
		            ),
		        	array(
		            'name'=> 'ต่ำสุด',
		            'data'=> array(intval($meta['min_views']))
		            ),	
		         )	                   
	       );
       $meta['chart_evaluates']=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงข้อมูลการประเมินสื่อ'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		            'colors'=>array('#00aa00','#0000aa','#aa0000'),
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y:.1f}<br>คะแนน'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array('การประเมินโดยคณะกรรมการ','การประเมินโดยผู้ใช้'),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'คะแนนที่ได้'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> array(
		        	array(
		            'name'=> 'สูงสุด',
		            'data'=> array(floatval($meta['max_score']),floatval($meta['max_rating']))
		            ),
		        	array(
		            'name'=> 'เฉลี่ย',
		            'data'=> array(floatval($meta['avg_score']),floatval($meta['avg_rating']))
		            ),
		        	array(
		            'name'=> 'ต่ำสุด',
		            'data'=> array(floatval($meta['min_score']),floatval($meta['min_rating']))
		            ),	
		         )	                   
	       );
       $meta['chart_medias']=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงข้อมูลจำนวนสื่อ'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		            'colors'=>array('#555555','#00aa00','#aa0000','#0000aa'),
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true, 
		                        'format'=>'{y} สื่อ'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array(''),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนสื่อ'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> array(
		        	array(
		            'name'=> 'ทั้งหมด',
		            'data'=> array(intval($meta['total']))
		            ),		        
		        	array(
		            'name'=> 'ผ่านการประเมินแล้ว',
		            'data'=> array(intval($meta['active']))
		            ),
		        	array(
		            'name'=> 'ยังไม่ผ่านการประเมิน',
		            'data'=> array(intval($meta['inactive']))
		            ),
		        	array(
		            'name'=> 'ยังไม่ได้ประเมิน',
		            'data'=> array($meta['total'] - ($meta['active'] + $meta['inactive']))
		            ),	
		         )	                   
	       ); 			

	       $charts=array();
	       $cs=explode(',',"#7cb5ec,#90ed7d,#f7a35c,#8085e9,#f15c80,#e4d354,#2b908f,#f45b5b,#91e8e1,#434348");
	       $csi=count($cs);
	       $idx=-1;
	       foreach($groups as $k=>$gp){
	       		$idx++;
				$its=array();
				$titles= array();
				foreach($gp as $it){
					$titles[]=$it['media_id']."<br>".$it['topic'];
					$its[]=intval($it['view_count']);
				}

$colors=array();
$rnd=array(array_rand($cs,1));
$colors[]=$cs[$idx % $csi];
$its=array(
		array(
			'showInLegend'=> false,  
            'name'=> '',
            'data'=> $its
        )
       );
       $title=$k.'-???';
       if(isset($lup[$k])) $title=$lup[$k];
       
				$charts[]=array(
			 	'title'=>array(
		            'text'=> $title
		        ),	
		        	 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'bar'

		            ),
		            'colors'=>$colors,	
		            'plotOptions'=>array(
		                'bar'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} ครั้ง'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> $titles,
		            'title'=>array(
		                'text'=>''
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนครั้งเข้าดู'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),        
 				'series'=> $its 
 				);		       
	       }
	       $meta['charts']=$charts;
	       
	       $charts=array();
	      
	       $csi=count($cs);
	       $idx=-1;
	       foreach($xmeta as $k=>$gp){
	       		$idx++;
				$its=array();
				$titles= array();
				foreach($gp as $it){
					$titles[]=$it['media_id']."<br>".$it['topic'];
					$its[]=intval($it['rating']);
				}

$colors=array();
$rnd=array(array_rand($cs,1));
$colors[]=$cs[$idx % $csi];
$its=array(
		array(
			'showInLegend'=> false,  
            'name'=> '',
            'data'=> $its
        )
       );
       $title=$k.'-???';
       if(isset($lup[$k])) $title=$lup[$k];
       
				$charts[]=array(
			 	'title'=>array(
		            'text'=> $title
		        ),	
		        	 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'bar'

		            ),
		            'colors'=>$colors,	
		            'plotOptions'=>array(
		                'bar'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} คะแนน'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> $titles,
		            'title'=>array(
		                'text'=>''
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'คะแนน'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),        
 				'series'=> $its 
 				);		       
	       }
	       $meta['charts2']=$charts;	   
	       
	       //pie chart
$item_types=array();
	$xtypes=array(
			array('type_id'=>'1', 'name'=> 'สื่อประเภทวีดีโอ'),
			array('type_id'=>'2','name'=> 'สื่อประเภทเสียง' ),
			array('type_id'=>'3','name'=> 'สื่อประเภทเอกสาร' )
	);
	
foreach($xtypes as $x){
	if(isset($types[$x['type_id']])){
		$b=$types[$x['type_id']];
		unset($types[$x['type_id']]);
	}else{
		$b=array();
		$b['n']=0;

	}
	$item_types[]=array('name'=> $x['name'],'data'=> array(intval($b['n'])));
}
if(count($types)){
	$n=0;
	foreach($types as $a){
		$n+= intval($a['n']);
	}
	if($n>0) $item_types[]=array('name'=> 'อื่นๆ','data'=> array($n));
}




$type_charts=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงข้อมูลประเภทของสื่อ'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		      
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true, 
		                        'format'=>'{y} สื่อ'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array(''),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนสื่อ'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> $item_types	                   
	       ); 

    
    		$meta['chart_types']=$type_charts;
			$meta['evalcharts']=$evalcharts;
    	           
			
	       return array('error'=>false ,'__raw'=>true,'total'=>$n, 'meta'=>$meta, 'page'=>$this->param['page'],'data'=>&$rows);    
	  }

  function add(){
    return false;
  }
  function update(){
    return false;
  }  
  function delete(){
    return false;
  }  

  
}

class NGTABLE_REPORT_MEDIA2 extends NGTABLE{

  function __construct($app, $table='media', $pk='media_id') {
    parent::__construct($app, $table, $pk);
  }
  function buildFilter(&$rs, $fs){
  	if($rs){
  		if($fs){
  			foreach( array('level_id','group_id','user_id','subject_id','type_id') as $f){
  				if(isset($fs[$f]) && ($fs[$f]!='')){
  					$rs->where($f, $fs[$f]);
  				}
  			}

  			$str='date';
			if(!(isset($fs[$str]) && $fs[$str])){
				$fs[$str]=date('Y-m-1');
			}
  			if(isset($fs[$str]) && $fs[$str]){
  				$df=$this->app->orm->date_info($fs[$str]);
  				$rs->where('date_created>=?',$df['begin']);
				$rs->where('date_created<=?',$df['end']);
  			}
  			
  		}
  		if(isset($fs['search']) && $fs['search']){
  			$str='%' . $fs['search']. '%';
  			$rs->where('(topic LIKE ?) OR (media_id LIKE ?) OR (description LIKE ?)',array($str,$str,$str));
  		}
  		$rs->order('date_created desc,topic');
  	}
  }
  function thmonth($str){
	if($str){
		$ms=explode('_','มกราคม_กุมภาพันธ์_มีนาคม_เมษายน_พฤษภาคม_มิถุนายน_กรกฎาคม_สิงหาคม_กันยายน_ตุลาคม_พฤศจิกายน_ธันวาคม');
		$d=strtotime($str);
		$n=intval(date('m',$d))-1;
		$str=$ms[$n]. ' ';
		$str.=date('Y',$d);
		return $str;
	}
	return '';
  }
  function getmeta($rs){
			$meta=array('q'=>array(),'max_score'=>0,'min_score'=>0,'avg_score'=>0,'max_rating'=>0,'avg_rating'=>0,'min_rating'=>0,'max_views'=>0,'min_views'=>0,'avg_views'=>0);
			//sumary
	       $meta['active']=$this->app->orm->{$this->tb}()->where('active',1)->where('media_id',$rs)->count("*");
	       $meta['inactive']=$this->app->orm->{$this->tb}()->where('(active IS NULL) OR (active!=?)',1)->where('media_id',$rs)->count("*");
	       
	       $tm=$rs->push();
	       $rr=$tm->push()->select('')->select('type_id,count(media_id) as n')->group('type_id'); 
	       $types=$rr->fetchPairs('type_id');
	       $meta['q']['types']=$types;
	       $rr=$tm->push()->where('evaluate_score>0')->select('')->select('max(evaluate_score) as max_score, min(evaluate_score) as min_score, avg(evaluate_score) as avg_score')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }
	       $rr=$this->app->orm->study()->where('media_id',$tm)->select('')->select('max(rating) as max_rating, avg(rating) as avg_rating, min(rating) as min_rating')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }
	       
	       $rr=$this->app->orm->study()->where('study.media_id',$tm)->select('sum(study.view_count) as views,count(study.user_id) as users')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }

		   //teacher & student uses
		   $tusers=$this->app->orm->user()->select('user_id')->where('user_type','2');
		   $nsusers=$this->app->orm->user()->select('user_id')->where('user_type!=?','0');

		   $meta['teacher_views']=0;
		   $meta['student_views']=0;
		   $meta['teacher_count']=0;
		   $meta['student_count']=0;
	       $rr=$this->app->orm->study()->where('study.media_id',$tm)->where('study.user_id',$tusers)->select('sum(study.view_count) as teacher_views')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }
	       $rr=$this->app->orm->study()->where('study.media_id',$tm)->where('NOT study.user_id',$nsusers)->select('sum(study.view_count) as student_views')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }

	       $rr=$this->app->orm->study()->where('study.media_id',$tm)->where('study.user_id',$tusers)->where('study.view_count>0')->select('distinct(study.user_id) as teacher_count');
		   $meta['teacher_count']=count($rr);
		   
	       $rr=$this->app->orm->study()->where('study.media_id',$tm)->where('NOT study.user_id',$nsusers)->where('study.view_count>0')->select('distinct (study.user_id) as student_count');
	       $meta['student_count']=count($rr);

	       $meta['users']=count($this->app->orm->study()->where('view_count>0')->where('media_id',$tm)->select('distinct(user_id)'));
	       $rr=$this->app->orm->{'(select media_id as media_id, sum(view_count) as view_count from study where view_count>0 group by media_id) as `tt`'}()->where('media_id',$tm)->select('max(view_count) as max_views, min(view_count) as min_views, avg(view_count) as avg_views')->fetch();
	       if($rr){
	       	foreach($rr as $a=>$b){
	       		$meta[$a]=$b;
	       	}
	       }
	       $lup=$this->app->orm->group()->fetchPairs('group_id','name');
	        $meta['q']['lup']=$lup;
		   $groups=array();
		   $rr=$this->app->orm->study()->where('media.media_id',$tm)->select('distinct(media.group_id) as group_id');
		   		   
		   foreach($rr as $a){
		  	 $rr2=$this->app->orm->study()->where('media.group_id', $a['group_id'])->where('media.media_id',$tm)->select('media.media_id, media.topic, media.group_id,media.user_id, sum(study.view_count) as view_count')->order('view_count desc')->group('study.media_id','view_count>0')->limit(10);
		
			 if(count($rr2)){

			 	if(!isset($groups[$a['group_id']])){
			 		$groups[$a['group_id']]=array();
			 	}
			 	foreach($rr2 as $r){
			 		$groups[$a['group_id']][]=$r->toArray();
			 	}
			 }
		   }

		   
		   $subs=(string) $this->app->orm->study()->where('study.media_id',$tm)->select('')->select('study.media_id, avg(study.rating) as rating,sum(view_count) as views,media.topic, media.type_id, media.group_id')->order('rating desc')->group('')->group('study.media_id','rating IS NOT NULL');
	       $xmeta=array();
	       $gs=$this->app->orm->{'('. $subs .') as `tt`'}()->select('distinct(tt.group_id) as group_id');
	       foreach($gs as $g){
	       	   $gid=$g['group_id'];
	 		   $rr= $this->app->orm->{'('. $subs .') as `tt`'}()->select('tt.*')->where('tt.group_id',$gid)->limit(10);
	 		   if(count($rr)){
	 		   $xmeta[$gid]=$rr->toArray();
	 		   }
	       }

	       
	       $rs->limit($this->param['count'],(($this->param['page']-1) * $this->param['count']));
	       $n=$rs->count("*");
	       $rows=$this->app->orm->toArray($rs);
	       if(count($rows)>0){
	       		$ids=array();
	       		foreach($rows as &$row){
	       		$ids[]=$row['media_id'];
	       		}
	       		$tm=$this->app->orm->study()->where('media_id',$ids)->select('count(user_id) as count_users, sum(view_count) as count_views, avg(rating) as count_rating,sum(like_count) as count_like, sum(unlike_count) as count_unlike')->group('media_id');
	       		$tm2=$tm->fetchPairs('media_id');
	       		foreach($rows as &$row){
	       			if(isset($tm2[$row['media_id']])){
	       				foreach($tm2[$row['media_id']] as $a=>$b){
	       					$row[$a]=$b;
	       				}
	       			}
	       		}
	       }
	        $meta['q']['groups']=$groups;
			$meta['q']['xmeta']=$xmeta;
			$meta['total']=$n;
			return $meta;
  }
	function query(){

			$month1='';
			$month2='';
	       $params=null;
	       $rs=$this->app->orm->{$this->tb}();
		   $rs2=$this->app->orm->{$this->tb}();

		   if(isset($this->param['filter'])){
			   if(isset($this->param['filter']['date_from'])){
			    $month1=$this->param['filter']['date_from'];
			   }
			   if(isset($this->param['filter']['date_to'])){
			   $month2=$this->param['filter']['date_to'];
			   }
		   }
		   if(empty($month1)) $month1=date('Y-m-1');
		   if(empty($month2)){
			$month2=date('Y-m-1');
		   }
		 

	       if(isset($this->param['filter']) && is_array($this->param['filter'])){
	       	$params=$this->param['filter'];
			$this->param['filter']['date']=$month1;
	        $this->buildFilter($rs, $this->param['filter']);
			$this->param['filter']['date']=$month2;
	        $this->buildFilter($rs2, $this->param['filter']);
	       }else{
	        $this->buildFilter($rs,null);
			 $this->buildFilter($rs2,null);
	       }
	       
		    $month1=$this->thmonth($month1);
			$month2=$this->thmonth($month2);
			$meta=$this->getmeta($rs);
			$meta2=$this->getmeta($rs2);
			$types=$meta['q']['types'];
			$lup=$meta['q']['lup'];
			$groups=$meta['q']['groups'];
			$xmeta=$meta['q']['xmeta'];
			$types2=$meta2['q']['types'];
			$lup2=$meta2['q']['lup'];
			$groups2=$meta2['q']['groups'];
			$xmeta2=$meta2['q']['xmeta'];
			unset($meta['q']);
			unset($meta2['q']);

			//teacher and student

	       //create charts
	
	       $meta['chart_uses']=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงการเปรียบเทียบจำนวนผู้ใช้สื่อของครูกับนักเรียน'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		            'colors'=>array('#0000aa','#00aa00'),
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} คน'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array($month1,$month2),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนคนที่เข้าดู'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> array(
		        	array(
		            'name'=> 'ครูเข้าดูสื่อ',
		            'data'=> array(intval($meta['teacher_count']), intval($meta2['teacher_count']))
		            ),
		        	array(
		            'name'=> 'นักเรียนเข้าดูสื่อ',
		            'data'=> array(intval($meta['student_count']), intval($meta2['student_count']))
		            )
		         )	                   
	       );
	       $meta['chart_uses2']=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงการเปรียบเทียบจำนวนการเข้าดูสื่อของครูกับนักเรียน'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		            'colors'=>array('#0000aa','#00aa00'),
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} ครั้ง'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array($month1,$month2),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนครั้งที่เข้าดู'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> array(
		        	array(
		            'name'=> 'ครูเข้าดูสื่อ',
		            'data'=> array(intval($meta['teacher_views']), intval($meta2['teacher_views']))
		            ),
		        	array(
		            'name'=> 'นักเรียนเข้าดูสื่อ',
		            'data'=> array(intval($meta['student_views']), intval($meta2['student_views']))
		            )
		         )	                   
	       );
	       //create charts
	       $meta['chart_views']=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงข้อมูลการเข้าดูสื่อ'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		            'colors'=>array('#00aa00','#0000aa','#aa0000'),
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} ครั้ง'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array($month1,$month2),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนครั้งที่เข้าดู'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> array(
		        	array(
		            'name'=> 'สูงสุด',
		            'data'=> array(intval($meta['max_views']), intval($meta2['max_views']))
		            ),
		        	array(
		            'name'=> 'เฉลี่ย',
		            'data'=> array(intval($meta['avg_views']), intval($meta2['avg_views']))
		            ),
		        	array(
		            'name'=> 'ต่ำสุด',
		            'data'=> array(intval($meta['min_views']), intval($meta2['min_views']))
		            ),	
		         )	                   
	       );


			$meta['chart_evaluates']=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงข้อมูลการประเมินสื่อ'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		            'colors'=>array('#00aa00','#0000aa','#aa0000'),
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y:.1f}<br>คะแนน'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array('การประเมินโดยคณะกรรมการ '.$month1,'การประเมินโดยคณะกรรมการ '.$month2,'การประเมินโดยผู้ใช้ '.$month1,'การประเมินโดยผู้ใช้ '.$month2),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'คะแนนที่ได้'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> array(
		        	array(
		            'name'=> 'สูงสุด',
		            'data'=> array(floatval($meta['max_score']),floatval($meta2['max_score']),floatval($meta['max_rating']),floatval($meta2['max_rating']))
		            ),
		        	array(
		            'name'=> 'เฉลี่ย',
		            'data'=> array(floatval($meta['avg_score']),floatval($meta2['avg_score']),floatval($meta['avg_rating']),floatval($meta2['avg_rating']))
		            ),
		        	array(
		            'name'=> 'ต่ำสุด',
		            'data'=> array(floatval($meta['min_score']),floatval($meta2['min_score']),floatval($meta['min_rating']),floatval($meta2['min_rating']))
		            ),	
		         )	                   
	       );

	
       $meta['chart_medias']=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงข้อมูลจำนวนสื่อ'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		            'colors'=>array('#555555','#00aa00','#aa0000','#0000aa'),
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true, 
		                        'format'=>'{y} สื่อ'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array($month1,$month2),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนสื่อ'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> array(
		        	array(
		            'name'=> 'ทั้งหมด',
		            'data'=> array(intval($meta['total']),intval($meta2['total']))
		            ),		        
		        	array(
		            'name'=> 'ผ่านการประเมินแล้ว',
		            'data'=> array(intval($meta['active']),intval($meta2['active']))
		            ),
		        	array(
		            'name'=> 'ยังไม่ผ่านการประเมิน',
		            'data'=> array(intval($meta['inactive']),intval($meta2['inactive']))
		            ),
		        	array(
		            'name'=> 'ยังไม่ได้ประเมิน',
		            'data'=> array($meta['total'] - ($meta['active'] + $meta2['inactive']))
		            ),	
		         )	                   
	       ); 			

	       $charts=array();
	       $cs=explode(',',"#7cb5ec,#90ed7d,#f7a35c,#8085e9,#f15c80,#e4d354,#2b908f,#f45b5b,#91e8e1,#434348");
	       $csi=count($cs);
	       $idx=-1;
	       foreach($groups as $k=>$gp){
	       		$idx++;
				$its=array();
				$titles= array();
				foreach($gp as $it){
					$titles[]=$it['media_id']."<br>".$it['topic'];
					$its[]=intval($it['view_count']);
				}

$colors=array();
$rnd=array(array_rand($cs,1));
$colors[]=$cs[$idx % $csi];
$its=array(
		array(
			'showInLegend'=> false,  
            'name'=> '',
            'data'=> $its
        )
       );
       $title=$k.'-???';
       if(isset($lup[$k])) $title=$lup[$k];
       
				$charts[]=array(
			 	'title'=>array(
		            'text'=> $title
		        ),	
		        	 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'bar'

		            ),
		            'colors'=>$colors,	
		            'plotOptions'=>array(
		                'bar'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} ครั้ง'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> $titles,
		            'title'=>array(
		                'text'=>''
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนครั้งเข้าดู'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),        
 				'series'=> $its 
 				);		       
	       }
	       $meta['charts']=$charts;
	       
	       $charts=array();
	      
	       $csi=count($cs);
	       $idx=-1;
	

	       foreach($xmeta as $k=>$gp){
	       		$idx++;
				$its=array();
				$titles= array();
				foreach($gp as $it){
					$titles[]=$it['media_id']."<br>".$it['topic'];
					$its[]=intval($it['rating']);
				}
					   
$colors=array();
$rnd=array(array_rand($cs,1));
$colors[]=$cs[$idx % $csi];
$its=array(
		array(
			'showInLegend'=> false,  
            'name'=> '',
            'data'=> $its
        )
       );
       $title=$k.'-???';
       if(isset($lup[$k])) $title=$lup[$k];
       
				$charts[]=array(
			 	'title'=>array(
		            'text'=> $title
		        ),	
		        	 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'bar'

		            ),
		            'colors'=>$colors,	
		            'plotOptions'=>array(
		                'bar'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true,
		                        'format'=>'{y} คะแนน'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> $titles,
		            'title'=>array(
		                'text'=>''
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'คะแนน'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),        
 				'series'=> $its 
 				);		       
	       }
	       $meta['charts2']=$charts;	   
	       //pie chart
$item_types=array();
	$xtypes=array(
			array('type_id'=>'1', 'name'=> 'สื่อประเภทวีดีโอ'),
			array('type_id'=>'2','name'=> 'สื่อประเภทเสียง' ),
			array('type_id'=>'3','name'=> 'สื่อประเภทเอกสาร' )
	);
	
foreach($xtypes as $x){
	if(isset($types[$x['type_id']])){
		$b=$types[$x['type_id']];
		unset($types[$x['type_id']]);
	}else{
		$b=array();
		$b['n']=0;

	}
	if(isset($types2[$x['type_id']])){
		$b2=$types2[$x['type_id']];
		unset($types2[$x['type_id']]);
	}else{
		$b2=array();
		$b2['n']=0;

	}
	$item_types[]=array('name'=> $x['name'],'data'=> array(intval($b['n']), intval($b2['n'])));
}
if(count($types)){
	$n=0;
	foreach($types as $a){
		$n+= intval($a['n']);
	}
	$n2=0;
	foreach($types2 as $a){
		$n2+= intval($a['n']);
	}
	if($n>0 || n2>0){
		$item_types[]=array('name'=> 'อื่นๆ','data'=> array($n, $n2));
	}
}




$type_charts=array(
			 	'title'=>array(
		            'text'=> 'แผนภูมิแสดงข้อมูลประเภทของสื่อ'
		        ),			 
		        'options'=> array(
		            'chart'=>array(
		                'type'=> 'column'

		            ),
		      
		            'plotOptions'=>array(
		                'column'=>array(
		                    'dataLabels'=>array(
		                        'enabled'=> true, 
		                        'format'=>'{y} สื่อ'
		                    )
		            
		                )
		            )
		        ),
		        'xAxis'=>array(
		            'categories'=> array($month1, $month2),
		            'title'=>array(
		                'text'=>null
		            )
		        ),
		        'yAxis'=>array(
		            'min'=> 0,
		            'title'=>array(
		                'text'=> 'จำนวนสื่อ'
		            ),
		            'labels'=>array(
		                'overflow'=> 'justify'
		            )
		        ),		        
		        'legend'=>array(
		            'layout'=> 'vertical',
		            'align'=> 'right',
		            'verticalAlign'=> 'top',
		            'floating'=> true,
		            'borderWidth'=> 1,
		            'shadow'=>true
		        ),		        
		        'series'=> $item_types	                   
	       ); 

    
    		$meta['chart_types']=$type_charts;
	
    	           
			
	       return array('error'=>false ,'__raw'=>true,'total'=>0, 'meta'=>$meta, 'page'=>1,'data'=>array());    
	  }

  function add(){
    return false;
  }
  function update(){
    return false;
  }  
  function delete(){
    return false;
  }  

  
}

class NGTABLE_ADMIN_USER extends NGTABLE{
  function __construct($app, $table='user', $pk='user_id',$register=false) {
    $this->required[]='first_name';
    $this->required[]='last_name';

    $this->uniques['user_id']='เลขประจำตัวผู้ใช้ซ้ำ กรุณาเปลี่ยนใหม่';
    $this->roles[]='admin';
    parent::__construct($app, $table, $pk, $register);
  }
  function buildFilter(&$rs, $fs){
    if($rs){
      if($fs){   
        foreach( array('level_id','group_id','user_type','active') as $f){
          if(isset($fs[$f]) && ($fs[$f]!='')){
            $rs->where($f, $fs[$f]);
          }
        }
      }
      if(isset($fs['search']) && $fs['search']){
        $str='%' . $fs['search']. '%';
        $rs->where('(first_name LIKE ?) OR (last_name LIKE ?)',array($str,$str));    
      } 
      $rs->select('user_id,first_name,last_name,title,user_type,active,group_id,level_id');    
    }
  } 
  function get($id=null){
  	$r=parent::get($id);
  	if($r && isset($r['user_id'])){
  		$r['_id_']=$r['user_id'];
  	}
    return $r;
  }  
  function update(){
  	  global $UPLOADS_DIR;
      $this->app->api_require_fields($this->required);
	  $key='_id_';
	  
	  $new_id=null;
	  if(isset($this->param[$key]) && $this->param[$key]){
	  	$new_id=$this->param[$this->pk];
	  	$this->param[$this->pk]=$this->param[$key];
	  	$this->param[$key]=$new_id;
	  }
	  
      foreach($this->uniques as $fd=>$err){
        if(isset($this->param[$fd]) && $this->param[$fd]){
          $b=$this->param[$fd];
          if($fd==$this->pk) $b=$this->param['_id_'];
          
          $rs=$this->app->orm->{$this->tb}()->where($fd, $b)->where($this->pk .' != ?', $this->param[$this->pk])->limit(1);
          if(count($rs)){
			return array('__raw'=>true, 'error'=>true, 'message'=>$err, '__code'=>400);
          }
        }
      }
      unset($this->param[$key]);
      
      $rs=$this->app->orm->{$this->tb}()->where($this->pk, $this->param[$this->pk])->limit(1);
      if(count($rs)){
        $obj=$rs->fetch();
        $old_id=null;
        if($new_id){
        	$old_id=$this->param[$this->pk];
        	$this->param[$this->pk]=$new_id;
        }
        $affected = $obj->update($this->param);
        $r=array();
        if($affected>0){
          $rs=$this->app->orm->{$this->tb}()->where($this->pk, $this->param[$this->pk])->limit(1);
          $obj=$rs->fetch();
          foreach($this->param as $a=>$b){
            if(isset($obj[$a])){
              $r[$a]=$obj[$a];
            }
          }
          if($old_id) $r['_id_']=$old_id;
          if($old_id && $new_id && $old_id!=$new_id){
          	//rename thumb
          	$uploadsDir=$UPLOADS_DIR;
          	$f=$uploadsDir. 'photos/'. $old_id .'.jpg';
          	if(file_exists($f)){
          		@rename($f, $uploadsDir. 'photos/'. $new_id .'.jpg');
          	}
          	$f=$uploadsDir. $old_id;
          	if(file_exists($f)){
          		@rename($f, $uploadsDir. $new_id );
          	}      
          	//change ids
          	$p=array('user_id'=>$new_id);
          	$this->app->orm->study('user_id', $old_id)->update($p);
          	$this->app->orm->evaluate_form('user_id', $old_id)->update($p);
          	$this->app->orm->media('user_id', $old_id)->update($p);
          	$this->app->orm->score('user_id', $old_id)->update($p);
          	$this->app->orm->comment('user_id', $old_id)->update($p);
          }
          return $r;
        }else{
          $r[$this->pk]=$this->param[$this->pk];
          return $r;
        }
      }
      return false;
  }  
  function can_delete(){
		$rs=$this->app->orm->study()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;
		$rs=$this->app->orm->comment()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;
		$rs=$this->app->orm->evaluate_form()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;	
		$rs=$this->app->orm->score()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;	
		return true;
  } 
  function delete_links($ids){
  	global $UPLOADS_DIR;
  	if($ids){
  		$this->app->orm->transaction='BEGIN';
		$this->app->orm->study()->where($this->pk,$ids)->delete();
		$this->app->orm->comment()->where($this->pk,$ids)->delete();
		$this->app->orm->evaluate_form()->where($this->pk,$ids)->delete();
		$this->app->orm->score()->where($this->pk,$ids)->delete();
  		$this->app->orm->transaction='COMMIT';
  		
  		//delete folder
  		if($UPLOADS_DIR){
	  		$fds=$ids;
	  		if(!is_array($ids)){
	  			$fds=array();
	  			$fds[]=$ids;
	  		}
	  		foreach($fds as $fd){
	  			@unlink($UPLOADS_DIR . 'photos/'. $fd . '.jpg');
	  			del_tree($UPLOADS_DIR . $fd);
	  		}
  		}
  	}
  }
  function delete(){

		$usr=$this->app->auth->getUser();
		$user_id=($usr)?$usr['user_id']:'0';  
		
		$rs=$this->app->orm->{$this->tb}()->where($this->pk,$this->param[$this->pk]);
		$rs->where($this->pk,$user_id);

		$arl=$this->param[$this->pk];
		if(!is_array($arl)){
			$arl=array();
			$arl[]=$this->param[$this->pk];
		}
		
		if( count($rs)>0){
			if(in_array($user_id, $arl)){
				return array('__raw'=>true, 'error'=>true, 'message'=>'เกิดข้อผิดพลาด ไม่สามารถลบตัวเองออกจากระบบได้', '__code'=>400);
			}
		}

	 	return parent::delete();
  }
  function custom_process($action){
  	if($action=='status'){
		$usr=$this->app->auth->getUser();
		$user_id=($usr)?$usr['user_id']:'0';  
		$rs=$this->app->orm->{$this->tb}()->where($this->pk.'!=?', $user_id)->where($this->pk,$this->param[$this->pk])->update(array('active'=>$this->param['active']));
		return  $rs;	
  	}
  	return false;
  }
}

class NGTABLE_ADMIN_LEVEL extends NGTABLE{
	function __construct($app, $table='level', $pk='level_id') {
		$this->required[]='name';	
		$this->uniques['name']='ชื่อระดับชั้นซ้ำ กรุณาเปลี่ยนใหม่';	
		$this->uniques['code']='รหัสชั้นเรียนซ้ำ กรุณาเปลี่ยนใหม่';		
		parent::__construct($app, $table, $pk);
	}
	function can_delete(){
		$rs=$this->app->orm->user()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		return (count($rs)<1);
	}
		
}
class NGTABLE_ADMIN_EVALUATE_GROUP extends NGTABLE{
	function __construct($app, $table='evaluate_group', $pk='evaluate_group_id') {
		$this->required[]='name';	
		$this->uniques['name']='ชื่อหัวข้อเรื่องที่ประเมินซ้ำ กรุณาเปลี่ยนใหม่';		
		parent::__construct($app, $table, $pk);
	}
	function buildFilter(&$rs, $fs){
		if($rs){

      if($fs && isset($fs['search'])){
        $rs->where('evaluate_group.name LIKE ?','%' . $fs['search']. '%');
        $rs->or('evaluate_topic.name LIKE ?','%' . $fs['search']. '%');
      }  

			$rs->select('evaluate_group.*, evaluate_topic.evaluate_topic_id, evaluate_topic.name as topic');
		}
	}
  	function query(){
       $rs=$this->app->orm->join_table($this->tb,'left join evaluate_topic on evaluate_group.evaluate_group_id=evaluate_topic.evaluate_group_id');
 		$rs->order($this->pk.' desc');
       if(isset($this->param['filter']) && is_array($this->param['filter'])){
        $this->buildFilter($rs, $this->param['filter']);
       }else{
        $this->buildFilter($rs,null);
       }

       if(isset($this->param['sorting']) && is_array($this->param['sorting'])){
        $b=true;
        foreach($this->param['sorting'] as $k=>$v){
          if($b){
            $b=false;
            $rs->order('');
          }
          $rs->order($k . ' '  .$v);
        }
       }
       $rs->limit($this->param['count'],(($this->param['page']-1) * $this->param['count']));
       $n=$rs->count("*");

       $rows=$this->app->orm->toArray($rs);
       return array('error'=>false ,'__raw'=>true,'total'=>$n,  'page'=>$this->param['page'],'data'=>&$rows);    
  	}	
	function can_delete(){
		$rs=$this->app->orm->evaluate_topic()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		return (count($rs)<1);
	}
	  function delete_links($ids){
	  	if($ids){
	  		$this->app->orm->transaction='BEGIN';
			$this->app->orm->evaluate_topic()->where($this->pk,$ids)->delete();
	  		$this->app->orm->transaction='COMMIT';
	  	}
	  }			  	
}
class NGTABLE_ADMIN_EVALUATE_TOPIC extends NGTABLE{
	function __construct($app, $table='evaluate_topic', $pk='evaluate_topic_id') {
		$this->required[]='name';	
		$this->uniques['name']='ชื่อหัวข้อย่อยเรื่องที่ประเมินซ้ำ กรุณาเปลี่ยนใหม่';		
		parent::__construct($app, $table, $pk);
	}
	function can_delete(){
		$rs=$this->app->orm->evaluate_form_detail()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		return (count($rs)<1);
	}	
	  function delete_links($ids){
	  	if($ids){
	  		$this->app->orm->transaction='BEGIN';
			$this->app->orm->evaluate_form_detail()->where($this->pk,$ids)->delete();
	  		$this->app->orm->transaction='COMMIT';
	  	}
	  }		
	function buildFilter(&$rs, $fs){
		if($rs){
				foreach( array('evaluate_group_id') as $f){
					if(isset($fs[$f]) && ($fs[$f]!='')){
						$rs->where($f, $fs[$f]);
					}
				}
      if($fs && isset($fs['search'])){
        $rs->where('name LIKE ?','%' . $fs['search']. '%');
        $rs->or('name LIKE ?','%' . $fs['search']. '%');
      }  

			
		}
	}
}

class NGTABLE_ADMIN_GROUP extends NGTABLE{
	function __construct($app, $table='`group`', $pk='group_id') {
		$this->required[]='name';
		$this->uniques['name']='ชื่อกลุ่มสาระซ้ำ กรุณาเปลี่ยนใหม่ ';		
		parent::__construct($app, $table, $pk);
	}
	function can_delete(){
		$rs=$this->app->orm->user()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		return (count($rs)<1);
	}
	
	function query(){
		return parent::query();
	}
}

class NGTABLE_ADMIN_SUBJECT extends NGTABLE{
	function __construct($app, $table='subject', $pk='subject_id') {
		$this->required[]='name';
		$this->uniques['name']='ชื่อวิชาเรียนซ้ำ กรุณาเปลี่ยนใหม่ ';		
		//$this->uniques['code']='รหัสวิชาเรียนซ้ำ กรุณาเปลี่ยนใหม่ ';		
		parent::__construct($app, $table, $pk);
	}
	function can_delete(){
		$rs=$this->app->orm->media()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		return (count($rs)<1);
	}
}

class NGTABLE_TEACHER_MEDIA extends NGTABLE{
	function __construct($app, $table='media', $pk='media_id') {
		$this->required[]='topic';
		parent::__construct($app, $table, $pk);
	}
	function buildFilter(&$rs, $fs){
		if($rs){
			if($fs){
				foreach( array('level_id','group_id','subject_id') as $f){
					if(isset($fs[$f]) && ($fs[$f]!='')){
						$rs->where($f, $fs[$f]);
					}
				}
			}
			if(isset($fs['search']) && $fs['search']){
				$str='%' . $fs['search']. '%';
				$rs->where('topic LIKE ?',$str);
			}
			$usr=$this->app->auth->getUser();
			$user_id=($usr)?$usr['user_id']:'0';
			$rs->where('user_id',$user_id);

		}
	}

	function update(){
		if($this->param){
			$b=false;
			if(isset($this->param['user_id'])){
				$b=(intval($this->param['user_id']) >0);
			}
			if(!$b){
				$usr=$this->app->auth->getUser();
				$this->param['user_id']=($usr)?$usr['user_id']:'0';
			}
			
			$this->param['date_modified']= new NotORM_Literal("NOW()");
			
		
			return parent::update();
		}
		return false;
	}	
	function add(){
		if($this->param){
			$b=false;
			if(isset($this->param['user_id'])){
				$b=(intval($this->param['user_id']) >0);
			}
			if(!$b){
				$usr=$this->app->auth->getUser();
				$this->param['user_id']=($usr)?$usr['user_id']:'0';
			}
			if((!isset($this->param['media_id'])) || empty($this->param)){
				$level=$this->app->orm->level('level_id',$this->param['level_id'])->fetch();
				$subject=$this->app->orm->subject('subject_id',$this->param['subject_id'])->fetch();
				if(!$level) $level['code']='XXX';
				if(!$subject) $subject['code']='XXX';
				$type='XXX';
				switch($this->param['type_id']){
					case 1:
					$type='VDO';
					break;
					case 2:
					$type='MP3';
					break;
					case 3:
					$type='DOC';
					break;
				}
				$key=strtoupper(sprintf("%s%s%s",$type,$subject['code'],$level['code']));
				$mid=$this->app->orm->media()->where('media_id LIKE ?',$key .'%')->max('media_id');
				$id=0;
				if($mid){
					$id=intval(substr($mid,strlen($key)));
				}
				$id++;
				$this->param['media_id']=sprintf('%s%04s',$key,$id);
			}
			
			
			return parent::add();
		}
		return false;
	}
  function can_delete(){
		$rs=$this->app->orm->study()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;
		$rs=$this->app->orm->comment()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;
		$rs=$this->app->orm->evaluate_form()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;	
		$rs=$this->app->orm->score()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;
		return true;
  } 
  function before_delete($rs){
  	$this->data=$rs->fetchPairs('media_id');
  }
  function delete_links($ids){
  	if($ids && $this->data){
  		global $UPLOADS_DIR;
  		//delete folder
  		if($UPLOADS_DIR){
	  		$fds=$ids;
	  		if(!is_array($ids)){
	  			$fds=array();
	  			$fds[]=$ids;
	  		}
	  		foreach($fds as $fd){
	  			if(isset($this->data[$fd])){
	  				$url=$this->data[$fd]['url'];
	  				if($url){
	  					@unlink($UPLOADS_DIR . "{$this->data[$fd]['user_id']}/". $url);
	  				}
	  				$url=md5($this->data[$fd]['media_id']).'.thumb.jpg';
	  				@unlink($UPLOADS_DIR . "{$this->data[$fd]['user_id']}/". $url);
	  				@unlink($UPLOADS_DIR . "{$this->data[$fd]['user_id']}/sm/". $url);
	  			
	  			}
	  		}
  		}
  	}
  }
}
class NGTABLE_ADMIN_MEDIA extends NGTABLE{
	function __construct($app, $table='media', $pk='media_id') {
		$this->required[]='topic';
		parent::__construct($app, $table, $pk);
	}
	function buildFilter(&$rs, $fs){
		if($rs){
			if($fs){
				foreach( array('level_id','group_id','subject_id','user_id') as $f){
					if(isset($fs[$f]) && ($fs[$f]!='')){
						$rs->where($f, $fs[$f]);
					}
				}
			}
			if(isset($fs['search']) && $fs['search']){
				$str='%' . $fs['search']. '%';
				$rs->where('topic LIKE ?',$str);
			}

		}
	}

	function update(){
		if($this->param){
			$b=false;
		
			$this->param['date_modified']= new NotORM_Literal("NOW()");

			return parent::update();
		}
		return false;
	}
  function can_delete(){
		$rs=$this->app->orm->study()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;
		$rs=$this->app->orm->comment()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;
		$rs=$this->app->orm->evaluate_form()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;	
		$rs=$this->app->orm->score()->select($this->pk)->where($this->pk,$this->param[$this->pk])->limit(1);
		if(count($rs)>0) return false;
		return true;
  } 
 
  function before_delete($rs){
  	$this->data=$rs->fetchPairs('media_id');
  }
  function delete_links($ids){
  	if($ids && $this->data){
  		global $UPLOADS_DIR;
  		//delete folder
  		if($UPLOADS_DIR){
	  		$fds=$ids;
	  		if(!is_array($ids)){
	  			$fds=array();
	  			$fds[]=$ids;
	  		}
	  		foreach($fds as $fd){
	  			if(isset($this->data[$fd])){
	  				$url=$this->data[$fd]['url'];
	  				if($url){
	  					@unlink($UPLOADS_DIR . "{$this->data[$fd]['user_id']}/". $url);
	  				}
	  				$url=md5($this->data[$fd]['media_id']).'.thumb.jpg';
	  				@unlink($UPLOADS_DIR . "{$this->data[$fd]['user_id']}/". $url);
	  				@unlink($UPLOADS_DIR . "{$this->data[$fd]['user_id']}/sm/". $url);
	  			
	  			}
	  		}
  		}
  	}
  }   	
}
class NGTABLE_USER_COMMENT extends NGTABLE{
	function __construct($app, $table='comment', $pk='comment_id') {
		$this->required[]='comment';
		parent::__construct($app, $table, $pk);
	}
	function buildFilter(&$rs, $fs){
		if($rs){
			if($fs){
				foreach( array('media_id') as $f){
					if(isset($fs[$f]) && ($fs[$f]!='')){
						$rs->where('media.'.$f, $fs[$f]);
					}
				}
			}
			$usr=$this->app->auth->getUser();	
			$user_id=($usr)?$usr['user_id']:'0';	
			$role=$this->app->auth->getRole();
			
			if($role!='admin'){	
				if($role=='teacher'){
					$rs2=$this->app->orm->media()->select('media_id')->where('user_id',$user_id)->where('media_id',$fs['media_id'])->limit(1);
					$n=count($rs2);
					$rs->and('(comment.hide!=?) OR (comment.user_id=?) OR ?',array(1,$user_id ,$n));
				}else{	
					$rs->and('(comment.hide!=?) OR (comment.user_id=?)',array(1,$user_id));
				}
			}
			$rs->order('');
			$rs->order('comment.comment_id desc');
			$rs->select('comment.*,user.title,user.first_name,user.last_name,media.user_id as author_id');
			
		}
	}	
	function add(){
		if($this->param){
			$usr=$this->app->auth->getUser();	
			$this->param['user_id']=($usr)?$usr['user_id']:'0';
			$it=parent::add();
			if($it && $usr){
				$it['first_name']=$usr['first_name'];
				$it['last_name']=$usr['last_name'];
				$it['title']=$usr['title'];
				$it['user_id']=$this->param['user_id'];
			}
			return $it;
		}
		return false; 
	}
}



function importUserFromExcel($app, $inputFileName, $param=null){

	if(!class_exists('PHPExcel_IOFactory')){
		require_once 'phar://' . dirname(__DIR__). '/Excel/phpexcel.phar';
	}
	//  Read your Excel workbook
	try {
    	$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
    	$objPHPExcel = $objReader->load($inputFileName);

		//  Get worksheet dimensions
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
	
	    $fields=array('user_id','title','first_name','last_name','email');
	    $visible_fields=array();
	    $result=array();
		for ($row = 1; $row <= $highestRow; $row++) {
		    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			if(empty($visible_fields)){
		    	foreach($rowData[0] as $k=>$v){
		    		if(is_string($v)){
		    			$v=trim($v);
		    		}else{
		    			$v=(string) $v;
		    		}
		        	if(in_array($v,$fields)){
		        		$visible_fields[$v]=$k;
		        	}
		        }
			}else{
				if(!(isset($visible_fields['user_id']) && isset($visible_fields['title']) && isset($visible_fields['first_name']) && isset($visible_fields['last_name']))){
					echo "require fields (user_id, title, first_name, last_name)";
					break;
				}
		    	$it=array();
	        	foreach($visible_fields as $a=>$b){
	        		$v=$rowData[0][$b];
	        		if(is_string($v)){
	        			$v=trim($v);
	        		}else{
	        			$v=(string) $v;
	        		}
	        		$it[$a]=$v;
	        	}
		        if($it['user_id'] && $it['title'] && $it['first_name'] && $it['last_name']){
		        	$result[$it['user_id']]=$it;
		        }else{
		        	unset($it);
		        }
		        
			}
		}

		
		if(!empty($result)){
			$password='password';
			$group_id=0;
			$level_id=0;
			$user_type=1;
			$active=1;
			if($param){
				$k='password';
				if(isset($param[$k])){
					$password=$param[$k];
				}
				$k='level_id';
				if(isset($param[$k])){
					$level_id=$param[$k];
				}
				$k='level_id';
				if(isset($param[$k])){
					$level_id=$param[$k];
				}
				$k='user_type';
				if(isset($param[$k])){
					$user_type=$param[$k];
				}	
				$k='active';
				if(isset($param[$k])){
					$active=$param[$k];
				}
			}
			
			$rs=$app->orm->user()->select('user_id')->where('user_id',array_keys($result));
			$pks=array();
			$count_exists=count($rs);
			if($count_exists){
				$pks=$rs->fetchPairs('user_id');
			}
			$app->orm->transaction='BEGIN';
			$rs=$app->orm->user();
			$count_insert=0;

			foreach($result as $row){
				if(isset($row['user_id'])){
				if(!isset($pks[$row['user_id']])){
					$row['password']=$password;
					$row['user_type']=$user_type;
					$row['level_id']=$level_id;
					$row['group_id']=$group_id;
					$row['active']=$active;

					$rs->insert($row);	

					$count_insert++;
				}
				}
			}
			$app->orm->transaction='COMMIT';
			return array('rows'=>$highestRow, 'cols'=>$highestColumn, 'count'=>count($result), 'inserts'=>$count_insert, 'exists'=>$count_exists, 'error'=>false);
		}
	} catch (Exception $e) {
	    
	}
			
	return array('rows'=>$highestRow, 'cols'=>$highestColumn, 'count'=>0, 'inserts'=>0, 'exists'=>0, 'error'=>true);
}


class NGTABLE_HISTORY extends NGTABLE{
  function __construct($app, $table='history', $pk='media_id') {
    parent::__construct($app, $table, $pk);
  }

	function query(){

		$usr=$this->app->auth->getUser();
		$user_id=($usr)?$usr['user_id']:'0'; 
	       $rs=$this->app->orm->join_table('media','inner join study on media.media_id=study.media_id');
 		   $rs->select('media.*,study.last_used,study.view_count,study.like_count,study.unlike_count,study.rating')->where('study.user_id',$user_id)->where('study.view_count>?',0)->order('study.last_used desc');
	       $rs->limit($this->param['count'],(($this->param['page']-1) * $this->param['count']));
	       $n=$rs->count("*");
	       $rows=$this->app->orm->toArray($rs);
	       return array('error'=>false ,'__raw'=>true,'total'=>$n, 'page'=>$this->param['page'],'data'=>&$rows);    
	  }

	  function add(){
	    return false;
	  }
	  function update(){
	    return false;
	  }  
	  function delete(){
	    return false;
	  }  

}

class NGTABLE_HISTORY_EVALUATE extends NGTABLE{
  function __construct($app, $table='history_evaluate', $pk='media_id') {
    parent::__construct($app, $table, $pk);
  }

	function query(){

		$usr=$this->app->auth->getUser();
		$user_id=($usr)?$usr['user_id']:'0'; 
	       $rs=$this->app->orm->join_table('media','inner join evaluate_form on media.media_id=evaluate_form.media_id');
 		   $rs->select('media.*,evaluate_form.date_modified,evaluate_form.avg_score')->where('evaluate_form.user_id',$user_id)->order('evaluate_form.date_modified desc');
	       $rs->limit($this->param['count'],(($this->param['page']-1) * $this->param['count']));
	       $n=count($rs);
	       $rows=$this->app->orm->toArray($rs);
		   $meta=array();
		   $meta['pass']=$this->app->orm->evaluate_form()->where('user_id',$user_id)->where('avg_score>=3')->count('media_id');
	       $meta['nopass']=$n-$meta['pass']; 

	       return array('error'=>false ,'__raw'=>true,'total'=>$n, 'meta'=>$meta, 'page'=>$this->param['page'],'data'=>&$rows);    
	  }

	  function add(){
	    return false;
	  }
	  function update(){
	    return false;
	  }  
	  function delete(){
	    return false;
	  }  

}
