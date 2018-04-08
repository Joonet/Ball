<?php
/**
 * Created by PhpStorm.
 * User: Jo
 * Date: 27/2/2018
 * Time: 15:48
 */

//namespace app\api\controller;
namespace app\push\controller;



use think\worker\Server;
use app\admin\model\Device;
use app\admin\model\Test;
use Workerman\Lib\Timer;
use Workerman\Worker;


// 心跳间隔25秒
define('HEARTBEAT_TIME', 60);
class Worke extends Server
{

    protected $socket = 'tcp://0.0.0.0:2347';


    /**
     * 收到信息
     * @param $connection
     * @param $data
     *
     * {
    "id": "00C11350",  //全球唯一ID    unique_id
    "ssid": "GoodAP",   //当前连接wifi的名字
    "psw": "12345678",  //当前wifi的密码
    "ip": "192.168.99.118", //当前连接的局域网IP
    "mac": "5C:CF:7F:C1:13:50", //本机的MAC地址
    "rssi": -32,    //当前连接wifi的信号强度
    "batmv": 1,     //电池电压
    "levpp": 0      //液位百分比
    }
     * {"id": "00C11350","ssid": "JoP","psw": "12345678","ip": "000000","mac": "5C5445513:50","rssi": -32,"batmv": 1,"levpp": 0}
     *
     * online
     */
    public function onMessage($connection, $data)
    {
        // 给connection临时设置一个lastMessageTime属性，用来记录上次收到消息的时间
        $connection->lastMessageTime = time();

        $array = json_decode($data,true);
        if(!$array){
            $connection->send('{"code":400, "msg":"数据格式有误，请确认后继续发送"}');
            return;
        }else{
            $device=null;
            if (Device::where('mac', $array['mac'])->find()){
                $device = Device::where('mac', $array['mac'])->find();
            }else{
                $device = new Device();
            }

            try {
                $device->unique_id = $array['id'];
                $device->ssid = $array['ssid'];
                $device->psw = $array['psw'];
                $device->private_ip = $array['ip'];
                $device->mac = $array['mac'];
                $device->rssi = $array['rssi'];
                $device->batmv = $array['batmv'];
                $device->levpp = $array['levpp'];
                $device->online = 1;
                $device->public_ip = $connection->getRemoteIp();
                $device->connection_id = $connection->id;
                $device->save();
            } catch (\Exception $e) {
                $connection->send("{"code":404, "msg":$e->getMessage()}");
            }
        }

        //获取液位值，若为1，则桶满，发送邮件
        if ($array['levpp'] == 1){

            $this->send_mail();
            $connection->send('邮件已发送');

        }
        $connection->send('{"code":200, "msg":"已收到消息"}');
        //解析data数据（json格式）

    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {

    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        if ($device = Device::where('connection_id', $connection->id)->find()){
            $device->online = 0;
            $device->connection_id = -1;
            $device->save();
        }elseif ($device = Device::where('unique_id', 00000)->find()){
            $device->delete();
        }

//        echo '断开';
        $connection->send('协议连接已断开');
//        echo("<script>console.log('我收到你的信息了');</script>");

    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
//        $test = Test::where('connection', $connection->id)->find();
//        $test->delete();
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {

        $this->addTimer($worker);

        // 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
        $inner_text_worker = new Worker('text://0.0.0.0:5678');
        $inner_text_worker->onMessage = function($connection, $data)
        {

            // $data数组格式，里面有uid，表示向那个uid的页面推送数据  {"uid":13}
//            $data = json_decode($buffer, true);
//            $uid = $data['uid'];
            // 通过workerman，向uid的页面推送数据
            $ret = $this->sendMessageByUid($data, $data, $connection);
            // 返回推送结果
            $connection->send($ret ? 'ok' : 'fail');
        };
        $inner_text_worker->listen();
    }

    private function addTimer($worker){
        // 进程启动后设置一个每秒运行一次的定时器
        Timer::add(1, function()use($worker){
            $time_now = time();
            foreach($worker->connections as $connection) {
                // 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
                if (empty($connection->lastMessageTime)) {
                    $connection->lastMessageTime = $time_now;
                    continue;
                }
                // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
                if ($time_now - $connection->lastMessageTime > HEARTBEAT_TIME) {
                    $connection->close();
                }
            }
        });
    }

    // 针对uid推送数据
    function sendMessageByUid($uid, $message, $connection)
    {
        $connection->send($uid."->".count($this->worker->connections));

        foreach ($this->worker->connections as $con){
            $connection->send('id为:'.$con->id);
        }
//        $connection->send($this->worker->connections[0]->id);
        if (isset($this->worker->connections[$uid])){
            $this->worker->connections[$uid]->send($message);
            return true;
        }
        return false;
    }



    /**
     * 油箱满后邮件通知用户
     */
    private function send_mail() {

        $url = 'http://api.sendcloud.net/apiv2/mail/send';
        $API_USER = 'Jonet_test_tjXKtf';
        $API_KEY = '89AUbwztLA8aFdKN';

        $param = array(
            'apiUser' => $API_USER, # 使用api_user和api_key进行验证
            'apiKey' => $API_KEY,
            'from' => 'jo@sendcloud.org', # 发信人，用正确邮件地址替代
            'fromName' => 'PREC_Jo',
            'to' => 'jo@precintl.com;elegzh@yeah.net;sky@precintl.com;kevin@precintl.com',# 收件人地址, 用正确邮件地址替代, 多个地址用';'分隔
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

    /**
     * @param $connection
     * 向指定设备发送led灯闪亮控制命令
     */
    private function sendMessageToDevice($connection){

    }
}