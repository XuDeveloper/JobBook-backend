<?php
namespace Home\Controller;

use Home\Model\CominfoModel;
use Home\Model\JobinfoModel;
use Think\Controller;

/**
 * 就业信息
 */
class JobController extends Controller
{
    //获取所有岗位
    public function getAll($index = 0)
    {
        // $index=file_get_contents('php://input');
        //$index=0;
        try {
            $job             = new JobinfoModel();
            $company         = new CominfoModel();
            $getSearchResult = $job->getSearch("", "", $index);

            /*下面代码对接联系公司*/
            for ($k = 0; $k < count($getSearchResult); $k++) {
                $getSearchResult[$k]['comlogo'] = $company->getCompanyIcon($getSearchResult[$k]['companyname']);
                //$getSearchResult[$k]['company']=$company->getCompany($getSearchResult[$k]['companyname']);
            }
            //var_dump($getSearchResult);
            $this->feedback(C('SUCCESS_CODE'), '', $getSearchResult);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), '');
        }
    }

    //获取推荐岗位
    public function getRecommend($index = 0)
    {
        // $index=file_get_contents('php://input');
        //$index=0;
        try {
            $job             = new JobinfoModel();
            $company         = new CominfoModel();
            $getSearchResult = $job->getSearch("", "", $index);

            /*下面代码对接联系公司*/
            for ($k = 0; $k < count($getSearchResult); $k++) {
                $getSearchResult[$k]['comlogo'] = $company->getCompanyIcon($getSearchResult[$k]['companyname']);
                //$getSearchResult[$k]['company']=$company->getCompany($getSearchResult[$k]['companyname']);
            }
            //var_dump($getSearchResult);
            $this->feedback(C('SUCCESS_CODE'), '', $getSearchResult);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), '');
        }
    }

    //获取某条记录的具体信息
    public function getDetail($job_id, $account = "")
    {
        try {
            $job    = new JobinfoModel();
            $result = $job->getJobInfo($job_id);

            $company           = new CominfoModel();
            $result['company'] = $company->getCompany($result['companyname']);

            $result['company']['comments'] = array();
            unset($result['companyname']);

            if (empty($account)) {
                $result['ifLike'] = 0;
            } else {
                $result['ifLike'] = (int) $job->checkLikeJob($account, $job_id);
            }
            //var_dump($result);
            $this->feedback(C('SUCCESS_CODE'), '', $result);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), '');
        }
    }

    //查询岗位
    public function search($location = "", $type = "", $index = 0)
    {
        // $index=file_get_contents('php://input');
        //$index=0;
        try {
            $job             = new JobinfoModel();
            $company         = new CominfoModel();
            $getSearchResult = $job->getSearch("", "", $index);

            /*下面代码对接联系公司*/
            for ($k = 0; $k < count($getSearchResult); $k++) {
                $getSearchResult[$k]['comlogo'] = $company->getCompanyIcon($getSearchResult[$k]['companyname']);
                //$getSearchResult[$k]['company']=$company->getCompany($getSearchResult[$k]['companyname']);
            }
            //var_dump($getSearchResult);
            $this->feedback(C('SUCCESS_CODE'), '', $getSearchResult);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), '');
        }
    }

    public function liked($job_id, $account = "")
    {
        try {
            $data = array(
                'user'   => $account,
                'job_id' => $job_id,
            );
            $result = M('favourite')->add($data);

            if ($result) {
                $this->feedback(C('SUCCESS_CODE'), '', '');
            } else {
                $this->feedback(C('JOB_LIKE_ERROR_CODE'), $result, $getSearchResult);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), '');
        }

        //exit(json_encode($back));
    }

    public function unliked($job_id, $account = "")
    {
        try {
            $where['user']   = $account;
            $where['job_id'] = $job_id;
            $result          = M('favourite')->where($where)->delete();
            if ($result) {
                $this->feedback(C('SUCCESS_CODE'), '', '');
            } else {
                $this->feedback(C('JOB_UNLIKE_ERROR_CODE'), $result, $getSearchResult);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), '');
        }

        // exit(json_encode($back));
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
