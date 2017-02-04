<?php


function __($string){
	if($translator = \FDT2k\Noctis\Core\Env::getTranslator()){
		return $translator->getTranslation($string);
	}else{
		return $string;
	}
}

function array_flip_key($array,$key){
	$result = array();
	if (is_array($array)){
		foreach($array as $k => $value){
			$result[$value[$key]]=$value;
		}
	}
	return $result;
}

function isEmpty($val) {
	//var_dump($val,empty($val) && $val !== 0 && $val !=="0");
	#if(!is_string($val)){return false;}
	if(is_string($val)){
		$val = trim($val);
	}
	return empty($val) && $val !== 0 && $val !=="0";
}



function array_form_to_assoc($data,$refkey){
	$new_array = array();

	$keys = array_keys($data);

	foreach($data[$refkey] as $index => $value){
		$item = array();
		foreach($keys as $key){
			$item[$key]= $data[$key][$index];
		}
		$new_array[] = $item;
		
	}

	return $new_array;
}
