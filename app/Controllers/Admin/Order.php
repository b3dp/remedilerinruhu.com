<?php


namespace App\Controllers\Admin;

use App\Libraries\Iyzico;
use App\Controllers\BaseController;
use App\Controllers\SendMail;
use App\Models\UserModels;
use App\Models\OrderModels;
use App\Models\AddressModels;
use App\Models\ProductDetailModels;
use App\Models\Category;
use App\Models\SettingModels;

class Order extends BaseController
{
    public function _construct()
    {
        
    }

    public function all() 
    { 
        $data['sidebarActive'] = 'order';
        $data['sidebarAltActive'] = 'order/all';
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
        if (session()->get('admin')['role'] == '5') {
            $data['orders'] = $orderModels->orderListPanel(['o.status !=' => '99']);
        }else{
            $data['orders'] = $orderModels->orderListPanel(['o.status !=' => '99', 'odn.store_id' => session()->get('admin')['store_id'] ]);
        }
        $data['ordersCount'] = count($data['orders']);
        return view ("admin/order/all-list", $data);
    }

    public function waiting() 
    { 
        $data['sidebarActive'] = 'order';
        $data['sidebarAltActive'] = 'order/waiting';
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
        if (session()->get('admin')['role'] == '5') {
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'status' => '1']);
        }else{
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'status' => '1', 'odn.store_id' => session()->get('admin')['store_id']]);
        }
        $data['ordersCount'] = count($data['orders']);
        return view ("admin/order/waiting-list", $data);
    }

    public function prepared() 
    { 
        $data['sidebarActive'] = 'order';
        $data['sidebarAltActive'] = 'order/prepared';
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
        if (session()->get('admin')['role'] == '5') {
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'status' => '2']);
        }else{
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'status' => '2', 'odn.store_id' => session()->get('admin')['store_id']]);
        }
        
        $data['ordersCount'] = count($data['orders']);
        return view ("admin/order/prepared-list", $data);
    }

    public function shipped() 
    { 
        $data['sidebarActive'] = 'order';
        $data['sidebarAltActive'] = 'order/shipped';
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
        if (session()->get('admin')['role'] == '5') {
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'status' => '3']);
        }else{
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'status' => '3', 'odn.store_id' => session()->get('admin')['store_id']]);
        }
        $data['ordersCount'] = count($data['orders']);
        return view ("admin/order/shipped-list", $data);
    }

    public function today() 
    { 
        $data['sidebarActive'] = 'order';
        $data['sidebarAltActive'] = 'order/today';
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
        if (session()->get('admin')['role'] == '5') {
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'DATE(buy_at)' => nowDate()]);
        }else{
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'DATE(buy_at)' => nowDate(), 'odn.store_id' => session()->get('admin')['store_id']]);
        }
        
        $data['ordersCount'] = count($data['orders']);
        return view ("admin/order/today-list", $data);
    }

    public function delivered() 
    { 
        $data['sidebarActive'] = 'order';
        $data['sidebarAltActive'] = 'order/delivered';
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
        if (session()->get('admin')['role'] == '5') {
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'status' => '4']);
        }else{
            $data['orders'] = $orderModels->orderList(['status !=' => '99', 'status' => '4', 'odn.store_id' => session()->get('admin')['store_id'] ]);
        }
        $data['ordersCount'] = count($data['orders']);
        return view ("admin/order/delivered-list", $data);
    }
    
    public function detail($order_id) 
    { 
        $data['sidebarActive'] = 'order';
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$category = new Category($db);
        $data['productDetailModels'] = $productDetailModels;
        $data['category'] = $category;
        $data['order'] = $orderModels->c_one(['id' => $order_id, 'status !=' => '99']);
        if (session()->get('admin')['role'] == '5') {
            $data['orderDetail'] = $orderModels->orderDetailNebimAll(['order_id' => $order_id]);
        }else{
            $data['orderDetail'] = $orderModels->orderDetailNebimAll(['order_id' => $order_id, 'store_id' => session()->get('admin')['store_id']]);
        }
        $data['delivery_address'] = $addressModels->c_one('order_address_clone', ['id' => $data['order']->shipping_address]);

        $data['delivery_town'] = $addressModels->c_one('town', ['TownID' => $data['delivery_address']->user_town], 'TownName ASC');
        $data['delivery_city']= $addressModels->c_one('city', ['CityID' => $data['delivery_address']->user_city], 'CityName ASC');
        $data['delivery_neighborhood'] = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $data['delivery_address']->user_neighborhood]);

        $data['billing_address'] = $addressModels->c_one('order_address_clone', ['id' => $data['order']->billing_address]);

        $data['billing_town'] = $addressModels->c_one('town', ['TownID' => $data['billing_address']->user_town], 'TownName ASC');
        $data['billing_city']= $addressModels->c_one('city', ['CityID' => $data['billing_address']->user_city], 'CityName ASC');
        $data['billing_neighborhood'] = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $data['billing_address']->user_neighborhood]);

        $data['user'] = $userModels->c_one(['users.id' => $data['order']->user_id]);
        if (!$data['order']) {
            return redirect()->route('order/all');
        }
        return view ("admin/order/order-detail", $data);
    }

    public function panelOrderEdit() 
    { 
        $data['sidebarActive'] = 'order';
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$category = new Category($db);
        $settingModels = new SettingModels($db);
        $SendMail = new SendMail($db);
        $iyzico = new Iyzico();

        $order_id = $this->request->getPost('order_id');
        $status = $this->request->getPost('status');
        $invonceNo = $this->request->getPost('invonceNo');
        $cargoCount = $this->request->getPost('cargoCount');
        $orederCanceledNote = $this->request->getPost('orederCanceledNote');
        
        $order = $orderModels->c_one(['id' => $order_id, 'status !=' => '99']);
        $orderDetail = $orderModels->orderDetailNebimOne(['order_id' => $order_id, 'store_id' => session()->get('admin')['store_id'] ]);
        $userCheackOrder = $userModels->c_one(['id' => $order->user_id, 'is_active' => '1']);
        $delivery_address = $addressModels->c_one('order_address_clone', ['id' => $order->shipping_address]);

        $town = $addressModels->c_one('town', ['TownID' => $delivery_address->user_town], 'TownName ASC');
        $city = $addressModels->c_one('city', ['CityID' => $delivery_address->user_city], 'CityName ASC');
        $neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $delivery_address->user_neighborhood]);

        $billing_address = $addressModels->c_one('order_address_clone', ['id' => $order->billing_address]);

        $townBilling = $addressModels->c_one('town', ['TownID' => $billing_address->user_town], 'TownName ASC');
        $cityBilling = $addressModels->c_one('city', ['CityID' => $billing_address->user_city], 'CityName ASC');
        $neighborhoodBilling = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $billing_address->user_neighborhood]);

        $data['user'] = $userModels->c_one(['id' => $order->user_id]);
        if (!$order) {
            $data['error'] = 'Düzenlemek istediğiniz sipariş bulunamadı.';
        }elseif (!$status) {
            $data['error'] = 'Lütfen siparişin durum bilgisini seçiniz.';
        }elseif ($status == '3' && (!$invonceNo  && !$orderDetail->invonce_no)) {
            $data['error'] = 'Lütfen siparişiniz fatura numarasını giriniz ve kargo fişini yazdırınız.';
        }elseif ($status == '3' && (!$cargoCount && !$orderDetail->invonce_no)) {
            $data['error'] = 'Lütfen kargonun kaç parçadan oluştugunuzu giriniz ve kargo fişini yazdırınız.';
        }else{
            if ($status == '2') {
                $updateOrderDetailData = [
                    'status' => $status,
                    'updated_at' => created_at()
                ];
                if (session()->get('admin')['role'] == '5') {
                    $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['order_id' => $order_id, 'status !=' => '5', 'status !=' => '6', 'status !=' => '9'], $updateOrderDetailData);
                }else{
                    $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['order_id' => $order_id, 'store_id' => session()->get('admin')['store_id'], 'status !=' => '5', 'status !=' => '6', 'status !=' => '9'], $updateOrderDetailData);
                }

                if (session()->get('admin')['role'] == '5') {
                    $orderDetailFind = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', ' status NOT IN ("5", "9") AND return_count IS NULL AND cancellation_count IS NULL ');
                }else{
                    $orderDetailFind = $orderModels->orderDetailNebimAll(['order_id' => $order_id, 'store_id' => session()->get('admin')['store_id']], '', ' status NOT IN ("5", "9") AND return_count IS NULL AND cancellation_count IS NULL ');
                }

                $contactSetting = $settingModels->c_all(['type' => 'contact']);
                $contact = namedSettings($contactSetting);

                $orderDetailCheack = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', ' status NOT IN ("2","5","9") ');

                if (!$orderDetailCheack) {
                    $updateOrderData = [
                        "status" => '2',
                        "updated_at" => created_at(),
                    ];
                    $insertOrder = $orderModels->edit($order_id, $updateOrderData);
                }

                foreach ($orderDetailFind as $veriable) {

                    $singlePrice = $veriable->price;
                    $cancellationPrice =  $singlePrice * $veriable->piece;

                    $returnJson = $iyzico->cancelRequest($veriable->iyziko_payment_transaction_id, ($cancellationPrice) );

                    $row = $productDetailModels->c_one(['p.id' => $veriable->product_id, 'pa.id' => $veriable->variant_id ]);
                    if ($row) {
                        $product_id = $veriable->product_id;
                        $variant_barcode = $veriable->variant_id;
                    }else{
                        $product_id = $veriable->product_id;
                    }
    
                    if ($variant_barcode) {
                        $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.is_active' => '1', 'pa.id' => $veriable->variant_id]);
                    }else{
                        $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.is_active' => '1']);
                    }
    
                    $productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' => $productCombinationOne->id], '1');
                    if (!$productPicture) {
                        $productPicture = $productDetailModels->c_all_image(['product_id' => $row->id], '1');
                    }
                    $featureArray = explode(' - ', $row->attr);
                    $attrubuteArray = explode(' - ', $productCombinationOne->attr_color);
                    $attrubuteIDArray = explode(' - ', $productCombinationOne->attr_color_id);
                    $categoryArray = explode(',', $row->category_id);
                    $color_id = $attrubuteIDArray['0'];
                    $color_title = $attrubuteArray['0'];
                    $attrubuteSizeArray = explode(' - ', $productCombinationOne->attr_size);
                    $attrubuteSizeIDArray = explode(' - ', $productCombinationOne->attr_size_id);
                    $size_id = $attrubuteSizeIDArray['0'];
                    $size_title = $attrubuteSizeArray['0'];
                    if ($size_title) {
                        $sizeView = '<span class="order-list__item-title"style="color: #555; font-size: 14px; line-height: 1.4"><b>Boyut</b> : '. $size_title .'</span>';
                    }
                    if ($color_title) {
                        $colorView = '<span class="order-list__item-title"style="color: #555; font-size: 14px; line-height: 1.4;"><b>Renk</b> : '. $color_title .'</span>';
                    }
                    foreach ($categoryArray as $item) {
                        $catFind = '';
                        if ($item) {
                            $catFind = $category->c_all_list('', $item);
                            if(!$catFind){
                                $endCatID = $item;
                            }
                        }
                    }
                    $data['arr']['topCategoryFind'] = array_reverse($category->c_top_all_list('', $endCatID));
                    $endCategoryFind = $category->c_one(["id" =>$endCatID]);
    
                    $brand = $row->b_title;
                    $colorTitle = $color_title;
                    $sizeTitle = $size_title;
                    
                    $singlePrice = $veriable->price * $veriable->piece;
    
                    if ($veriable->vat_rate) {
                        $vatRate[$veriable->vat_rate] = $vatRate[$veriable->vat_rate] + vat_add(($veriable->price), $veriable->vat_rate);
                        $totalVatRate = $totalVatRate + vat_add(($veriable->price), $veriable->vat_rate);
                    }
    
                    if ($productPicture) {
                        foreach ($productPicture as $item) {
                            if (file_exists('uploads/products/min/'.$item->image.'') && $item->image){
                                $image = base_url('/uploads/products/min/'.$item->image);
                            }else{
                                $image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                            }
                        }
                    }else{
                        $image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                    }
    
    
                    if ($productCombinationOne->title) {
                        $title = $productCombinationOne->title;
                    }else{
                        $title = $row->title;
                    }
    
                    $productOrderDetailArea .= '
                        <table class="row"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%">
                            <tr class="order-list__item order-list__item--single"
                                style="border-bottom-color: #e5e5e5; border-bottom-style: none !important; border-bottom-width: 1px; width: 100%">
                                <td class="order-list__item__cell"
                                    style="padding: 0;">
                                    <table style="border-collapse: collapse; border-spacing: 0">
                                        <td
                                            style="">
                                            <img src="'.$image.'"
                                                align="left" width="60" height="60"
                                                class="order-list__product-image"
                                                style="border: 1px solid #e5e5e5; border-radius: 8px; margin-right: 15px">
                                        </td>
                                        <td class="order-list__product-description-cell"
                                            style=" width: 100%;">
                                            <span class="order-list__item-title"style="color: #555; font-size: 16px; font-weight: 600; line-height: 1.4">'. $title .' × '. $veriable->piece .'</span>
                                            <br>
                                            '.$colorView . ' ' . $sizeView.'
                                        </td>
                                        <td class="order-list__price-cell"
                                            style=" white-space: nowrap;">
                                            <p class="order-list__item-price"
                                                style="color: #555; font-size: 16px; font-weight: 600; line-height: 150%; margin: 0 0 0 15px; text-align: right"
                                                align="right">'. number_format($singlePrice, 2) .' TL</p>
                                        </td>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    ';
                }

                $delivery_address = $addressModels->c_one('order_address_clone', ['id' => $order->shipping_address]);
                $town = $addressModels->c_one('town', ['TownID' => $delivery_address->user_town], 'TownName ASC');
                $city = $addressModels->c_one('city', ['CityID' => $delivery_address->user_city], 'CityName ASC');
                $neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $delivery_address->user_neighborhood]);
        
                $billing_address = $addressModels->c_one('order_address_clone', ['id' => $order->billing_address]);
        
                $townBilling = $addressModels->c_one('town', ['TownID' => $billing_address->user_town], 'TownName ASC');
                $cityBilling = $addressModels->c_one('city', ['CityID' => $billing_address->user_city], 'CityName ASC');
                $neighborhoodBilling = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $billing_address->user_neighborhood]);


                $delivery_options = $orderModels->delivery_c_one(['id' => $order->shipping_id]);

                foreach ($vatRate as $key => $item) {
                    $vatPriceArea .= '
                        <tr class="subtotal-line">
                            <td class="subtotal-line__title"
                                style=" padding: 5px 0;">
                                <p
                                    style="color: #777; font-size: 16px; line-height: 1.2em; margin: 0">
                                    <span style="font-size: 16px">KDV ('.$key.'%)</span>
                                </p>
                            </td>
                            <td class="subtotal-line__value"
                                style=" padding: 5px 0; text-align: right;"
                                align="right"> <strong
                                    style="color: #555; font-size: 16px">'. number_format($item, 2) .' TL</strong>
    
                            </td>
                        </tr>
                    ';
                }

                if ($orderDetailFind) {
                    $sendMail = new SendMail();
                    $mailContent = '
                        <table class="body"
                            style="border-collapse: collapse; border-spacing: 0; height: 100% !important; width: 100% !important">
                            <tr>
                                <td
                                    style="">
                                    <table class="header row"
                                        style="border-collapse: collapse; border-spacing: 0; margin: 40px 0 20px; width: 100%">
                                        <tr>
                                            <td class="header__cell"
                                                style="">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="shop-name__cell"
                                                                            style="">
                                                                            <img src="'. base_url('public/frontend/assets/img/bilt/bilt_logo.png').'" width="180">
                                                                        </td>
                                                                        <td class="order-number__cell"
                                                                            style="color: #999; font-size: 14px; text-align: right; text-transform: uppercase"
                                                                            align="right"> <span class="order-number__text"
                                                                                style="font-size: 16px">
                                                                                Sipariş No : '. $order->order_no .'
                                                                            </span>
    
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row content" style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                        <tr>
                                            <td class="content__cell"
                                                style="padding-bottom: 40px;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h2 style="font-size: 24px; font-weight: normal; margin: 0 0 10px">Aşağıda belirtilen ürünler hazırlanıyor.</h2>
    
                                                                <p style="color: #777; font-size: 16px; line-height: 150%; margin: 0">Merhaba '. $userCheackOrder->name .', siparişinizde bulunan ürünler kargoya verilmek üzere hazırlanmaktadır..</p>
                                                                <table class="row actions"
                                                                    style="border-collapse: collapse; border-spacing: 0; margin-top: 20px; width: 100%">
                                                                    <tr>
                                                                        <td class="actions__cell"
                                                                            style="">
                                                                            <table class="button main-action-cell"
                                                                                style="border-collapse: collapse; border-spacing: 0; float: left; margin-right: 15px">
                                                                                <tr>
                                                                                    <td class="button__cell" style="background: #1f1f1f; border-radius: 4px; padding: 20px 25px; text-align: center;"
                                                                                        align="center" bgcolor="#1f1f1f">
                                                                                        <a href="'. base_url('/hesabim/detay/'. $order->order_no .'') .'" class="button__text"
                                                                                            style="color: #fff; font-size: 16px; text-decoration: none">Siparis Detayı</a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row section"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="section__cell"
                                                style="padding: 40px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h3 style="font-size: 20px; font-weight: normal; margin: 0 0 25px">Sipariş Detayları
                                                                </h3>
    
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                '. $productOrderDetailArea .'
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row section"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="section__cell"
                                                style="padding: 40px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h3 style="font-size: 20px; font-weight: normal; margin: 0 0 25px">Teslimat Fatura Bilgileri</h3>
    
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="customer-info__item"
                                                                            style="padding-bottom: 40px; width: 50%;">
                                                                            <h4
                                                                                style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
                                                                                Teslimat Adresi</h4>
        
                                                                            <p
                                                                                style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                '. $delivery_address->title .'
                                                                                <br>'. $delivery_address->receiver_name .'
                                                                                <br>'. $delivery_address->address .'
                                                                                <br>
                                                                                <br>'. $neighborhood->NeighborhoodName .', '. $town->TownName .'/'. $city->CityName .'</p>
                                                                        </td>
                                                                        <td class="customer-info__item"
                                                                            style=" padding-bottom: 40px; width: 50%;">
                                                                            <h4
                                                                                style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
                                                                                Fatura Adresi</h4>
        
                                                                            <p
                                                                                style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                '. $billing_address->title .'
                                                                                <br>'. $billing_address->receiver_name .'
                                                                                <br>'. $billing_address->address .'
                                                                                <br>
                                                                                <br>'. $neighborhoodBilling->NeighborhoodName .', '. $townBilling->TownName .'/'. $cityBilling->CityName .'</p>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="customer-info__item"
                                                                            style="padding-bottom: 40px; width: 50%; ">
                                                                            <h4
                                                                                style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
                                                                                Teslimat Tipi</h4>
        
                                                                            <p
                                                                                style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                '. $delivery_options->title .'</p>
                                                                        </td>
                                                                        <td class="customer-info__item"
                                                                            style=" padding-bottom: 40px; width: 50%;">
                                                                            <h4
                                                                                style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
                                                                                Sipariş Durumu</h4>
        
                                                                            <p class="customer-info__item-content"
                                                                                style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                <span style="font-size: 16px"><strong
                                                                                style="color: #555; font-size: 16px">Hazırlanıyor</strong></span>
                                                                            </p>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row footer"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="footer__cell"
                                                style=" padding: 35px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <p class="disclaimer__subtext"
                                                                    style="color: #999; font-size: 14px; line-height: 150%; margin: 0">Herhangi bir sorunuz varsa, bu adresten bize ulaşın:
                                                                    <a href="mailto:'. $contact['biltstore_info_email_general']->value .'"
                                                                        style="color: #1f1f1f; font-size: 14px; text-decoration: none">'. $contact['biltstore_info_email_general']->value .'</a>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        </body>
    
                        </html>
                        </body>
                    ';
                    $registerMail = $sendMail->SendMailOrder($userCheackOrder->email, $contact['biltstore_info_email']->value, $contact['biltstore_info_email_general']->value, "Siparişinizdeki ürünler hazırlanıyor..", $mailContent);
                }

            }elseif ($status == '3') {
                $updateOrderDetail = true;
            }elseif ($status == '4') {
                $updateOrderDetailData = [
                    'status' => $status,
                    'updated_at' => created_at()
                ];
                if (session()->get('admin')['role'] == '5') {
                    $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['order_id' => $order_id, 'status !=' => '5', 'status !=' => '6', 'status !=' => '9'], $updateOrderDetailData);
                }else{
                    $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['order_id' => $order_id, 'store_id' => session()->get('admin')['store_id'], 'status !=' => '5', 'status !=' => '6', 'status !=' => '9'], $updateOrderDetailData);
                }

                if (session()->get('admin')['role'] == '5') {
                    $orderDetailFind = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', ' status NOT IN ("5", "9") AND return_count IS NULL AND cancellation_count IS NULL ');
                }else{
                    $orderDetailFind = $orderModels->orderDetailNebimAll(['order_id' => $order_id, 'store_id' => session()->get('admin')['store_id']], '', ' status NOT IN ("5", "9") AND return_count IS NULL AND cancellation_count IS NULL ');
                }

                $contactSetting = $settingModels->c_all(['type' => 'contact']);
                $contact = namedSettings($contactSetting);

                $orderDetailCheack = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', ' status NOT IN ("4","5","9") ');

                if (!$orderDetailCheack) {
                    $updateOrderData = [
                        "status" => '4',
                        "updated_at" => created_at(),
                    ];
                    $insertOrder = $orderModels->edit($order_id, $updateOrderData);
                }

                foreach ($orderDetailFind as $veriable) {

                    $singlePrice = $veriable->price;
                    $cancellationPrice =  $singlePrice * $veriable->piece;

                    $returnJson = $iyzico->cancelRequest($veriable->iyziko_payment_transaction_id, ($cancellationPrice) );

                    $row = $productDetailModels->c_one(['p.id' => $veriable->product_id, 'pa.id' => $veriable->variant_id ]);
                    if ($row) {
                        $product_id = $veriable->product_id;
                        $variant_barcode = $veriable->variant_id;
                    }else{
                        $product_id = $veriable->product_id;
                    }
    
                    if ($variant_barcode) {
                        $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.is_active' => '1', 'pa.id' => $veriable->variant_id]);
                    }else{
                        $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.is_active' => '1']);
                    }
    
                    $productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' => $productCombinationOne->id], '1');
                    if (!$productPicture) {
                        $productPicture = $productDetailModels->c_all_image(['product_id' => $row->id], '1');
                    }
                    $featureArray = explode(' - ', $row->attr);
                    $attrubuteArray = explode(' - ', $productCombinationOne->attr_color);
                    $attrubuteIDArray = explode(' - ', $productCombinationOne->attr_color_id);
                    $categoryArray = explode(',', $row->category_id);
                    $color_id = $attrubuteIDArray['0'];
                    $color_title = $attrubuteArray['0'];
                    $attrubuteSizeArray = explode(' - ', $productCombinationOne->attr_size);
                    $attrubuteSizeIDArray = explode(' - ', $productCombinationOne->attr_size_id);
                    $size_id = $attrubuteSizeIDArray['0'];
                    $size_title = $attrubuteSizeArray['0'];
                    if ($size_title) {
                        $sizeView = '<span class="order-list__item-title"style="color: #555; font-size: 14px; line-height: 1.4"><b>Boyut</b> : '. $size_title .'</span>';
                    }
                    if ($color_title) {
                        $colorView = '<span class="order-list__item-title"style="color: #555; font-size: 14px; line-height: 1.4;"><b>Renk</b> : '. $color_title .'</span>';
                    }
                    foreach ($categoryArray as $item) {
                        $catFind = '';
                        if ($item) {
                            $catFind = $category->c_all_list('', $item);
                            if(!$catFind){
                                $endCatID = $item;
                            }
                        }
                    }
                    $data['arr']['topCategoryFind'] = array_reverse($category->c_top_all_list('', $endCatID));
                    $endCategoryFind = $category->c_one(["id" =>$endCatID]);
    
                    $brand = $row->b_title;
                    $colorTitle = $color_title;
                    $sizeTitle = $size_title;
                    
                    $singlePrice = $veriable->price * $veriable->piece;
    
                    if ($veriable->vat_rate) {
                        $vatRate[$veriable->vat_rate] = $vatRate[$veriable->vat_rate] + vat_add(($veriable->price), $veriable->vat_rate);
                        $totalVatRate = $totalVatRate + vat_add(($veriable->price), $veriable->vat_rate);
                    }
    
                    if ($productPicture) {
                        foreach ($productPicture as $item) {
                            if (file_exists('uploads/products/min/'.$item->image.'') && $item->image){
                                $image = base_url('/uploads/products/min/'.$item->image);
                            }else{
                                $image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                            }
                        }
                    }else{
                        $image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                    }
    
    
                    if ($productCombinationOne->title) {
                        $title = $productCombinationOne->title;
                    }else{
                        $title = $row->title;
                    }
    
                    $productOrderDetailArea .= '
                        <table class="row"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%">
                            <tr class="order-list__item order-list__item--single"
                                style="border-bottom-color: #e5e5e5; border-bottom-style: none !important; border-bottom-width: 1px; width: 100%">
                                <td class="order-list__item__cell"
                                    style="padding: 0;">
                                    <table style="border-collapse: collapse; border-spacing: 0">
                                        <td
                                            style="">
                                            <img src="'.$image.'"
                                                align="left" width="60" height="60"
                                                class="order-list__product-image"
                                                style="border: 1px solid #e5e5e5; border-radius: 8px; margin-right: 15px">
                                        </td>
                                        <td class="order-list__product-description-cell"
                                            style=" width: 100%;">
                                            <span class="order-list__item-title"style="color: #555; font-size: 16px; font-weight: 600; line-height: 1.4">'. $title .' × '. $veriable->piece .'</span>
                                            <br>
                                            '.$colorView . ' ' . $sizeView.'
                                        </td>
                                        <td class="order-list__price-cell"
                                            style=" white-space: nowrap;">
                                            <p class="order-list__item-price"
                                                style="color: #555; font-size: 16px; font-weight: 600; line-height: 150%; margin: 0 0 0 15px; text-align: right"
                                                align="right">'. number_format($singlePrice, 2) .' TL</p>
                                        </td>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    ';
                }

                $delivery_address = $addressModels->c_one('order_address_clone', ['id' => $order->shipping_address]);
                $town = $addressModels->c_one('town', ['TownID' => $delivery_address->user_town], 'TownName ASC');
                $city = $addressModels->c_one('city', ['CityID' => $delivery_address->user_city], 'CityName ASC');
                $neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $delivery_address->user_neighborhood]);
        
                $billing_address = $addressModels->c_one('order_address_clone', ['id' => $order->billing_address]);
        
                $townBilling = $addressModels->c_one('town', ['TownID' => $billing_address->user_town], 'TownName ASC');
                $cityBilling = $addressModels->c_one('city', ['CityID' => $billing_address->user_city], 'CityName ASC');
                $neighborhoodBilling = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $billing_address->user_neighborhood]);


                $delivery_options = $orderModels->delivery_c_one(['id' => $order->shipping_id]);

                foreach ($vatRate as $key => $item) {
                    $vatPriceArea .= '
                        <tr class="subtotal-line">
                            <td class="subtotal-line__title"
                                style=" padding: 5px 0;">
                                <p
                                    style="color: #777; font-size: 16px; line-height: 1.2em; margin: 0">
                                    <span style="font-size: 16px">KDV ('.$key.'%)</span>
                                </p>
                            </td>
                            <td class="subtotal-line__value"
                                style=" padding: 5px 0; text-align: right;"
                                align="right"> <strong
                                    style="color: #555; font-size: 16px">'. number_format($item, 2) .' TL</strong>
    
                            </td>
                        </tr>
                    ';
                }

                if ($orderDetailFind) {
                    $sendMail = new SendMail();
                    $mailContent = '
                        <table class="body"
                            style="border-collapse: collapse; border-spacing: 0; height: 100% !important; width: 100% !important">
                            <tr>
                                <td
                                    style="">
                                    <table class="header row"
                                        style="border-collapse: collapse; border-spacing: 0; margin: 40px 0 20px; width: 100%">
                                        <tr>
                                            <td class="header__cell"
                                                style="">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="shop-name__cell"
                                                                            style="">
                                                                            <img src="'. base_url('public/frontend/assets/img/bilt/bilt_logo.png').'" width="180">
                                                                        </td>
                                                                        <td class="order-number__cell"
                                                                            style="color: #999; font-size: 14px; text-align: right; text-transform: uppercase"
                                                                            align="right"> <span class="order-number__text"
                                                                                style="font-size: 16px">
                                                                                Sipariş No : '. $order->order_no .'
                                                                            </span>
    
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row content" style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                        <tr>
                                            <td class="content__cell"
                                                style="padding-bottom: 40px;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h2 style="font-size: 24px; font-weight: normal; margin: 0 0 10px">Aşağıda belirtilen ürünler teslim edildi.</h2>
    
                                                                <p style="color: #777; font-size: 16px; line-height: 150%; margin: 0">Merhaba '. $userCheackOrder->name .', siparişinizde bulunan ürünler teslim edildi..</p>
                                                                <table class="row actions"
                                                                    style="border-collapse: collapse; border-spacing: 0; margin-top: 20px; width: 100%">
                                                                    <tr>
                                                                        <td class="actions__cell"
                                                                            style="">
                                                                            <table class="button main-action-cell"
                                                                                style="border-collapse: collapse; border-spacing: 0; float: left; margin-right: 15px">
                                                                                <tr>
                                                                                    <td class="button__cell" style="background: #1f1f1f; border-radius: 4px; padding: 20px 25px; text-align: center;"
                                                                                        align="center" bgcolor="#1f1f1f">
                                                                                        <a href="'. base_url('/hesabim/detay/'. $order->order_no .'') .'" class="button__text"
                                                                                            style="color: #fff; font-size: 16px; text-decoration: none">Siparis Detayı</a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row section"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="section__cell"
                                                style="padding: 40px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h3 style="font-size: 20px; font-weight: normal; margin: 0 0 25px">Sipariş Detayları
                                                                </h3>
    
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                '. $productOrderDetailArea .'
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row section"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="section__cell"
                                                style="padding: 40px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h3 style="font-size: 20px; font-weight: normal; margin: 0 0 25px">Teslimat Fatura Bilgileri</h3>
    
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="customer-info__item"
                                                                            style="padding-bottom: 40px; width: 50%;">
                                                                            <h4
                                                                                style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
                                                                                Teslimat Adresi</h4>
        
                                                                            <p
                                                                                style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                '. $delivery_address->title .'
                                                                                <br>'. $delivery_address->receiver_name .'
                                                                                <br>'. $delivery_address->address .'
                                                                                <br>
                                                                                <br>'. $neighborhood->NeighborhoodName .', '. $town->TownName .'/'. $city->CityName .'</p>
                                                                        </td>
                                                                        <td class="customer-info__item"
                                                                            style=" padding-bottom: 40px; width: 50%;">
                                                                            <h4
                                                                                style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
                                                                                Fatura Adresi</h4>
        
                                                                            <p
                                                                                style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                '. $billing_address->title .'
                                                                                <br>'. $billing_address->receiver_name .'
                                                                                <br>'. $billing_address->address .'
                                                                                <br>
                                                                                <br>'. $neighborhoodBilling->NeighborhoodName .', '. $townBilling->TownName .'/'. $cityBilling->CityName .'</p>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="customer-info__item"
                                                                            style="padding-bottom: 40px; width: 50%; ">
                                                                            <h4
                                                                                style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
                                                                                Teslimat Tipi</h4>
        
                                                                            <p
                                                                                style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                '. $delivery_options->title .'</p>
                                                                        </td>
                                                                        <td class="customer-info__item"
                                                                            style=" padding-bottom: 40px; width: 50%;">
                                                                            <h4
                                                                                style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
                                                                                Sipariş Durumu</h4>
        
                                                                            <p class="customer-info__item-content"
                                                                                style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                <span style="font-size: 16px"><strong
                                                                                style="color: #555; font-size: 16px">Tamamlandı</strong></span>
                                                                            </p>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row footer"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="footer__cell"
                                                style=" padding: 35px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <p class="disclaimer__subtext"
                                                                    style="color: #999; font-size: 14px; line-height: 150%; margin: 0">Herhangi bir sorunuz varsa, bu adresten bize ulaşın:
                                                                    <a href="mailto:'. $contact['biltstore_info_email_general']->value .'"
                                                                        style="color: #1f1f1f; font-size: 14px; text-decoration: none">'. $contact['biltstore_info_email_general']->value .'</a>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        </body>
    
                        </html>
                        </body>
                    ';
                    $registerMail = $sendMail->SendMailOrder($userCheackOrder->email, $contact['biltstore_info_email']->value, $contact['biltstore_info_email_general']->value, "Siparişinizdeki ürünler teslim edildi..", $mailContent);
                }

            }elseif ($status == '5') {

                if (session()->get('admin')['role'] == '5') {
                    $orderDetailFind = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', ' status NOT IN ("5", "9") ');
                }else{
                    $orderDetailFind = $orderModels->orderDetailNebimAll(['order_id' => $order_id, 'store_id' => session()->get('admin')['store_id']], '', ' status NOT IN ("5", "9") ');
                }

                $updateOrderDetailData = [
                    'status' => $status,
                    'orederCanceledNote' => $orederCanceledNote,
                    'updated_at' => created_at()
                ];
                if (session()->get('admin')['role'] == '5') {
                    $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['order_id' => $order_id, 'status !=' => '5', 'status !=' => '6', 'status !=' => '9'], $updateOrderDetailData);
                }else{
                    $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['order_id' => $order_id, 'store_id' => session()->get('admin')['store_id'], 'status !=' => '5', 'status !=' => '6', 'status !=' => '9'], $updateOrderDetailData);
                }

                $contactSetting = $settingModels->c_all(['type' => 'contact']);
                $contact = namedSettings($contactSetting);

                $orderDetailCheack = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', ' status NOT IN ("5", "9") ');

                if (!$orderDetailCheack) {
                    $updateOrderData = [
                        "status" => '5',
                        "overall_total" => '0',
                        "vat_price" => '0',
                        "total_price" => '0',
                        "updated_at" => created_at(),
                    ];
                    $insertOrder = $orderModels->edit($order_id, $updateOrderData);
                }

                foreach ($orderDetailFind as $veriable) {

                    $singlePrice = $veriable->price;
                    $cancellationPrice =  $singlePrice * $veriable->piece;

                    $returnJson = $iyzico->cancelRequest($veriable->iyziko_payment_transaction_id, ($cancellationPrice) );

                    $row = $productDetailModels->c_one(['p.id' => $veriable->product_id, 'pa.id' => $veriable->variant_id ]);
                    if ($row) {
                        $product_id = $veriable->product_id;
                        $variant_barcode = $veriable->variant_id;
                    }else{
                        $product_id = $veriable->product_id;
                    }
    
                    if ($variant_barcode) {
                        $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.is_active' => '1', 'pa.id' => $veriable->variant_id]);
                    }else{
                        $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.is_active' => '1']);
                    }
    
                    $productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' => $productCombinationOne->id], '1');
                    if (!$productPicture) {
                        $productPicture = $productDetailModels->c_all_image(['product_id' => $row->id], '1');
                    }
                    $featureArray = explode(' - ', $row->attr);
                    $attrubuteArray = explode(' - ', $productCombinationOne->attr_color);
                    $attrubuteIDArray = explode(' - ', $productCombinationOne->attr_color_id);
                    $categoryArray = explode(',', $row->category_id);
                    $color_id = $attrubuteIDArray['0'];
                    $color_title = $attrubuteArray['0'];
                    $attrubuteSizeArray = explode(' - ', $productCombinationOne->attr_size);
                    $attrubuteSizeIDArray = explode(' - ', $productCombinationOne->attr_size_id);
                    $size_id = $attrubuteSizeIDArray['0'];
                    $size_title = $attrubuteSizeArray['0'];
                    if ($size_title) {
                        $sizeView = '<span class="order-list__item-title"style="color: #555; font-size: 14px; line-height: 1.4"><b>Boyut</b> : '. $size_title .'</span>';
                    }
                    if ($color_title) {
                        $colorView = '<span class="order-list__item-title"style="color: #555; font-size: 14px; line-height: 1.4;"><b>Renk</b> : '. $color_title .'</span>';
                    }
                    foreach ($categoryArray as $item) {
                        $catFind = '';
                        if ($item) {
                            $catFind = $category->c_all_list('', $item);
                            if(!$catFind){
                                $endCatID = $item;
                            }
                        }
                    }
                    $data['arr']['topCategoryFind'] = array_reverse($category->c_top_all_list('', $endCatID));
                    $endCategoryFind = $category->c_one(["id" =>$endCatID]);
    
                    $brand = $row->b_title;
                    $colorTitle = $color_title;
                    $sizeTitle = $size_title;
                    
                    $singlePrice = $veriable->price * $veriable->piece;
    
                    if ($veriable->vat_rate) {
                        $vatRate[$veriable->vat_rate] = $vatRate[$veriable->vat_rate] + vat_add(($veriable->price), $veriable->vat_rate);
                        $totalVatRate = $totalVatRate + vat_add(($veriable->price), $veriable->vat_rate);
                    }
    
                    if ($productPicture) {
                        foreach ($productPicture as $item) {
                            if (file_exists('uploads/products/min/'.$item->image.'') && $item->image){
                                $image = base_url('/uploads/products/min/'.$item->image);
                            }else{
                                $image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                            }
                            
                        }
                    }else{
                        $image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                    }
    
    
                    if ($productCombinationOne->title) {
                        $title = $productCombinationOne->title;
                    }else{
                        $title = $row->title;
                    }
    
                    $productOrderDetailArea .= '
                        <table class="row"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%">
                            <tr class="order-list__item order-list__item--single"
                                style="border-bottom-color: #e5e5e5; border-bottom-style: none !important; border-bottom-width: 1px; width: 100%">
                                <td class="order-list__item__cell"
                                    style="padding: 0;">
                                    <table style="border-collapse: collapse; border-spacing: 0">
                                        <td
                                            style="">
                                            <img src="'.$image.'"
                                                align="left" width="60" height="60"
                                                class="order-list__product-image"
                                                style="border: 1px solid #e5e5e5; border-radius: 8px; margin-right: 15px">
                                        </td>
                                        <td class="order-list__product-description-cell"
                                            style=" width: 100%;">
                                            <span class="order-list__item-title"style="color: #555; font-size: 16px; font-weight: 600; line-height: 1.4">'. $title .' × '. $veriable->piece .'</span>
                                            <br>
                                            '.$colorView . ' ' . $sizeView.'
                                        </td>
                                        <td class="order-list__price-cell"
                                            style=" white-space: nowrap;">
                                            <p class="order-list__item-price"
                                                style="color: #555; font-size: 16px; font-weight: 600; line-height: 150%; margin: 0 0 0 15px; text-align: right"
                                                align="right">'. number_format($singlePrice, 2) .' TL</p>
                                        </td>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    ';
                }
    
                foreach ($vatRate as $key => $item) {
                    $vatPriceArea .= '
                        <tr class="subtotal-line">
                            <td class="subtotal-line__title"
                                style=" padding: 5px 0;">
                                <p
                                    style="color: #777; font-size: 16px; line-height: 1.2em; margin: 0">
                                    <span style="font-size: 16px">KDV ('.$key.'%)</span>
                                </p>
                            </td>
                            <td class="subtotal-line__value"
                                style=" padding: 5px 0; text-align: right;"
                                align="right"> <strong
                                    style="color: #555; font-size: 16px">'. number_format($item, 2) .' TL</strong>
    
                            </td>
                        </tr>
                    ';
                }

                if ($orderDetailFind) {
                    $sendMail = new SendMail();
                    $mailContent = '
                        <table class="body"
                            style="border-collapse: collapse; border-spacing: 0; height: 100% !important; width: 100% !important">
                            <tr>
                                <td
                                    style="">
                                    <table class="header row"
                                        style="border-collapse: collapse; border-spacing: 0; margin: 40px 0 20px; width: 100%">
                                        <tr>
                                            <td class="header__cell"
                                                style="">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="shop-name__cell"
                                                                            style="">
                                                                            <img src="'. base_url('public/frontend/assets/img/bilt/bilt_logo.png').'" width="180">
                                                                        </td>
                                                                        <td class="order-number__cell"
                                                                            style="color: #999; font-size: 14px; text-align: right; text-transform: uppercase"
                                                                            align="right"> <span class="order-number__text"
                                                                                style="font-size: 16px">
                                                                                Sipariş No : '. $order->order_no .'
                                                                            </span>
    
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row content" style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                        <tr>
                                            <td class="content__cell"
                                                style="padding-bottom: 40px;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h2 style="font-size: 24px; font-weight: normal; margin: 0 0 10px">Aşağıda belirtilen ürünler iptal edilmiştir.</h2>
    
                                                                <p style="color: #777; font-size: 16px; line-height: 150%; margin: 0">Merhaba '. $userCheackOrder->name .', siparişinizde bulunan bazi ürünler belirtilen sebep sonuçunda iptal edilmştir.</p>
                                                                <table class="row actions"
                                                                    style="border-collapse: collapse; border-spacing: 0; margin-top: 20px; width: 100%">
                                                                    <tr>
                                                                        <td class="actions__cell"
                                                                            style="">
                                                                            <table class="button main-action-cell"
                                                                                style="border-collapse: collapse; border-spacing: 0; float: left; margin-right: 15px">
                                                                                <tr>
                                                                                    <td class="button__cell" style="background: #1f1f1f; border-radius: 4px; padding: 20px 25px; text-align: center;"
                                                                                        align="center" bgcolor="#1f1f1f">
                                                                                        <a href="'. base_url('/hesabim/detay/'. $order->order_no .'') .'" class="button__text"
                                                                                            style="color: #fff; font-size: 16px; text-decoration: none">Siparis Detayı</a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row section"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="section__cell"
                                                style="padding: 40px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h3 style="font-size: 20px; font-weight: normal; margin: 0 0 25px">Sipariş Detayları
                                                                </h3>
    
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                '. $productOrderDetailArea .'
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row section"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="section__cell"
                                                style="padding: 40px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h3 style="font-size: 20px; font-weight: normal; margin: 0 0 25px">İptal Sebebi</h3>
    
                                                            </td>
                                                        </tr>
                                                    </table>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="customer-info__item"
                                                                            style="padding-bottom: 40px; width: 50%;">
                                                                            <p style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
                                                                                '. $orederCanceledNote .'
                                                                            </p>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row footer"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="footer__cell"
                                                style=" padding: 35px 0;">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <p class="disclaimer__subtext"
                                                                    style="color: #999; font-size: 14px; line-height: 150%; margin: 0">Herhangi bir sorunuz varsa, bu adresten bize ulaşın:
                                                                    <a href="mailto:'. $contact['biltstore_info_email_general']->value .'"
                                                                        style="color: #1f1f1f; font-size: 14px; text-decoration: none">'. $contact['biltstore_info_email_general']->value .'</a>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        </body>
    
                        </html>
                        </body>
                    ';
                    $registerMail = $sendMail->SendMailOrder($userCheackOrder->email, $contact['biltstore_info_email']->value, $contact['biltstore_info_email_general']->value, "Siparişinizdeki ürünler iptal edildi.", $mailContent);
                }
               
            }

            if (!$data['error']) {
                if ($updateOrderDetail) {
                    $data['success'] = 'Sipariş başarılı bir şekilde düzenlendi.';
                }else{
                    $data['error'] = 'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
                } 
            }
           
        }
        return json_encode($data);
    }

    public function orderProductNebimAgain()
    {
        ini_set('max_execution_time', 1200);
        $db = db_connect();
        $order_id = $this->request->getPost('value');
        $userModels = new UserModels($db);
        $orderModels = new OrderModels($db);
        $addressModels = new AddressModels($db);

        $orderFind = $orderModels->c_one(['id' => $order_id]);
        $orderDetailFind = $orderModels->orderDetailNebimAll(['order_id' => $order_id]);
        $userFind = $userModels->c_one(['id' => $orderFind->user_id]);
        $userShippingAdress = $addressModels->c_one('order_address_clone', ['id' => $orderFind->shipping_address]);
        $userBillingAdress = $addressModels->c_one('order_address_clone', ['id' => $orderFind->billing_address]);
        $client = \Config\Services::curlrequest();
        $nebimCustomerData = [
            "ModelType" => '6',
            "CustomerCode" => "".$userFind->nebim_CurrAccCode."",
            "StoreCode" => "S008",
            "PosTerminalID" => "1",
            "WarehouseCode" => "S008",
            "ShipmentMethodCode" => "2",
            "DeliveryCompanyCode" => "001",
            "IsSalesViaInternet" => true,
            "DocumentNumber" => "". $orderFind->order_no ."",
            "Description" => "". $orderFind->order_note ."",
            "BillingPostalAddressID" => "". $userBillingAdress->nebim_PostalAddressID ."",
            "ShippingPostalAddressID" => "". $userShippingAdress->nebim_PostalAddressID ."",
            "IsCompleted" => true,
            "Payments" => [
                [
                    "PaymentType" => "2",
                    "Code" => "",
                    "CreditCardTypeCode" => "IYZ",
                    "InstallmentCount" => "1",
                    "CurrencyCode" => "TRY",
                    "Amount" =>  floatval($orderFind->total_price),
                ]
            ],
            "OrdersViaInternetInfo" => [
                "SalesURL" => base_url().'/',
                "PaymentTypeCode" => 1,
                "PaymentTypeDescription" => "KREDIKARTI/BANKAKARTI",
                "PaymentAgent" => "Iyzoco",
                "PaymentDate" => "". nowDate() ."T". nowTime() ."Z",
                "SendDate" => "". nowDate() ."T". nowTime() ."Z",
            ],    
        ];
        
        foreach ($orderDetailFind as $key => $row) {
            helper('text');
            $LineID = "". random_string('crypto', 8) ."-". random_string('crypto', 4) ."-". random_string('crypto', 4) ."-". random_string('crypto', 4) ."-". random_string('crypto', 12) ."";
            $nebimCustomerData['Lines'][]  = [
                "LineID" => "". $LineID ."",
                "UsedBarcode" => "". $row->variant_barcode ."",
                "Qty1" => $row->piece,
                "PriceVI" => ($row->price / $row->piece),
            ];
            $updateOrderDetailDate = [
                "order_id" => $row->order_id,
                "product_order_no" => $row->product_order_no,
                "nebim_line_id" => $LineID,
                "iyziko_payment_transaction_id" => $row->iyziko_payment_transaction_id,
                "request_no" => $row->request_no,
                "user_id" => $row->user_id,
                "product_id" => $row->product_id,
                "variant_id" => $row->variant_id,
                "variant_barcode" => $row->variant_barcode,
                "vat_rate" => $row->vat_rate,
                "price" => $row->price / $row->piece,
                "piece" => 1,
                "coupon_id" => $row->coupon_id,
                "coupon_discount_type" => $row->coupon_discount_type,
                "coupon_discount" => $row->coupon_discount,
                "coupon_rate" => $row->coupon_rate,
                "color_id" => $row->color_id,
                "size_id" => $row->size_id,
                "status" => $row->status,
                "created_at" => $row->created_at,
                "updated_at" => created_at()
            ];
            $updateOrder = $orderModels->orderDetailNebimUpdate($row->id, $updateOrderDetailDate);
        }
        
        $nebimCustomerData['Lines'][]  = [
            "ItemTypeCode" => "5",
            "ItemCode" => "KRG",
            "Qty1" => "1",
            "PriceVI" => floatval($orderFind->shipping_price),
        ];
        
        $updateOrderDate = [
            "nebim_OrderNumber" => 'Nebim Gitti',
            "updated_at" => created_at()
        ];
        $updateOrder = $orderModels->edit($order_id, $updateOrderDate);
        echo json_encode($nebimCustomerData);
        echo 'deneme';
    }
    
    
}