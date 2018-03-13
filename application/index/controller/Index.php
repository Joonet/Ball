<?php
namespace app\index\controller;
//use think\worker\Server;


class Index
{
    public function index()
    {

        $data = '{"id": "00C11350","ssid": "GoodAP","psw": "12345678","ip": "192.168.99.118","mac": "5C:CF:7F:C1:13:50","rssi": -32,"batmv": 1,"levpp": 0}';
        $array = json_decode('js',true);
        var_dump($array);
        if ($array){
            echo '1';
        }else{
            echo '2';
        }
    }

    public function test(){
//        $sendcloud=new \SendCloud("Jonet_test_tjXKtf", "89AUbwztLA8aFdKN",'v2');
//        $mail=new \Mail();
//        $mail->addBcc("lianzimi@ifaxin.com");
//        $mail->addCc("bida@ifaxin.com");
//        $mail->setFrom("test@test.com");
//        $mail->addTo("jo@precintl.com;elegzh@yeah.net");
//        $mail->setFromName("来自测试发送");
//        $mail->setSubject("测试");
//        $mail->setContent("这是一封测试邮件,请勿回复");
//        $mail->setRespEmailId(true);
//        $mail->setLabel(14411);
//        //添加多个邮件头
//        $mail->addHeader("header1", "header1");
//        $mail->addHeader("header2", "header2");
//        $sendcloud->sendCommon($mail);
    }

    public function send_mail() {
        $url = 'http://api.sendcloud.net/apiv2/mail/send';
        $API_USER = 'Jonet_test_tjXKtf';
        $API_KEY = '89AUbwztLA8aFdKN';

        $param = array(
            'apiUser' => $API_USER, # 使用api_user和api_key进行验证
            'apiKey' => $API_KEY,
            'from' => 'jo@sendcloud.org', # 发信人，用正确邮件地址替代
            'fromName' => 'PREC_Jo',
            'to' => 'jo@precintl.com;elegzh@yeah.net;sky@precintl.com',# 收件人地址, 用正确邮件地址替代, 多个地址用';'分隔
            'subject' => '油桶溢满警告_测试',
            'html' => '<html>
<head>
<base target="_blank">
<style type="text/css">
::-webkit-scrollbar{ display: none; }
</style>
<style id="cloudAttachStyle" type="text/css">
#divNeteaseBigAttach, #divNeteaseBigAttach_bak{display:none;}
</style>

</head>
<body tabindex="0" role="listitem">




<table border="0" cellspacing="0" cellpadding="0" align="center" style="width:100%; height:100%;background:#f0f0f0;"><tbody><tr><td align="center" style="width:100%; height:100%;background:#f0f0f0;"><table border="0" cellspacing="0" cellpadding="0" width="600" style=" margin:0 auto; font-family:微软雅黑;"><tbody><tr height="56" style="background:#f0f0f0;"></tr><tr><td height="133" style=" position:relative; background:#fff; "><img src="https://static.wixstatic.com/media/1e8f9b_dc4fe56404fa4769a625ae74e718854f~mv2.png/v1/fill/w_266,h_52,al_c,lg_1/1e8f9b_dc4fe56404fa4769a625ae74e718854f~mv2.png"></td></tr><tr><td width="600" height="100" style="background:#fff;"></td></tr><tr><td width="600" height="30" style="background:#fff;"><table border="0" cellspacing="0" cellpadding="0"><tbody><tr><td width="50"></td><td nt-type="edit" nt-val="mutext" width="500" style="font-family:微软雅黑; font-size:24px; color:#0a1420; font-weight:500; line-height:30px;">亲爱的开发者，您好！</td></tr></tbody></table></td></tr><tr><td width="600" height="85" style="background:#fff;"></td></tr><tr><td width="600" height="65" style="background:#fff;"><table border="0" cellspacing="0" cellpadding="0"><tbody><tr><td width="50"></td><td nt-type="edit" nt-val="mutext" width="500" style="font-family:微软雅黑; font-size:16px; color:#353535; line-height:30px;text-indent: 2em;">您的编号为#xxx的油桶已满，请及时处理。 </td></tr></tbody></table></td></tr><tr><td width="600" height="20" style="background:#fff;"></td></tr><tr><td width="600" height="65" style="background:#fff;"><table border="0" cellspacing="0" cellpadding="0"><tbody><tr><td width="50"></td><td width="500" nt-type="edit" nt-val="text" style="font-family:微软雅黑; font-size:20px; color:#0a1420; font-weight:500; line-height:30px; text-align:right;">深圳市万引力工程技术有限公司</td></tr></tbody></table></td></tr><tr><td width="600" height="226" style="background:#fff;"><table border="0" cellspacing="0" cellpadding="0"><tbody><tr height="34"></tr><tr><td width="600" align="center"><img nt-type="edit" nt-val="image" src="http://mapopen-bms.cdn.bcebos.com/images/userEmail/bms-emil-erweima.jpg"></td></tr><tr height="15"></tr><tr><td nt-type="edit" nt-val="text" width="600" height="15" style="background:#fff;color: #091420; font-size:12px;line-height:15px; font-family:微软雅黑; font-weight:bold; text-align:center;">了解如何快速通过认证•获取产品更新信息•技术沙龙报名•手机自助查询配额</td></tr><tr><td nt-type="edit" nt-val="text" width="600" height="25" style="background:#fff;color: #091420; font-size:12px;line-height:25px; font-family:微软雅黑; text-align:center;">请关注百度地图开放平台微信公众号</td></tr><tr height="20"></tr></tbody></table></td></tr><tr><td width="600" height="50" style="background:#fff;"><img style="width: 600px;" src="http://mapopen-bms.cdn.bcebos.com/images/userEmail/bms-emil-bot-bg.jpg"></td></tr><tr height="50" style="background:#f0f0f0;"><td width="600" height="50"></td></tr></tbody></table></td></tr></tbody></table> 

<style type="text/css">
body{font-size:14px;font-family:arial,verdana,sans-serif;line-height:1.666;padding:0;margin:0;overflow:auto;white-space:normal;word-wrap:break-word;min-height:100px}
td, input, button, select, body{font-family:Helvetica, \'Microsoft Yahei\', verdana}
pre {white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word;width:95%}
th,td{font-family:arial,verdana,sans-serif;line-height:1.666}
img{ border:0}
header,footer,section,aside,article,nav,hgroup,figure,figcaption{display:block}
blockquote{margin-right:0px}
</style>

<style id="ntes_link_color" type="text/css">a,td a{color:#064977}</style>

</body></html>',
            'respEmailId' => 'true'
        );


        $data = http_build_query($param);

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data
            ));
        $context  = stream_context_create($options);
        $result = file_get_contents($url, FILE_TEXT, $context);

        return $result;
    }
}
