<?php
namespace Home\Controller;

use Home\Model\SquareModel;
use Home\Model\UserModel;
use Think\Controller;

/**
 * 问问
 */
class SquareController extends Controller
{
    //发布工作圈
    public function releaseSquare()
    {
        try {
//$json='{"author":{"account":"888","favourite":[],"password":"0a113ef6b61820daa5611c870ed8d5ee","telephone":"","textCVs":[],"userName":"Raymond","videoCVs":[]},"commentnum":0,"content":"888","s_id":0,"title":"888"}';
            $json       = file_get_contents('php://input');
            $sourceData = json_decode($json, true);

            $date = date('Y-m-d G:i:s');
            $data = array(
                'author'  => $sourceData['author']['account'],
                'content' => $sourceData['content'],
                'date'    => $date,
            );

            $square = new SquareModel();
            if ($square->addSquare($data)) {
                $this->feedback(C('SUCCESS_CODE'), "", null);
            } else {
                $this->feedback(C('SQUARE_RELEASE_ERROR_CODE'), "Release Failed", null);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
    }

    //获得广场的工作圈
    public function allSquares($account = "", $index = 0)
    {
        // $index=file_get_contents('php://input');
        try {
            //$index=4;
            $square = new SquareModel();
            $data   = $square->getSquares($index);
            $likes  = $square->getMyLikes($account);
            $data   = $this->dealSquare($account, $data, $likes);

            // for ($i=0; $i < count($data); $i++) {
            //     $data[$i]['date']=$this->countTime($data[$i]['date']);
            //     $data[$i]['commentNum']=(int)$square->countComments($data[$i]['s_id']);
            //     $data[$i]['likesNum']=(int)$square->countLikes($data[$i]['s_id']);
            //     $data[$i]['author']=$square->getUserInfo($data[$i]['author']);
            //     if (in_array($data[$i]['s_id'],$likes)) {
            //         $data[$i]['ifLike']=1;
            //     }else{
            //         $data[$i]['ifLike']=0;
            //     }
            // }
            //var_dump($data);
            $this->feedback(C('SUCCESS_CODE'), "", $data);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
    }

    //获得关注的工作圈
    public function focusSquare($account = "", $index = 0)
    {
        try {
            // $index=file_get_contents('php://input');
            //$index=0;
            $user    = new UserModel();
            $myFocus = $user->getMyFocus($account);
            if (count($myFocus) == 0) {
                // $data = array();
                $this->feedback(C('SUCCESS_CODE'), "", null);
            } else {
                $square = new SquareModel();
                $data   = $square->getFocusSquares($index, $myFocus);
                $likes  = $square->getMyLikes($account);
                $data   = $this->dealSquare($account, $data, $likes);

                // for ($i=0; $i < count($data); $i++) {
                //              $data[$i]['date']=$this->countTime($data[$i]['date']);
                //              $data[$i]['commentNum']=(int)$square->countComments($data[$i]['s_id']);
                //              $data[$i]['likesNum']=(int)$square->countLikes($data[$i]['s_id']);
                //              $data[$i]['author']=$square->getUserInfo($data[$i]['author']);
                //              if (in_array($data[$i]['s_id'],$likes)) {
                //                  $data[$i]['ifLike']=1;
                //              }else{
                //                  $data[$i]['ifLike']=0;
                //              }
                //             }
                //var_dump($data);
                $this->feedback(C('SUCCESS_CODE'), "", $data);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
    }

    //获得某条工作圈
    public function getSingleMoment($s_id, $account)
    {
        try {
            $square         = new SquareModel();
            $data           = $square->getOneMoment($s_id);
            $data['date']   = $this->countTime($data['date']);
            $temporary      = $square->getMyLikes($account);
            $data['author'] = $square->getUserInfo($data['author']);
            if (in_array($s_id, $temporary)) {
                $data['ifLike'] = 1;
            } else {
                $data['ifLike'] = 0;
            }
            // var_dump($temporary);
            //var_dump($data);
            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }

    //得到某个人的工作圈
    public function getHisSquare($his, $my = "")
    {
        try {
            $square = new SquareModel();
            $source = $square->getHis($his);
            $likes  = $square->getMyLikes($my);

            // for ($i=0; $i < count($source); $i++) {
            //      $source[$i]['date']=$this->countTime($source[$i]['date']);
            //     $source[$i]['commentNum']=(int)$square->countComments($source[$i]['s_id']);
            //     $source[$i]['likesNum']=(int)$square->countLikes($source[$i]['s_id']);
            //     $source[$i]['author']=$square->getUserInfo($source[$i]['author']);
            //     if (in_array($source[$i]['s_id'],$likes)) {
            //               $source[$i]['ifLike']=1;
            //           }else{
            //               $source[$i]['ifLike']=0;
            //           }
            // }
            $data = $this->dealSquare($his, $source, $likes);
            //var_dump($data);
            $this->feedback(C('SUCCESS_CODE'), "", $data);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
    }

    //处理工作圈
    public function dealSquare($account, $source, $likes)
    {
        $square = new SquareModel();
        $user   = new UserModel();
        for ($i = 0; $i < count($source); $i++) {
            $source[$i]['date']           = $this->countTime($source[$i]['date']);
            $source[$i]['commentNum']     = (int) $square->countComments($source[$i]['s_id']);
            $source[$i]['likesNum']       = (int) $square->countLikes($source[$i]['s_id']);
            $source[$i]['author']         = $square->getUserInfo($source[$i]['author']);
            $source[$i]['author']['type'] = (int) $user->ifLike($account, $source[$i]['author']['account']);
            if (in_array($source[$i]['s_id'], $likes)) {
                $source[$i]['ifLike'] = 1;
            } else {
                $source[$i]['ifLike'] = 0;
            }
        }
        return $source;
    }

    //计算当前时间与发表时间的差（公共方法）
    public function countTime($date)
    {
        $date = strtotime($date);
        $now  = strtotime(date('Y-m-d G:i:s'));
        if (($now - $date) > 86400) {
            return date('Y-m-d', $date);
        } else {
            if (($now - $date) > 3600) {
                return floor(($now - $date) / 3600) . "小时前";
            } else {
                if (($now - $date) > 60) {
                    return ceil(($now - $date) / 60) . "分钟前";
                } else {
                    return "刚刚";
                }
            }
        }
    }

    //得到某个工作圈的评论
    public function getComments($s_id, $index = 0)
    {
        try {
            $square   = new SquareModel();
            $comments = $square->gainComments($s_id, $index);

            for ($i = 0; $i < count($comments); $i++) {
                $comments[$i]['ask_time'] = $this->countTime($comments[$i]['ask_time']);
                $comments[$i]['applier']  = $square->getUserInfo($comments[$i]['applier']);
            }
            // var_dump($comments);
            $this->feedback(C('SUCCESS_CODE'), "", $comments);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
    }

    //添加评论
    public function comment()
    {
        try {
            $json = file_get_contents('php://input');
            //$json='{"applier":{"account":"888","favourite":[],"password":"0a113ef6b61820daa5611c870ed8d5ee","telephone":"","textCVs":[],"userName":"Raymond","videoCVs":[]},"bad":0,"comment_id":0,"content":"qishou111","good":0,"s_id":2}';
            $data['words'] = $json;
            M('temporary')->add($data);
            $sourceData = json_decode($json, true);

            $ask_time = date('Y-m-d G:i:s');
            $data     = array(
                's_id'     => $sourceData['s_id'],
                'content'  => $sourceData['content'],
                'ask_time' => $ask_time,
                'applier'  => $sourceData['applier']['account'],
            );

            $square = new SquareModel();
            if ($square->addComment($data)) {
                $this->feedback(C('SUCCESS_CODE'), "", $this->getSingleMoment($data['s_id'], $data['applier']));
            } else {
                $this->feedback(C('SQUARE_COMMENT_ERROR_CODE'), "fail to comment", null);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
    }

    //点赞工作圈
    public function likeSquare($s_id, $account)
    {
        try {
            $square = new SquareModel();
            $check  = $square->checkLikes($account, $s_id);
            if (count($check) == 0) {
                $data = array(
                    'account' => $account,
                    's_id'    => $s_id,
                );
                if ($square->addLikes($data)) {
                    $this->sendMessage($account, $s_id);
                    $this->feedback(C('SUCCESS_CODE'), "", $this->getSingleMoment($s_id, $account));
                } else {
                    $this->feedback(C('SQUARE_LIKE_ERROR_CODE'), "fail to likes", null);
                }
            } else {
                $this->feedback(C('SQUARE_HAS_LIKED_ERROR_CODE'), "has liked", null);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
    }

    public function sendMessage($my, $s_id)
    {
        $check['s_id']    = $s_id;
        $you              = M('square')->where($check)->getField('author');
        $where['account'] = $you;
        $devicetoken      = M('user')->where($where)->getField('devicetoken');
        //echo $devicetoken;
        $data = array(
            "type"        => 2,
            "accountFrom" => $my,
            "accountTo"   => $you,
            "event"       => $s_id,
            "time"        => date('Y-m-d'),
        );
        Vendor('AndroidMessage.Demo');
        $demo = new \Demo("588392cbf43e481fb7001e56", "azsotoh6owtb40cgzzbde1qqh2jbecjr");
        $d    = $demo->sendAndroidUnicast($devicetoken, $data);
        //print($d); 返回数据备用
    }

    //取消赞工作圈
    public function unlikeSquare($account, $s_id)
    {
        try {
            $square = new SquareModel();
            $check  = $square->deleteLikes($account, $s_id);
            if ($check) {
                $this->feedback(C('SUCCESS_CODE'), "", $this->getSingleMoment($s_id, $account));
            } else {
                $this->feedback(C('SQUARE_UNLIKE_ERROR_CODE'), "unlike failed", null);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
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

    // public function deal($comment_id,$account){
    //     $check['account']=$account;
    //     $cr=M('good')->where($check)->count();
    //     if ($cr==0) {
    //         $data=array(
    //             'account'=>$account,
    //             'comment_id'=>$comment_id,
    //             );
    //         $result=M('good')->add($data);
    //         if ($result) {
    //             $back['status']="true";
    //             $back['response']="";
    //         }else{
    //             $back['status']="false";
    //             $back['response']="fail to update";
    //         }
    //     }else{
    //         $hasgood=M('good')->field('comment_id')->where($check)->select();
    //         $hasarray=explode(",", $hasgood[0]['comment_id']);
    //         if (in_array($comment_id,$hasarray)) {
    //             $back['status']="false";
    //             $back['response']="has opinions";
    //         }else{
    //             $newOpinions=$hasgood[0]['comment_id'].",$comment_id";
    //             $update_comment['comment_id']=$newOpinions;
    //             $where['comment_id']=$comment_id;
    //             $goodNum = M('ask_answer')->field('good')->where($where)->select();
    //             $update_num['good']=$goodNum[0]['good']+1;

    //             $model = new Model();
    //             $model->startTrans();
    //             $line1 = $model->table('good')->where($check)->save($update_comment);
    //             $line2 = $model->table('ask_answer')->where($where)->save($update_num);
    //             if ($line1 && $line2) {
    //                 $model->commit();
    //                 $back['status']="true";
    //                 $back['response']="";
    //             }else{
    //                 $model->rollback();
    //                 $back['status']="false";
    //                 $back['response']="fail to update";
    //             }
    //         }
    //     }
    //     exit(json_encode($back));
    // }

}
