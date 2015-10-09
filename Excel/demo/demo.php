<?php
$inputFileName = 'demo2.xls';
importUserFromExcel($inputFileName);

function importUserFromExcel($inputFileName){

	if(!class_exists('PHPExcel_IOFactory')){
		require_once 'phar://' . __DIR__. '/phpexcel.phar';
	}

	//  Read your Excel workbook
	try {
    	$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
    	$objPHPExcel = $objReader->load($inputFileName);
	} catch (Exception $e) {
	    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)  . '": ' . $e->getMessage());
	}

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
        		if(is_string($v)) $v=trim($v);
        		$it[$a]=$v;
        	}
	        if($it['user_id'] && $it['title'] && $it['first_name'] && $it['last_name']){
	        	$result[$it['user_id']]=$it;
	        }else{
	        	unset($it);
	        }
	        
		}
	}
	var_dump(array_keys($result));
}
