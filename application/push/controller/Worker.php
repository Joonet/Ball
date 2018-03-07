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
    protected $socket = 'tcp://0.0.0.0:2347';
//    protected $socket = 'websocket://0.0.0.0:2346';

    // 全局变量，保存当前进程的客户端连接数
    private $connection_count = 0;

    /**
    ws = new WebSocket("ws://47.94.204.68:2346");
    ws.onopen = function() {
    alert("连接成功");
    ws.send('tom');
    alert("给服务端发送一个字符串：tom");
    };
    ws.onmessage = function(e) {
    alert("收到服务端的消息：" + e.data);
    };
     */

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
        $connection->send('我收到你的信息了'.$data.'\n'.'当前连接数为：'.$this->connection_count);
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        // 有新的客户端连接时，连接数+1
        ++$this->connection_count;
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        // 客户端关闭时，连接数-1
        $this->connection_count--;
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