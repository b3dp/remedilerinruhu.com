<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModels;
use App\Models\AddressModels;
use App\Models\UserModels;

class Dashboard extends BaseController
{
    public function _construct()
    {
        
    }

    public function index()
    {
        $db =  db_connect();
        $orderModels = new OrderModels($db);
        $userModels = new UserModels($db);
        $addressModels = new AddressModels($db);
        $data['orders'] = $orderModels->orderListPanel(['o.status !=' => '99'], ['item' => '0', 'whereStart' => '10']);
        $data['ordersCount'] = count($data['orders']);
        return view ("admin/dashboard", $data);
    }
}