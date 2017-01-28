<?php

namespace FDT2k\Noctis\Core;



class HistoryItem extends \IObject{

}

class History{
	public $current;
	protected $history;
	protected $history_ordered;

	public function __construct(){
		//$this->action = $action;

		$this->history = &$_SESSION['history'];
		$this->history_ordered = &$_SESSION['history_ordered'];
		//var_dump(Env::getRoute()->bundle);
	}

	public function reset(){
		$this->history = array();
		$this->history_ordered = array();
		return $this;
	}
	public function store($label,$get=array(),$post= array()){
		$r = Env::getRoute();
		//var_dump($r->getQS());

		//var_dump($get);
		$this->storeHistory(ucFirst($label),$r->action,$r->module,$r->bundle,$get,$post);
	}

	public function storeHistory($desc,$action,$module,$bundle,$qs,$post){
// 		$datas=array('string'=>$desc,'action'=>Env::getAction(),'module'=>$current_bundle);
		$current_bundle = Env::getRoute()->bundle;
		$item = new HistoryItem();

		$item->setLabel($desc);
		$item->setAction($action);
		$item->setModule($module);
		$item->setBundle($bundle);
		$item->setQuerystring($qs);
		$item->setPost($post);
	//	var_dump($item);
		$key = md5(serialize($item));
/*
		$datas['string']=$desc;
		$datas['action']=$action;
		$datas['module']=$module;
		$datas['bundle']=$bundle;
		$datas['qs']=$qs;
		$key= md5(serialize($datas)); //we don't build the md5 with the params
		$datas['params']=serialize($post);
		$datas['posted_datas']="";*/
		$datas = $item;

		if(!@in_array($key,$this->history_ordered[$current_bundle])){
			$this->history_ordered[$current_bundle][] = $key;
			$this->history[$current_bundle][$key] = $datas;

		} else { // on efface l'historique jusqu'a revenir a ce point précis (l'actuel)
			// on met a jour les parametres
			$this->history[$current_bundle][$key] = $datas;

			$bClear = false;
			foreach ($this->history_ordered[$current_bundle] as  $datas){
				if(!$bClear){ // on parcours la liste tant qu'on a pas trouvé le point actuel.
					$tmp[]=$datas; // on stocke la clé pour plus tard

					if($key == $datas){ // on a atteint le point actuel
						$this->history_ordered[$current_bundle] = $tmp; //on met a jour la liste triée
						$bClear = true;
					}
				}else{
					unset($this->history[$current_bundle][$datas]);
				}
			}
		}

		//if we are not the first step we store the posted data to the previous step
	/*
		// this is not used anymore I think (2015-08-4)
		$ar = $this->history_ordered[$current_bundle];
		$previous = sizeof($ar)-2;
		if(isset($ar[$previous])){
			$this->history[$current_bundle][$ar[$previous]]['posted_datas']=serialize($params);
		}
		*/

		//wtf ?   fuck you future me !
		// okay past me, you got me (2015-08-4)
		// this has no fucking sense.
		/*$tmp = array();
		if(is_array($this->history_ordered)){
			foreach ($this->history_ordered[$current_bundle] as $key ){
		//	var_dump($key,$this->history[$key]['string']);
				$tmp[]=array('key'=>$key,'string'=>$this->history[$current_bundle][$key]->getLabel());
			}
		}
		$this->current = $tmp;
		*/


	}

	function getStates(){
		$tmp = array();
		$current_bundle = Env::getRoute()->bundle;
		if(is_array($this->history_ordered[$current_bundle])){
			foreach ($this->history_ordered[$current_bundle] as $key ){

				$tmp[]=array('key'=>$key,'module'=>$this->history[$current_bundle][$key]->getModule(),'string'=>$this->history[$current_bundle][$key]->getLabel());
			}
		}
		return $tmp;
	}


	public function getStoredState($key){
//var_dump($this->history[$current_bundle][$key]);
		$current_bundle = Env::getRoute()->bundle;
		return $this->history[$current_bundle][$key];
	}

	public function restoreState($key){

		$state = $this->getStoredState($key);
		if($state){
			Env::$post->setDatas($state->getPost());
			Env::$get->setDatas($state->getQuerystring());
			Env::getRoute()->action=$state->getAction();


			// delete everything after the current point
			$clear = false;
			$current_bundle = Env::getRoute()->bundle;
			$array = $this->history_ordered[$current_bundle];
			foreach ($array as $k=> $datas){
				if($key == $datas){

					$clear = true;
				}

				if($clear){
					unset($array[$k]);
				}

			}
			$this->history_ordered[$current_bundle]=$array;
			return $state;
		}
		return false;
	}

	public function getStateError(array $formDatas = array()){
//var_dump($formDatas);
		$datas = $this->history_ordered[$current_bundle];
		$key = $datas[sizeof($datas)-1];
		if(!empty($key)){
			if(sizeof($formDatas)>0){
				//injecting params

				$params = unserialize ($this->history[$current_bundle][$key]['params']);

				foreach($formDatas as $k => $value){
					if(!isset($params[$k])){
						$params[$k]=$value;
					}
				}

				$params = serialize($params);

				$this->history[$current_bundle][$key]['params']=$params;
			}
			return $key;
		}
		return false;
	}

	public function getLastState(array $formDatas = array()){
//var_dump($formDatas);
		$current_bundle = Env::getRoute()->bundle;
		$datas = $this->history_ordered[$current_bundle];
		$key = $datas[sizeof($datas)-1];

	#	var_dump( $this->history_ordered);
		if(!empty($key)){
			if(sizeof($formDatas)>0){
				//injecting params

				$params = unserialize ($this->history[$current_bundle][$key]['params']);

				foreach($formDatas as $k => $value){
					if(!isset($params[$k])){
						$params[$k]=$value;
					}
				}

				$params = serialize($params);

				$this->history[$current_bundle][$key]['params']=$params;
			}
			return $key;
		}
		return false;
	}

	public function getStateSuccess(){
		$datas = $this->history_ordered[$current_bundle];
		$key = $datas[sizeof($datas)-2];
		if(!empty($key)){
			return $key;
		}
		return false;
	}

	public function setError($key,$error){
		$this->history[$current_bundle][$key]['error']=$error;
	}

}
