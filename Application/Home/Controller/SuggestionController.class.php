<?php
namespace Home\Controller;

use Think\Controller;

//use Home\Common\CommonController;
/**
 * 意见反馈
 */
class SuggestionController extends Controller
{

    public function postSuggestion($account)
    {
        try {
            $json       = file_get_contents('php://input');
            $sourceData = json_decode($json, true);

            $mail = $sourceData['email'];
            if (empty($mail) || !preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $mail)) {
                $this->feedback(C('SUGGESTION_EMAIL_FORMAT_ERROR_CODE'), "the format of email is wrong", null);
            } else {
                $data = array(
                    'user'    => $account,
                    'u_email' => $sourceData['email'],
                    'content' => $sourceData['content'],
                    'date'    => date('Y-m-d'),
                    'reply'   => "",
                );
                $result = M('suggestion')->add($data);

                if ($result) {
                    $title = "【职谱】收到您的反馈";
                    $tip   = "我们已经收到您的建议啦，我们会尽快改正，很感谢您支持职谱。祝生活愉快~";
                    $mail  = new MailController();
                    if ($mail->sendMail($mail, $title, $tip)) {
                        $this->feedback(C('SUCCESS_CODE'), '', null);
                    } else {
                        $this->feedback(C('SUGGESTION_EMAIL_ERROR_CODE'), "send email failed", null);
                    }
                } else {
                    $this->feedback(C('SUGGESTION_EMAIL_ERROR_CODE'), "mysql failed", null);
                }
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

}
