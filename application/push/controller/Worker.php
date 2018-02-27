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

class Worker extends Server
{
//    protected $socket = 'tcp://0.0.0.0:2347';
    protected $socket = 'websocket://47.94.204.68:2346';

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        $connection->send('我收到你的信息了');
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        echo 'jjjj';
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {

    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {

    }
}