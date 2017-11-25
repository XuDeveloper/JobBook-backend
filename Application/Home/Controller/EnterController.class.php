<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\FocusModel;
use Home\Model\SquareModel;
use Home\Model\UserModel;
/**
* 
*/
class EnterController extends Controller
{
	
	public function doRegister()
	{
		//$json='{"account":"Jophis","password":"633004","telephone":"633004","userName":"hhh"}';  //测试数据
		$json = file_get_contents('php://input');
		$sourceData=json_decode($json,true);
		if (empty($json)){
            $this->feedback(C('PERSON_REGISTER_ERROR_CODE'), "register data is null!", null);
		}   

        $account=$sourceData['account'];
		$password=$sourceData['password'];
		$userName=$sourceData['username'];
		$telephone=$sourceData['telephone'];
        $devicetoken=$sourceData['devicetoken'];

        //检测用户是否被注册
        $where['account']=$account;
        $check=M('user')->where($where)->count('account');
        $result=array();
        $defaultHead="http://".C('server_address').'/jobBook/Public/src/head/default.jpg';
        if ($check==0) {
            //用户还没被注册
		    $data=array(
                'account'=>$account,
                'password'=>$password,
                'username'=>$userName,
                'head'=>$defaultHead,
                'login_time'=>date('Y-m-d'),
                'devicetoken'=>$devicetoken,
            );
		    $insert=M('user')->add($data);
		    if (!$insert) {
		    	//注册失败
                $this->feedback(C('PERSON_REGISTER_ERROR_CODE'), $insert, null);           
		    }else{
		    	//注册成功
		        $result['account']=$account;
                $result['password']=$password;
                $result['username']=$userName;
                $result['head']=$defaultHead;
                $result['telephone']="";
                $result['workspace']="";
                $result['workposition']="";
                $result['follow']=0;
                $result['fans']=0;
                $result['moment']=0;
                $this->feedback(C('SUCCESS_CODE'), 'register success', $result);
		    }
        }else{
        	//用户已被注册
            $this->feedback(C('PERSON_REGISTER_ERROR_CODE'), "Have Registered!", null);        		    
        }
    }

    public function doLogin(){
    	//$json='{"account":"Xu","password":"125463","telephone":"452109"}';
    	$json=file_get_contents('php://input');
        $sourceData=json_decode($json,true);
        // echo $json;
        $where['account']=$sourceData['account'];
        $where['password']=$sourceData['password'];
		$data=M('user')->where($where)->select();
		if (count($data)==1) {
            $where['account']=$sourceData['account'];
            $update['login_time']=date('Y-m-d');
            $update['devicetoken']=$sourceData['devicetoken'];
            $updateTime = M('user')->where($where)->save($update);
            if ($updateTime || $updateTime==0) {
                $user = new UserModel();
                $square = new SquareModel();
                $result=array(
                    'account'=>$sourceData['account'],
                    'password'=>$sourceData['password'],
                    'username'=>$data[0]['username'],
                    'head'=>$data[0]['head'],
                    'telephone'=>$date[0]['telephone'],
                    'workspace'=>$data[0]['workspace'],
                    'workposition'=>$data[0]['workposition'],
                    'follow'=>$user->countFollow($sourceData['account']),
                    'fans'=>$user->countFans($sourceData['account']),
                    'moment'=>$square->getMoments($sourceData['account']),
                    );
                $this->feedback(C('SUCCESS_CODE'), 'login success', $result); 
            }else{
                $this->feedback(C('PERSON_LOGIN_ERROR_CODE'), "user update error!", null);
            }		
		}else{
            $this->feedback(C('PERSON_LOGIN_ERROR_CODE'), "can not find user!", null);
		}
    }

    public function checkLogin($account){
        $where['account']=$account;
        $last_login_time=M('user')->field('login_time')->where($where)->select();
        if((strtotime(date('y-m-d'))-strtotime($last_login_time[0]['login_time']))<604800){
            //最近有登录
            $update['login_time']=date('Y-m-d');
            $result = M('user')->where($where)->save($update);
            $this->feedback(C('SUCCESS_CODE'), 'can login auto', null); 
        }else{
            //最近一周无登录
            $this->feedback(C('PERSON_AUTO_LOGIN_ERROR_CODE'), 'no login auto', $result); 
        }
    }

    public function checkforget($account){
    	$check['account']=$account;
    	$result=M('user')->where($check)->select();
    	if (count($result)==0) {
    		$this->feedback(C('PERSON_REGISTER_ERROR_CODE'), 'no registered', $result); 
    	}else{
    		$this->feedback(C('SUCCESS_CODE'), 'checkforget', null); 
    	}
    }

    public function forgetpsw($account,$newpsw){
    	$where['account']=$account;
    	$update['password']=md5($newpsw);
    	$result=M('user')->where($where)->save($update);
        $this->feedback(C('SUCCESS_CODE'), 'forgetpsw', null); 
    }

    //数据反馈（公共方法）
    public function feedback($code,$message,$data){
        $back['code']=$code;
        $back['message']=$message;
        $back['data']=$data;
        exit(json_encode($back));
    }
}