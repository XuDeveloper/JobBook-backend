<?php 
namespace Home\Model;
use Think\Model;
/**
* 
*/
class FocusModel extends Model
{
	// public function getQuestions($account){
	// 	$where['author']=$account;
	// 	$data = M('square')->field('content')->where($where)->select();
	// 	return $data;
	// }

	// public function getFocus($account){
	// 	$where['follow']=$account;
	// 	$data = M('focus')->where($where)->field('fans,focustime')->limit(0,10)->select();
	// 	return $data;
	// }

/*查看被关注列单*/
	public function getFocused($account){
		$where['fans']=$account;
		$data = M('focus')->where($where)->field('follow,focustime')->limit(0,10)->select();
		for ($i=0; $i < count($data); $i++) { 			
			$data[$i]['time']=$data[$i]['focustime'];
			unset($data[$i]['focustime']);
			$data[$i]['id']=$data[$i]['follow'];
			unset($data[$i]['follow']);
			$data[$i]['type']=1;
		}
		return $data;
	}

/*查看点赞列单*/
	public function getLikes($account){
		$where['author']=$account;
		$s_ids = M('square')->where($where)->field('s_id')->select();
		$result = array();
		for ($i=0; $i < count($s_ids); $i++) { 
			$check['s_id']=$s_ids[$i]['s_id'];
			$temp = M('likes')->where($check)->select();
			$result = array_merge($result,$temp);
		}
		for ($i=0; $i < count($result); $i++) {
			$result[$i]['time']=$result[$i]['liketime'];
			unset($result[$i]['liketime']);
			$result[$i]['id']=$result[$i]['s_id'];
			unset($result[$i]['s_id']);
			$result[$i]['type']=2;
			unset($result[$i]['account']);
		}
		return $result;
	}

/*查看评论列单*/
	public function getComments($account){
		$where['account']=$account;
		$data = M()->table(array('comment'=>'c','square'=>'s'))->where(array('c.s_id=s.s_id','author='.$account))->limit(0,10)->field('c.s_id,ask_time')->select();
		for ($i=0; $i < count($data); $i++) {			
			$data[$i]['time']=$data[$i]['ask_time'];
			unset($data[$i]['ask_time']);
			$data[$i]['id']=$data[$i]['s_id'];
			unset($data[$i]['s_id']);
			$data[$i]['type']=3;
		}
		return $data;
	}

	public function getUserInfo($account){
		$where['account']=$account;
		$data = M('user')->where($where)->select();
		$data[0]['followNum']=$this->getFocus($account);
		$data[0]['fansNum']=$this->getFocused($account);
		return $data[0];
	}

}

?>