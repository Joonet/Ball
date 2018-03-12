<?php
namespace app\index\controller;
//use think\worker\Server;


class Index
{
    public function index()
    {

        $sendcloud=new \SendCloud("Jonet_test_tjXKtf", "89AUbwztLA8aFdKN",'v1');
        $mail=new \Mail();
        $mail->addBcc("lianzimi@ifaxin.com");
        $mail->addCc("bida@ifaxin.com");
        $mail->setFrom("test@test.com");
        $mail->addTo("jo@precintl.com;elegzh@yeah.net");
        $mail->setReplyTo("reply@test.com");
        $mail->setFromName("来自测试发送");
        $mail->setContent("这是一封测试邮件,请勿回复");
        $mail->setSubject("测试");
        $mail->setRespEmailId(true);
        //添加多个邮件头
//        $mail->addHeader("header1", "header2");
//        $mail->addHeader("header2", "header2");
        $sendcloud->sendCommon($mail);
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
            'html' => '<tbody>
                         <tr>
                             <td width="100%" height="70px" valign="middle">
                                 <img style="margin-left:50px;" src="http://7xi9bi.com1.z0.glb.clouddn.com/35069/2015/07/20/1686ccdd7919429a8beeb4f3f15d5eb1.png" alt="logo">
                             </td>
                         </tr>
                         <tr>
                             <td align="center" valign="top">
                                 <table width="465px" border="0" cellpadding="0" cellspacing="0" style="background:#fff;height:411px;">
                                     <tbody><tr>
                                         <td valign="top" align="center" style="color:#666;line-height:1.5">
                                             <div style="width:360px;text-align:left;margin-top:50px;margin-bottom:80px;">
                                                 <p>亲爱的用户您好：</p>
                                                 <p style="text-indent:2em">
                                                     欢迎您使用xxx平台，我们将为您提供优质的服务。在使用的过程中如有任何疑问或者建议请联系我们。
                                                 </p>
                                             </div>
                                             <div style="border-top:1px dashed #ccc;margin:20px"></div>
                                         </td>
                                     </tr>
                                   
                                 </tbody></table>
                             </td>
                         </tr>
                        </tbody>',
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
