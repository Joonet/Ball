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


// 心跳间隔25秒
define('HEARTBEAT_TIME', 120);
class Worker extends Server
{

    protected $socket = 'tcp://0.0.0.0:2347';
//    protected $socket = 'websocket://0.0.0.0:2346';

    // 全局变量，保存当前进程的客户端连接数
    private $connection_count = 0;


    /**
     *
     *
     * 错误
     * failed: Error in connection establishment: net::ERR_CONNECTION_TIMED_OUT
     * Uncaught SyntaxError: Unexpected token
     */






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
     *
     * online
     */
    public function onMessage($connection, $data)
    {
        // 给connection临时设置一个lastMessageTime属性，用来记录上次收到消息的时间
        $connection->lastMessageTime = time();
//        $arr = '{"id": "00C11350","ssid": "GoodAP","psw": "12345678","ip": "127.0.0.1","mac": "5C:CF:7F:C1:13:50","rssi": -32,"batmv": 1,"levpp": 0}';
        $array = json_decode($data,true);
        if(!$array){
            $connection->send('wrong msg format');
            $connection->close();
        }
//        foreach ($array as $item=>$value){
//            $connection->send($item.">".$value);
//        }
        //
        $device = Device::where('ip', $connection->getRemoteIp())->find();
        $device->unique_id = $array['id'];
        $device->ssid = $array['ssid'];
        $device->psw = $array['psw'];
        $device->ip = $array['ip'];
        $device->mac = $array['mac'];
        $device->rssi = $array['rssi'];
        $device->batmv = $array['batmv'];
        $device->levpp = $array['levpp'];
        $device->online = 1;
        $device->save();

        //获取液位值，若为1，则桶满，发送邮件
        if ($array['levpp'] == 1){

            $this->send_mail();
            $connection->send('邮件已发送');

        }
        $connection->send('我收到你的信息了'.$data.'\n'.'当前连接数为：'.$this->connection_count);
        //解析data数据（json格式）




    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {


        // 有新的客户端连接时，连接数+1
        ++$this->connection_count;

        if ($device = Device::where('ip', $connection->getRemoteIp())->find()){
            $device->online = 1;
            $device->save();
        }else{
            $device = new Device();
            $device->ip = $connection->getRemoteIp();
            $device->save();
        }



    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        // 客户端关闭时，连接数-1
        $this->connection_count--;
        $device = Device::where('ip', $connection->getRemoteIp())->find();
        $device->online = 0;
        $device->save();
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

    /**
     * @param $connection
     * 向指定设备发送led灯闪亮控制命令
     */
    private function sendMessageToDevice($connection){

    }
}