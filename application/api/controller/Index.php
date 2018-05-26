<?php
namespace app\api\controller;


use app\admin\model\Device;

class Index
{
    public function index($id = 1)
    {
        if ($device = Device::where('id', $id)->find()) {
            return $device;
        }
        return 'no device';
    }
}
