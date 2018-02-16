<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 干货类
 */
class ArticleController extends Controller
{

    public function getArticle($a_id = "", $account = "")
    {
        //$json='{"article_id":"2"}';
        //$json=file_get_contents('php://input');
        //$sourceData=json_decode($json,true);
        try {
            $where['article_id'] = $a_id;
            $data                = M('article')->where($where)->find();
            $data['comments']    = array();

            //修改阅读量
            $update['readingquantity'] = $data['readingquantity'] + 1;
            $newData                   = M('article')->where($where)->save($update);

            //内容...字符串封装
            $data['content'] = htmlspecialchars($data['content']);

            // echo 
            // $data['image'] = explode("/jobBook/", $data['image'])[0]

            //检查是否收藏
            $check['user'] = $account;
            $myLike        = M('likearticle')->where($check)->getField('article_id', true);
            if (in_array($data['article_id'], $myLike)) {
                $data['ifLike'] = 1;
            } else {
                $data['ifLike'] = 0;
            }
            $this->feedback(C('SUCCESS_CODE'), '', $data);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
        //var_dump($data);      
    }

    public function test()
    {
        try {
            M('like1article')->add($data);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        // $back['data']='';
        // $back['message']=C('LOGIN_FIRST_ERROR_WORD');
        // $back['code']=C('LOGIN_FIRST_ERROR_CODE');
        // exit(json_encode($back));
    }

    public function allArticle($type = 0, $index = 0)
    {
        // $index=file_get_contents('php://input');
        //$index=4;
        try {
            if ($type == 0) {
                $articles = M('article')->limit($index, 10)->order('date desc')->select();
            } else {
                $where['type'] = $type;
                $articles      = M('article')->limit($index, 10)->where($where)->order('date desc')->select();
            }

            for ($i = 0; $i < count($articles); $i++) {
                // $end = explode("/jobBook/", $articles[$i]['image'])[1];
                $ip = "http://".C('server_address').'/';
                $articles[$i]['image'] = $ip.$articles[$i]['image'];
                // echo $articles[$i]['image'];
                $articles[$i]['content'] = htmlspecialchars($articles[$i]['content']);
                // $articles[$i]['content']=($articles[$i]['content']);
                $articles[$i]['comments'] = array();
            }
            // $this->feedback(C('PERSON_AUTO_LOGIN_ERROR_CODE'), '', '');
            $this->feedback(C('SUCCESS_CODE'), '', $articles);
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }

        // $check['user']=$account;
        // $myLike = M('likearticle')->where($check)->getField('article_id',true);

        // $articles = $this->ifLike($articles,$myLike);
        // var_dump($articles);
        
        // var_dump(json_encode($back));
    }

    /*检查每一个文章是否有收藏*/
    public function ifLike($allArticle, $myLike)
    {
        for ($i = 0; $i < count($allArticle); $i++) {
            if (in_array($allArticle[$i]['article_id'], $myLike)) {
                $allArticle[$i]['ifLike'] = 1;
            } else {
                $allArticle[$i]['ifLike'] = 0;
            }
        }
        return $allArticle;
    }

    public function likesArticle($account = "", $a_id)
    {
        // $json=file_get_contents('php://input');
        // $sourceData=json_decode($json,true);

        // $article_id=$sourceData['article_id'];
        // $user=$sourceData['account'];
        try {
            if ($account == "") {
                $this->feedback(C('LOGIN_FIRST_ERROR_CODE'), "account is null!", null);
            }
            $data = array(
                'user'       => $account,
                'article_id' => $a_id,
            );
            $result = M('likearticle')->add($data);
            if ($result != 0) {
                $this->feedback(C('SUCCESS_CODE'), '', null);
            } else {
                $this->feedback(C('ARTICLE_LIKE_ERROR_CODE'), $result, null);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }
    }

    public function unlikesArticle($account = "", $a_id)
    {
        try {
            if ($account == "") {
                $this->feedback(C('LOGIN_FIRST_ERROR_CODE'), "account is null!", null);
            }
            $data = array(
                'user'       => $account,
                'article_id' => $a_id,
            );
            $result = M('likearticle')->where($data)->delete();
            if ($result != 0) {
                $this->feedback(C('SUCCESS_CODE'), '', null);
            } else {
                $this->feedback(C('ARTICLE_UNLIKE_ERROR_CODE'), $result, null);
            }
        } catch (\Exception $e) {
            $this->feedback(C('NETWORK_ERROR_CODE'), $e->getMessage(), null);
        }

    }

    /*添加文章评论*/
    // public function addArticleComment(){
    //     $json=file_get_contents('php://input');
    //     $sourceData=json_decode($json,true);

    //     $ask_time=date('Y-m-d G:i:s');
    //     $data=array(
    //         'a_id'=>$sourceData['a_id'],
    //         'content'=>$sourceData['content'],
    //         'ask_time'=>$ask_time,
    //         'commenter'=>$sourceData['commenter']['account'],
    //     );
    //     $result = M('commentArticle')->add($data);
    //     if ($result) {
    //         $this->feedback("false","unlike failed");
    //     }else{
    //         $this->feedback("true","");
    //     }
    // }

    //数据反馈（公共方法）
    public function feedback($code, $message, $data)
    {
        $back['code']    = $code;
        $back['message'] = $message;
        $back['data']    = $data;
        exit(json_encode($back));
    }
}
