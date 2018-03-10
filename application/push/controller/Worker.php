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
define('HEARTBEAT_TIME', 20);
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
     */
    public function onMessage($connection, $data)
    {
        // 给connection临时设置一个lastMessageTime属性，用来记录上次收到消息的时间
        $connection->lastMessageTime = time();
        //
        $test = Test::where('connection', $connection->id)->find();
        $test->message = $data;
        $test->save();
        $connection->send('我收到你的信息了'.$data.'\n'.'当前连接数为：'.$this->connection_count);
        //解析data数据（json格式）

        //获取液位值，若为1，则桶满，发送邮件
//        $this->sendEmail();
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {


        // 有新的客户端连接时，连接数+1
        ++$this->connection_count;
        $test = new Test();
        $test->connection = $connection->id;
        $test->name = 'Jo';
        $test->save();
        echo '连接';
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        // 客户端关闭时，连接数-1
        $this->connection_count--;
        $test = Test::where('connection', $connection->id)->find();
        $test->delete();
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
    private function sendEmail(){

    }

    /**
     * @param $connection
     * 向指定设备发送led灯闪亮控制命令
     */
    private function sendMessageToDevice($connection){

    }
}