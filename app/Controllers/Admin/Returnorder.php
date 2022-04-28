<?php


namespace App\Controllers\Admin;

use App\Libraries\Iyzico;
use App\Controllers\BaseController;
use App\Models\UserModels;
use App\Models\OrderModels;
use App\Models\AddressModels;
use App\Models\ProductDetailModels;
use App\Models\Category;


class Returnorder extends BaseController
{ 
    public function _construct()
    {
        
    } 

    public function list()
    {
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
        $data['ordersReturn'] = $orderModels->orderReturnAll();
        $data['ordersReturnCount'] = count($data['ordersReturn']);
        $data['userModels'] = $userModels;
        return view ("admin/return/return-list", $data);
    } 

    public function add()
    {
        return view ("admin/return/return-add");
    }

    public function detail($id)
    {
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$category = new Category($db);
        $ordersReturn = $orderModels->orderReturnOne(['id' => $id]);
        $data['ordersReturn'] = $ordersReturn;
        $data['orderModels'] = $orderModels;
        $data['order'] = $orderModels->c_one(['id' => $ordersReturn->order_id]);
        $data['user'] = $userModels->c_one(['id' => $ordersReturn->user_id]);
        $data['orderReturnDetail'] = $orderModels->orderReturnDetailAll(['request_no' => $ordersReturn->request_no, 'user_id' => $ordersReturn->user_id]);
        $data['productDetailModels'] = $productDetailModels;
        $data['category'] = $category;
        return view ("admin/return/return-edit", $data);
    }

