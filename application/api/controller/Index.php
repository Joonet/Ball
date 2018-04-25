<?php
namespace app\api\controller;


use app\admin\model\Device;

class Index
{
    public function index()
    {
        if ($device = Device::where('id', 1)->find()) {
            return $device;
        }
        return 'nothing';
    }
}
