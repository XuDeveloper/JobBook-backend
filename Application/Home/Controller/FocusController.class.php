<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\FocusModel;
/**
* 
*/
class FocusController extends Controller
{
	public function getHisQuestion($account){
		$focus = new FocusModel();
		$data = $focus->getQuestions($account);
		var_dump($data);
		$this->feedback("true",json_encode($data));
	}

	//数据反馈（公共方法）
	public function feedback($code,$message,$data){
		$back['code']=$code;
		$back['message']=$message;
		$back['data']=$data;
		exit(json_encode($back));
	}
}