    public function returnOrderEdit() 
    { 
        $db =  db_connect();
        $iyzico = new Iyzico();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$category = new Category($db);
        $id = $this->request->getPost('id');
        $admin_description = $this->request->getPost('admin_description');
        $status = $this->request->getPost('status');
        $selectedProduct = $this->request->getPost('selectedProduct');
        $select_piece = $this->request->getPost('select_piece');

        $ordersReturn = $orderModels->orderReturnOne(['id' => $id]);
        $order_id = $ordersReturn->order_id;
        $order = $orderModels->c_one(['id' => $order_id, 'status !=' => '99']);
        
        $user = $userModels->c_one(['id' => $ordersReturn->user_id]);

        if (!$ordersReturn && !$order) {
            $data['error'] = 'Düzenlemek istediğiniz iade talebi bulunamadı.';
        }elseif (!$status) {
            $data['error'] = 'Lütfen düzenlemek istediğiniz durumu seçiniz.';
        }else{
            if ($status != '4' && $status != '5') {
                $updateRequestData = [
                    'status' => $status,
                    'admin_description' => $admin_description,
                    'updated_at' => created_at()
                ];
                $requestUpdate = $orderModels->orderReturnUpdate($id, $updateRequestData);
            }

            if ($requestUpdate || $status == '4' || $status == '5') {
                $data['success'] = 'İade isteği başarılı bir şekilde düzenlendi.';
                if ($status == '4') {
                    foreach ($selectedProduct as $row) {
                        $selectCount = $select_piece[$row];
                        $orderReturnDetail = $orderModels->orderReturnDetailOne(['request_no' => $ordersReturn->request_no, 'user_id' => $ordersReturn->user_id, 'id' => $row]);
                        if ($orderReturnDetail) {
                            $orderNebimFind = $orderModels->orderDetailNebimOne(['id' => $orderReturnDetail->order_detail_id, 'order_id' => $order_id], '( status = "6")');
                            $orderBoolFind = $orderModels->orderDetailNebimSingle(['variant_id' => $orderNebimFind->variant_id, 'order_id' => $order_id, 'status' => $orderNebimFind->status], $selectCount);
                            foreach ($orderBoolFind as $item) {
                                $updateOrderDetailNebimData = [
                                    'status' => '9',
                                    'return_count' => 1,
                                    'created_at' => created_at(),
                                ];
                                $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['id' => $item->id], $updateOrderDetailNebimData);
                                /*
                                    $url = base_url().'/api/nebimOrderDetailCanceled/'.$order_id.'/'. $item->id .'/1?apiKey=953E1C7C06494141B8DF4BBBDE76ED5E';
                                    $curl = curl_init();
                                    curl_setopt($curl, CURLOPT_URL, $url);
                                    curl_setopt ($curl, CURLOPT_GET, TRUE);
                            
                                    curl_setopt($curl, CURLOPT_USERAGENT, 'api');
                            
                                    curl_setopt($curl, CURLOPT_TIMEOUT_MS, 155);
                                    curl_setopt($curl, CURLOPT_HEADER, 0);
                                    curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
                                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
                                    curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10);
                            
                                    curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
                            
                                    curl_exec($curl);
                            
                                    curl_close($curl);
                                */
                            }
                            $updateOrderDetailData = [
                                'status' => '9',
                                'return_count' => $selectCount,
                                'created_at' => created_at(),
                            ];
                            $updateReturnDetail = $orderModels->orderReturnDetailWhereUpdate(['id' => $row, 'order_id' => $order_id], $updateOrderDetailData);
                        }
                    }
                    $orderDetailFind = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', '');
                    $orderDetailCheack = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', ' status NOT IN ("5", "9") ');
                    if ($orderDetailFind) {
                        foreach ($orderDetailFind as $row) {
                            $singlePrice = $row->price;
                            $cancellationPrice =  $singlePrice * $row->cancellation_count;
                            $returnPrice =  $singlePrice * $row->return_count;
                            
                            $overall_total += vat_deducted((($row->price * $row->piece)  - $cancellationPrice - $returnPrice), $row->vat_rate);
                            $vat_price += (($row->price * $row->piece) - $cancellationPrice) - vat_deducted((($row->price * $row->piece) - $cancellationPrice - $returnPrice), $row->vat_rate);
                            $basketTotalPrice += ($row->price * $row->piece) - $cancellationPrice - $returnPrice; 
                        }
                        $updateOrderData = [
                            "overall_total" => $overall_total,
                            "vat_price" => $vat_price,
                            "total_price" => $basketTotalPrice,
                            "updated_at" => created_at(),
                        ];
                        $insertOrder = $orderModels->edit($order_id, $updateOrderData);
                    }
                    if (!$orderDetailCheack) {
                        $updateOrderData = [
                            "status" => '9',
                            "overall_total" => '0',
                            "vat_price" => '0',
                            "total_price" => '0',
                            "updated_at" => created_at(),
                        ];
                        $insertOrder = $orderModels->edit($orderFind->id, $updateOrderData);
                    }
                }elseif ($status == '5') {
                    foreach ($selectedProduct as $row) {
                        $selectCount = $select_piece[$row];
                        $orderReturnDetail = $orderModels->orderReturnDetailOne(['request_no' => $ordersReturn->request_no, 'user_id' => $ordersReturn->user_id, 'id' => $row]);
                        if ($orderReturnDetail) {
                            $orderNebimFind = $orderModels->orderDetailNebimOne(['id' => $orderReturnDetail->order_detail_id, 'order_id' => $order_id]);
                            $orderBoolFind = $orderModels->orderDetailNebimSingle(['variant_id' => $orderNebimFind->variant_id, 'order_id' => $order_id, 'status' => '6'], $selectCount);
                            foreach ($orderBoolFind as $item) {
                                $updateOrderDetailNebimData = [
                                    'status' => '7',
                                    'return_request_cancel' => 1,
                                    'updated_at' => created_at(),
                                ];
                                $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['id' => $item->id], $updateOrderDetailNebimData);
                            }
                            $total_request_cancel = $orderReturnDetail->return_request_cancel + $selectCount;
                            $updateOrderDetailData = [
                                'status' => '9',
                                'return_request_cancel' => $total_request_cancel,
                                'created_at' => created_at(),
                            ];
                            $updateReturnDetail = $orderModels->orderReturnDetailWhereUpdate(['id' => $row, 'order_id' => $order_id], $updateOrderDetailData);
                        }
                    }

                }
                $requestEnd = TRUE;
                $orderReturnDetailList = $orderModels->orderReturnDetailAll(['request_no' => $ordersReturn->request_no, 'user_id' => $ordersReturn->user_id]);
                foreach ($orderReturnDetailList as $row) {
                    if (($row->return_request_count - $row->return_count - $row->return_request_cancel) > '0') {
                        $requestEnd = FALSE;
                    }
                }
                if ($requestEnd) {
                    $updateRequestData = [
                        'status' => '6',
                        'admin_description' => $admin_description,
                        'updated_at' => created_at()
                    ];
                    $requestUpdate = $orderModels->orderReturnUpdate($id, $updateRequestData);
                }
            }else{
                $data['error'] = 'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
    }
    
}