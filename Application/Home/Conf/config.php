<?php
return array(
	//'配置项'=>'配置值'
	'MAIL_HOST' =>'smtp.163.com',//smtp服务器的名称
    'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
    'MAIL_USERNAME' =>'m15986090742@163.com',//你的邮箱名
    'MAIL_FROM' =>'m15986090742@163.com',//发件人地址
    'MAIL_FROMNAME'=>'职谱',//发件人姓名
    'MAIL_PASSWORD' =>'lys18826102456',//邮箱密码
    'MAIL_CHARSET' =>'utf-8',//设置邮件编码
    'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
    //'MAIL_PORT' =>'465',//smtp服务器的名称
    //'MAIL_SECURE' =>'ssl',//smtp服务器的名称
    'server_address'=>gethostbyname($_SERVER['SERVER_NAME']),
    'SUCCESS_CODE'=>000,
    'SUCCESS_WORD'=>'True',
    'NETWORK_ERROR_CODE'=>101,
    'NETWORK_ERROR_WORD'=>'网络错误，请检查您的网络状态!',
    'LOGIN_ERROR_CODE'=>102,
    'LOGIN_ERROR_WORD'=>'账号或密码错误，请重试!',
    'LOGIN_FIRST_ERROR_CODE'=>103,
    'LOGIN_FIRST_ERROR_WORD'=>'请先登录!',
    'ARTICLE_LIKE_ERROR_CODE'=>104,
    'ARTICLE_LIKE_ERROR_WORD'=>'文章收藏失败，请重试!',
    'ARTICLE_UNLIKE_ERROR_CODE'=>105,
    'ARTICLE_UNLIKE_ERROR_WORD'=>'文章取消收藏失败，请重试!',
);