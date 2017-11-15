<?php
namespace Home\Controller;

use Home\Model\SquareModel;
use Think\Controller;
use Think\Model;

/**
 * 简历类
 */
class CvController extends Controller
{
    public function postCV($account)
    {
        try {
            $json          = file_get_contents('php://input');
            $where['user'] = $account;
            // $json='{"city":"aaa","company":"asdasfdqew","disabilitylevel":"一级","disabilitytype":"视力残疾","education":[{"admissiondate":"2017-01","graduationdate":"2017-04","major":"computer","school":"qawdas"}],"email":"aaa","expectlocation":"aaa","expectposition":"aaa","expectsalary":"aaa","havedisabilitycard":"有","head":"http://115.28.202.143/jobBook/Public/src/head/2016-09-04/57cb9214f0044.jpg","name":"raymond","position":"aefae","sex":"男","status":"qwed","telephone":"aaa","work":[{"company":"aaa","dimissiondate":"2016-09","inaugurationdate":"2016-09","position":"aaa"}]}';

            $model = new Model();
            $model->startTrans();
            // var_dump(json_decode($json,true)
            $sourceData = json_decode($json, true);

            $source  = $sourceData['education'];
            $delete1 = $model->table('education')->where($where)->delete();
            for ($i = 0; $i < count($source); $i++) {
                $data = array(
                    'user'           => $account,
                    'admissionDate'  => $source[$i]['admissiondate'],
                    'graduationDate' => $source[$i]['graduationdate'],
                    'school'         => $source[$i]['school'],
                    'major'          => $source[$i]['major'],
                );
                $result = $model->table('education')->add($data); //把数据插进去
                //出现插入数据失败，就把标记flag改成false
                if ($result === false || $delete1 === false) {
                    $model->rollback();
                    $this->feedback(C('CV_UPDATE_ERROR_CODE'), "education data update error!", '');
                }
            }

            $source  = $sourceData['work'];
            $delete2 = $model->table('work')->where($where)->delete();
            for ($i = 0; $i < count($source); $i++) {
                $data = array(
                    'user'             => $account,
                    'inaugurationDate' => $source[$i]['inaugurationdate'],
                    'dimissionDate'    => $source[$i]['dimissiondate'],
                    'company'          => $source[$i]['company'],
                    'position'         => $source[$i]['position'],
                );
                $result = $model->table('work')->add($data);
                if ($result === false || $delete2 === false) {
                    $model->rollback();
                    $this->feedback(C('CV_UPDATE_ERROR_CODE'), "work data update error!", '');
                }
            }

            $data = array(
                'user'               => $account,
                'sex'                => $sourceData['sex'],
                'email'              => $sourceData['email'],
                'city'               => $sourceData['city'],
                'telephone'          => $sourceData['telephone'],
                'disabilityType'     => $sourceData['disabilitytype'],
                'disabilityLevel'    => $sourceData['disabilitylevel'],
                'haveDisabilityCard' => $sourceData['havedisabilitycard'],
                'expectSalary'       => $sourceData['expectsalary'],
                'expectPosition'     => $sourceData['expectposition'],
                'expectLocation'     => $sourceData['expectlocation'],
                'status'             => $sourceData['status'],
            );
            $delete3 = $model->table('personality')->where($where)->delete();
            $result  = $model->table('personality')->add($data);
            if ($result === false || $delete3 === false) {
                $model->rollback();
                $this->feedback(C('CV_UPDATE_ERROR_CODE'), "personality data update error!", '');
            }

            $update['workspace']    = $sourceData['workspace'];
            $update['workposition'] = $sourceData['workposition'];
            $update['username']     = $sourceData['name'];
            $check['account']       = $account;
            $result                 = $model->table('user')->where($check)->save($update);
            if ($result === false) {
                $model->rollback();
                $this->feedback(C('CV_UPDATE_ERROR_CODE'), "user data update error!", '');
            }

            $model->commit();
            $square = new SquareModel();
            $data   = $square->getUserInfo($account);
            // var_dump($data);
            $this->feedback(C('SUCCESS_CODE'), '', $data);

        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), '');
        }
    }

    public function getcv($account)
    {
        try {
            $where['user']    = $account;
            $basicInfo        = M('personality')->where($where)->select();
            $education        = M('education')->field(array('admissiondate', 'graduationdate', 'school', 'major'))->where($where)->select();
            $work             = M('work')->field(array('inaugurationdate', 'dimissiondate', 'company', 'position'))->where($where)->select();
            $check['account'] = $account;
            $attach           = M('user')->where($check)->field('head,username,workspace,workposition')->select();
            if (count($basicInfo[0]) == 0) {
                $this->feedback(C('CV_GET_ERROR_CODE'), "No CV!", '');
            } else {
                $result                 = $basicInfo[0];
                $result['head']         = $attach[0]['head'];
                $result['workspace']    = $attach[0]['workspace'];
                $result['workposition'] = $attach[0]['workposition'];
                $result['education']    = $education;
                $result['name']         = $attach[0]['username'];
                $result['work']         = $work;
                //unset($result['basicInfo']['user']);
                //var_dump($result);
                $this->feedback(C('SUCCESS_CODE'), '', $result);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), '');
        }
    }

    //数据反馈（公共方法）
    public function feedback($code, $message, $data)
    {
        $back['code']    = $code;
        $back['message'] = $message;
        $back['data']    = $data;
        exit(json_encode($back));
    }

}
