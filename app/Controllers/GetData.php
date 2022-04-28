<?php

namespace App\Controllers;
use CodeIgniter\I18n\Time;
use App\Models\UserModels;
use App\Models\AddressModels;
use App\Models\Category;
use App\Models\ProductCategoryModels;
use App\Models\ProductDetailModels;
use App\Models\CampaignModels;
use App\Models\OrderModels;
use App\Models\SearchModels;
use App\Models\SettingModels;
use App\Models\AttributeGroupModels;
use App\Models\AttributeModels;
use Picqer\Barcode;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;
use function BenTools\StringCombinations\string_combinations;

class GetData extends BaseController
{
	public function citySelectd()
	{
        $db = db_connect();
        $user = new UserModels($db);
        $addressModels = new AddressModels($db);
         if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
        $city_id = $this->request->getPost('id');
        $cityFind = $addressModels->c_all('town', ['CityID' => $city_id]); 
        foreach ($cityFind as $key => $row) {
            $data['arr'][$key]['title'] = $row->TownName;
            $data['arr'][$key]['val'] = $row->TownID;
        }
        return json_encode($data);
	}

    public function townSelectd()
	{
        $db = db_connect();
        $user = new UserModels($db);
        $addressModels = new AddressModels($db);
         if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
        $thisID = $this->request->getPost('id');
        $userNeigh = $this->request->getPost('userNeigh');
        $townFind = $addressModels->c_all('town', ['TownID' => $thisID]); 
        if (!$townFind) {
            $data['error'] = 'Aradığınız il bulunamadı.';
        }else{
            $thisChild = $addressModels->neighborhood_all(['d.TownID' => $thisID]); 
            foreach ($thisChild as $key => $row) {
                $data['arr'][$key]['title'] = $row->NeighborhoodName;
                $data['arr'][$key]['userNeigh'] = $userNeigh;
                $data['arr'][$key]['val'] = $row->NeighborhoodID;
            }
        }
        return json_encode($data);
	}

    public function productQuickview()
	{
        $db = db_connect();
        $attributeModels = new AttributeModels($db);
        $productDetailModels = new ProductDetailModels($db);
        $productCategoryModels = new ProductCategoryModels($db);
        $campaignModels = new CampaignModels($db);
        $category = new Category($db);
        $product_id = $this->request->getPost('product_id');
        $variant_barcode = $this->request->getPost('variant_barcode');

        $row = $productDetailModels->c_one(['p.id' => $product_id, 'p.is_active']); 
        if (!$row) {
            $data['error'] = 'Aradiğiniz ürün bulunamadı.';
        }else{
            if ($variant_barcode) {
                $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.id' => $variant_barcode]);
                if (!$productCombinationOne) {
                    $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.barcode_no' => $variant_barcode]);
                    if (!$productCombinationOne) {
                        $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1']);
                    }
                }
            }else{
                $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1']);
            }
            $productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' =>$productCombinationOne->id], 1);
            if (!$productPicture) {
                $productPicture = $productDetailModels->c_all_image(['product_id' => $row->id], 1);
            }
            $featureArray = explode(' - ', $row->attr);
            $categoryArray = explode(',', $row->category_id);

            $combinationIDArray = explode(' - ', $productCombinationOne->attr_id);
            $combinationGroupIDArray = explode(' - ', $productCombinationOne->attr_group_id);
            $combinationGroupTitleArray = explode(' - ', $productCombinationOne->attr);
            foreach ($combinationGroupIDArray as $key => $val) {
                $selectCombinationArray[$val] = $combinationIDArray[$key];
                $selectCombinationArrayTitle[$val] = $combinationGroupTitleArray[$key];
            }
            $productCombinationAll = $productDetailModels->productCombinationAll(['p.id' => $product_id]);
            $productCombinationIDArray = explode(', ', $productCombinationAll->a_id);
            $productCombinationTitleArray = explode(', ', $productCombinationAll->a_id);
            $i = 0 ;
            foreach ($productCombinationIDArray as $key => $val) {
                $attrGroupFind = $attributeModels->c_one(['a.id' => $val, 'a.is_active' => '2']);
                $inCombination = '';
                foreach ($selectCombinationArray as $keySelect => $selectVal) {
                    if ($attrGroupFind->ag_id != $keySelect) {
                        $inCombination .= $selectVal . ',' ;
                    }
                }
                $inCombination = $inCombination . '' . $attrGroupFind->id;
                $inCombinationArray = explode(',' , $inCombination);
                $combinationFind = $productDetailModels->productCombinationOne(['pa.id_product' => $product_id, 'pa.is_active !=' => '3'], '' , $inCombinationArray);
               
                if ($attrGroupFind) {
                    if ($combinationGroup[$attrGroupFind->ag_id]) {
                        $combinationGroup[$attrGroupFind->ag_id][$i]['group_title'] =  $attrGroupFind->ag_title;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['group_slug'] =  $attrGroupFind->ag_slug;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['title'] =  $attrGroupFind->title;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['id'] =  $attrGroupFind->id;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['is_color'] = $attrGroupFind->is_color;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['color'] = $attrGroupFind->color;
                        if ($combinationFind && $combinationFind->stock == 0) {
                            $combinationGroup[$attrGroupFind->ag_id][$i]['disabled'] = 'disabled';
                        }
                    }else{
                        $i = 0 ;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['group_title'] =  $attrGroupFind->ag_title;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['group_slug'] =  $attrGroupFind->ag_slug;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['title'] =  $attrGroupFind->title;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['id'] =  $attrGroupFind->id;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['is_color'] = $attrGroupFind->is_color;
                        $combinationGroup[$attrGroupFind->ag_id][$i]['color'] = $attrGroupFind->color;
                        if ($combinationFind && $combinationFind->stock == 0) {
                            $combinationGroup[$attrGroupFind->ag_id][$i]['disabled'] = 'disabled';
                        }
                    }
                }
                ++$i;
            }

            $data['combinationGroup'] = $combinationGroup;
            $data['selectCombinationArray'] = $selectCombinationArray;
            $data['selectCombinationArrayTitle'] = $selectCombinationArrayTitle;

            foreach ($categoryArray as $item) {
                $catFind = '';
                if ($item) {
                    $catFind = $category->c_all_list('', $item);
                    if(!$catFind){
                        $endCatID = $item;
                    }
                }
            }
            $endCategoryFind = $category->c_one(["id" =>$endCatID]);

            if ($row->campaign_id) {
                $productCampaign = $campaignModels->c_one_index(['id' => $row->campaign_id, 'is_active' => '1']);
            }else{
                $productCampaign = '';
            }
            
           

            if ($productCombinationSelect->sale_price) {
                $priceArray = priceAreaFunction($productCombinationSelect->sale_price, $productCombinationSelect->discount_price, $productCombinationSelect->basket_price, $productCampaign->discount);
            }elseif ($productCombinationOne->sale_price) {
                $priceArray = priceAreaFunction($productCombinationOne->sale_price, $productCombinationOne->discount_price, $productCombinationOne->basket_price, $productCampaign->discount);
            }else{
                $priceArray = priceAreaFunction($row->sale_price, $row->discount_price, $row->basket_price, $productCampaign->discount);
            }
            $data['arr']['productID'] = $product_id;
            $data['arr']['variantID'] = $productCombinationOne->id;
            $data['arr']['variantBarcode'] = $variant_barcode;
            $data['arr']['link'] = $row->slug.'-p-'.$variant_barcode;

            if ($priceArray['discountBool']) {
                $data['arr']['discountBool'] = $priceArray['discountBool'];
            }
            if ($priceArray['discountRate']) {
                $data['arr']['discountRate'] = number_format($priceArray['discountRate'], 2);
            }
            if ($priceArray['totalPrice']) {
                $data['arr']['totalPrice'] = number_format(floor(($priceArray['totalPrice']*100))/100, 2);
            }
            if ($priceArray['discountPrice']) {
                $data['arr']['discountPrice'] = number_format(floor(($priceArray['discountPrice']*100))/100, 2);
            }
            if ($priceArray['basketBool']) {
                $data['arr']['basketBool'] = number_format($priceArray['basketBool'], 2);
            }
            if ($priceArray['basketPrice']) {
                $data['arr']['basketPrice'] = number_format($priceArray['basketPrice'], 2);
            }
            if ($priceArray['basketRate']) {
                $data['arr']['basketRate'] = number_format($priceArray['basketRate'], 1);
            }
            $data['arr']['brand'] = $row->b_title;
            $data['arr']['colorTitle'] = $color_title;
            $data['arr']['colorID'] = $color_id;
            $data['arr']['sizeTitle'] = $size_title;
            $data['arr']['sizeID'] = $size_id;
            $data['arr']['stock'] = $productCombinationOne->stock;
            
            if ($productPicture) {
                foreach ($productPicture as $item) {
                    if (file_exists('uploads/products/min/'.$item->image.'') && $item->image){
                        $data['arr']['image'] = base_url('/uploads/products/min/'.$item->image);
                    }else{
                        $data['arr']['image'] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                    }
                    
                }
            }else{
                $data['arr']['image'] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
            }
            
            if ($productCombinationOne->title) {
                $data['arr']['title'] = $productCombinationOne->title;
            }else{
                $data['arr']['title'] = $row->title;
            }

            if (isset($this->data['user_id'])) {
                $userModels = new UserModels($db);
                if ($productCombinationOne->id) {
                    $favoriteFind = $userModels->favorite_one (["user_id" => $this->data['user_id'] , "product_id" => $row->id, 'variant_id' => $productCombinationOne->id]);
                }else{
                    $favoriteFind = $userModels->favorite_one (["user_id" => $this->data['user_id'] , "product_id" => $row->id]);
                }
                if ($favoriteFind) {
                    $data['arr']['favorite'] = TRUE;
                }else{
                    $data['arr']['favorite'] = FALSE;
                }
            }else{
                $data['arr']['favorite'] = FALSE;
            }
        }
        return json_encode($data);
	}

    public function productStock()
	{
        $db = db_connect();
        $productCategoryModels = new ProductCategoryModels($db);
        $category = new Category($db);
        $product_id = $this->request->getPost('product_id');
        $size_id = $this->request->getPost('size_id');
        $variant_id = $this->request->getPost('variant_id');

        $row = $productCategoryModels->c_one(['p.id' => $product_id, 'p.is_active']); 
        if (!$row) {
            $data['error'] = 'Aradığınız ürün bulunamadı.';
        }else{
            if ($variant_id) {
                $productCombinationOne = $productCategoryModels->productCombinationOne(['pa.id_product' =>$row->id, 'pa.is_active' => '1', 'pa.is_active' => '1', 'pa.id' => $variant_id]);
            }else{
                $productCombinationOne = $productCategoryModels->productCombinationOne(['pa.id_product' =>$row->id, 'pa.is_active' => '1', 'pa.is_active' => '1', "pac.attribute_id" => $size_id]);
            }
            $data['arr']['productID'] = $product_id;
            $data['arr']['variantID'] = $productCombinationOne->id;
            $data['arr']['stock'] = $productCombinationOne->stock;
        }
        return json_encode($data);
	}

    public function getPriceArea()
	{
        $db = db_connect();
        $productDetailModels = new ProductDetailModels($db);
        $productCategoryModels = new ProductCategoryModels($db);
        $campaignModels = new CampaignModels($db);
        $category = new Category($db);
        $product_id = $this->request->getPost('product_id');
        $combination = $this->request->getPost('combination');

        foreach ($combination as $item) {
            $inCombination .= $item.',';
        }
        $inCombination = rtrim($inCombination, ',');
        $inCombinationArray = explode(',' , $inCombination);
        $row = $productCategoryModels->c_one(['p.id' => $product_id, 'p.is_active' => '1']); 
        if (!$row) {
            $data['error'] = 'Aradığınız ürün bulunamadı.';
        }else{
            $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $product_id, 'pa.is_active !=' => '3'], '' , $inCombinationArray);
            $data['arr']['productID'] = $product_id;
            $data['arr']['variantID'] = $productCombinationOne->id;
            $data['arr']['stock'] = $productCombinationOne->stock;

            $data['arr']['productPicture'] = $productDetailModels->attributePictureAll(['pap.product_attribute_id' => $productCombinationOne->id]);
            if (!$data['arr']['productPicture']) {
                $data['arr']['productPicture'] = $productDetailModels->c_all_image(['product_id' => $row->id]);
            }

            $discountBool = FALSE;
            $discountRate = '0.00';
            if ($row->campaign_id) {
                $productCampaign = $campaignModels->c_one_index(['id' => $row->campaign_id, 'is_active' => '1']);
            }else{
                $productCampaign = '';
            }
            
            if ($productCombinationOne->sale_price) {
                $priceArray = priceAreaFunction($productCombinationOne->sale_price, $productCombinationOne->discount_price, $productCombinationOne->basket_price, $productCampaign->discount);
            }else{
                $priceArray = priceAreaFunction($row->sale_price, $row->discount_price, $row->basket_price, $productCampaign->discount);
            }

            if ($priceArray['discountBool']) {
                $data['arr']['discountBool'] = $priceArray['discountBool'];
            }
            if ($priceArray['discountRate']) {
                $data['arr']['discountRate'] = number_format($priceArray['discountRate'], 2);
            }
            if ($priceArray['totalPrice']) {
                $data['arr']['totalPrice'] = number_format(floor(($priceArray['totalPrice']*100))/100, 2);
            }
            if ($priceArray['discountPrice']) {
                $data['arr']['discountPrice'] = number_format(floor(($priceArray['discountPrice']*100))/100, 2);
            }
            if ($priceArray['basketBool']) {
                $data['arr']['basketBool'] = number_format($priceArray['basketBool'], 2);
            }
            if ($priceArray['basketPrice']) {
                $data['arr']['basketPrice'] = number_format($priceArray['basketPrice'], 2);
            }
            if ($priceArray['basketRate']) {
                $data['arr']['basketRate'] = number_format($priceArray['basketRate'], 1);
            }
        }
        return json_encode($data);
	}

    public function getCargoReceiptPrint()
	{
        $db =  db_connect();
        $orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
		$settingModels = new SettingModels($db);

        $contactSetting = $settingModels->c_all(['type' => 'contact']);
        $contact = namedSettings($contactSetting);

        $order_id = $this->request->getPost('order_id');
        $status = $this->request->getPost('status');
        $invonceNo = $this->request->getPost('invonceNo');
        $cargoCount = $this->request->getPost('cargoCount');
        
        $order = $orderModels->c_one(['id' => $order_id, 'status !=' => '99']);
        $orderDetail = $orderModels->orderDetailAll(['order_id' => $order_id]);
        $delivery_address = $addressModels->c_one('order_address_clone', ['id' => $order->shipping_address]);

        $delivery_town = $addressModels->c_one('town', ['TownID' => $delivery_address->user_town], 'TownName ASC');
        $delivery_city = $addressModels->c_one('city', ['CityID' => $delivery_address->user_city], 'CityName ASC');
        $delivery_neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $delivery_address->user_neighborhood]);

        $billing_address = $addressModels->c_one('order_address_clone', ['id' => $order->billing_address]);

        $billing_town = $addressModels->c_one('town', ['TownID' => $billing_address->user_town], 'TownName ASC');
        $billing_city = $addressModels->c_one('city', ['CityID' => $billing_address->user_city], 'CityName ASC');
        $billing_neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $billing_address->user_neighborhood]);

        $user = $userModels->c_one(['id' => $order->user_id]);
        if (!$order) {
            $data['error'] = 'Düzenlemek istediğiniz sipariş bulunamadı.';
        }elseif (!$status) {
            $data['error'] = 'Lütfen siparişin durum bilgisini seçiniz.';
        }elseif ($status == '3' && (!$invonceNo)) {
            $data['error'] = 'Lütfen siparişiniz fatura numarasını giriniz.';
        }elseif ($status == '3' && (!$cargoCount)) {
            $data['error'] = 'Lütfen kargonun kaç parçadan oluştugunuzu giriniz.';
        }else{
            $url = base_url().'/api/orderCreateCargo/'.$order_id.'/'. $cargoCount .'/'.$invonceNo.'?apiKey=953E1C7C06494141B8DF4BBBDE76ED5E';
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Cookie: ci_session=ckn36kv4nae1rcl49ms4thqu3a7thide'
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $returnArray = json_decode($response, true);
            if ($returnArray['shippingOrderDetailVO']['errCode'] != '0') {
                $data['receiver_name'] = $delivery_address->receiver_name;
                $data['address'] = $delivery_address->address . ' ' . $delivery_neighborhood->NeighborhoodName . ' ' . $delivery_town->TownName . ' ' . $delivery_city->CityName;
                $data['phone'] = $delivery_address->phone;
                $data['email'] = $delivery_address->email;

                if (session()->get('admin')['role'] == '5') {
                    $data['sender_receiver_name'] = 'Biltstore E-Mağaza';
                    $data['sender_address'] = $contact['biltstore_address']->value;
                    $data['sender_phone'] = $contact['biltstore_tel']->value;
                    $data['sender_email'] = $contact['biltstore_email']->value;
                }else{
                    $store_id = session()->get('admin')['store_id'];
                    $storeFind = $userModels->c_one_shopping_centre(['id' => $store_id]);
                    $data['sender_receiver_name'] = 'Biltstore E-Mağaza';
                    $data['sender_address'] = $storeFind->address;
                    $data['sender_phone'] = $storeFind->phone;
                    $data['sender_email'] = $storeFind->email;
                }

                $data['order_type'] = 'Kredi/Banka Kartı Online';
                $data['cargo_company'] = 'Yurtiçi Kargo';
                $data['barcode_no'] = $returnArray['shippingOrderDetailVO']['cargoKey'];
                $generatorSVG = new BarcodeGeneratorSVG();
                $data['barcode'] = $generatorSVG->getBarcode($data['barcode_no'], $generatorSVG::TYPE_CODE_128);

                $updateOrderDetailData = [
                    'cargo_key' => $data['barcode_no'],
                    'invonce_no' => $invonceNo,
                    'cargo_count' => $cargoCount,
                    'status' => '3',
                    'updated_at' => created_at()
                ];

                if (session()->get('admin')['role'] == '5') {
                    $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['order_id' => $order->id, 'status !=' => '5', 'status !=' => '6', 'status !=' => '9'], $updateOrderDetailData);
                }else{
                    $updateOrderDetail = $orderModels->orderDetailNebimUpdateWhere(['order_id' => $order->id, 'store_id' => session()->get('admin')['store_id'], 'status !=' => '5', 'status !=' => '6', 'status !=' => '9'], $updateOrderDetailData);
                }

                $orderDetailCheack = $orderModels->orderDetailNebimAll(['order_id' => $order_id], '', ' status NOT IN ("3","5","9") ');

                if (!$orderDetailCheack) {
                    $updateOrderData = [
                        "status" => '3',
                        "updated_at" => created_at(),
                    ];
                    $insertOrder = $orderModels->edit($order_id, $updateOrderData);
                }

                $data['success'] = 'Kargo fişi başarılı bir şekilde oluşturuldu.';
            }elseif ($returnArray['shippingOrderDetailVO']['errCode'] == '60020') {
                $data['receiver_name'] = $delivery_address->receiver_name;
                $data['address'] = $delivery_address->address . ' ' . $delivery_neighborhood->NeighborhoodName . ' ' . $delivery_town->TownName . ' ' . $delivery_city->CityName;
                $data['phone'] = $delivery_address->phone;
                $data['email'] = $delivery_address->email;

                if (session()->get('admin')['role'] == '5') {
                    $data['sender_receiver_name'] = 'Biltstore E-Mağaza';
                    $data['sender_address'] = $contact['biltstore_address']->value;
                    $data['sender_phone'] = $contact['biltstore_tel']->value;
                    $data['sender_email'] = $contact['biltstore_email']->value;
                }else{
                    $store_id = session()->get('admin')['store_id'];
                    $storeFind = $userModels->c_one_shopping_centre(['id' => $store_id]);
                    $data['sender_receiver_name'] = 'Biltstore E-Mağaza';
                    $data['sender_address'] = $storeFind->address;
                    $data['sender_phone'] = $storeFind->phone;
                    $data['sender_email'] = $storeFind->email;
                }

                $data['order_type'] = 'Kredi/Banka Kartı Online';
                $data['cargo_company'] = 'Yurtiçi Kargo';
                $data['barcode_no'] = $returnArray['shippingOrderDetailVO']['cargoKey'];
                $generatorSVG = new BarcodeGeneratorSVG();
                $data['barcode'] = $generatorSVG->getBarcode($data['barcode_no'], $generatorSVG::TYPE_CODE_128);

                $data['success'] = 'Kargo fişi başarılı bir şekilde oluşturuldu.';

            }else{
                $data['error'] = 'Beklenmeyen bir hata oluştu ver kargo oluşturulamadı lütfen daha sonra tekrar deneyiniz.';
            }
        }

        return json_encode($data);
	}
    
    public function lastVisited()
	{
        $db = db_connect();
        $productCategoryModels = new ProductCategoryModels($db);
        $campaignModels = new CampaignModels($db);
        $category = new Category($db);
        $lastVisitedArray = $this->request->getPost('lastVisited');
        foreach ($lastVisitedArray AS $key => $variable) {
            $product_id = $variable['id'];
            $variant_id = $variable['variantID'];
            $row = $productCategoryModels->c_one(['p.id' => $product_id, 'p.is_active' => '1']); 
            if ($variant_id) {
                $productCombinationOne = $productCategoryModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.id' => $variant_id]);
            }else{
                $productCombinationOne = $productCategoryModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1']);
            }
            
            $productPicture = $productCategoryModels->attributePictureAll(['pap.product_attribute_id' =>$productCombinationOne->id], 2);
            if (!$productPicture) {
                $productPicture = $productCategoryModels->c_all_image(['product_id' => $row->id], 2);
            }
            $featureArray = explode(' - ', $row->attr);
            $attrubuteArray = explode(' - ', $productCombinationOne->attr_color);
            $attrubuteIDArray = explode(' - ', $productCombinationOne->attr_color_id);
            $attrubuteSizeArray = explode(' - ', $productCombinationOne->attr_size);
            $attrubuteSizeIDArray = explode(' - ', $productCombinationOne->attr_size_id);
            $categoryArray = explode(',', $row->category_id);
            $color_id = $attrubuteIDArray['0'];
            $color_title = $attrubuteArray['0'];
            $size_id = $attrubuteSizeIDArray['0'];
            $size_title = $attrubuteSizeArray['0'];
            foreach ($categoryArray as $item) {
                if ($item) {
                    $catFind = '';
                    $catFind = $category->c_all_list('0', $item);
                    if(!$catFind){
                        $endCatID = $item;
                    }
                }
            }
            $endCategoryFind = $category->c_one(["id" =>$endCatID]);
            $discountBool = FALSE;
            $discountRate = '0.00';
            if ($row->campaign_id) {
                $productCampaign = $campaignModels->c_one_index(['id' => $row->campaign_id, 'is_active' => '1']);
            }else{
                $productCampaign = '';
            }
           
            if ($productCombinationOne->sale_price) {
                $priceArray = priceAreaFunction($productCombinationOne->sale_price, $productCombinationOne->discount_price, $productCombinationOne->basket_price, $productCampaign->discount);
            }else{
                $priceArray = priceAreaFunction($row->sale_price, $row->discount_price, $row->basket_price, $productCampaign->discount);
            }
            
            $data['arr'][$key]['productID'] = $product_id;
            $data['arr'][$key]['variantID'] = $productCombinationOne->id;
            $data['arr'][$key]['variantBarcode'] = $productCombinationOne->barcode_no;
            $linkID = $productCombinationOne->barcode_no ? $productCombinationOne->barcode_no : $variant_id;
            $data['arr'][$key]['link'] = $row->slug .'-p-' . $linkID;
           
            if ($priceArray['discountBool']) {
                $data['arr'][$key]['discountBool'] = $priceArray['discountBool'];
            }
            if ($priceArray['discountRate']) {
                $data['arr'][$key]['discountRate'] = number_format($priceArray['discountRate'], 2);
            }
            if ($priceArray['totalPrice']) {
                $data['arr'][$key]['totalPrice'] = number_format(floor(($priceArray['totalPrice']*100))/100, 2);
            }
            if ($priceArray['discountPrice']) {
                $data['arr'][$key]['discountPrice'] = number_format(floor(($priceArray['discountPrice']*100))/100, 2);
            }
            if ($priceArray['basketBool']) {
                $data['arr'][$key]['basketBool'] = number_format($priceArray['basketBool'], 2);
            }
            if ($priceArray['basketPrice']) {
                $data['arr'][$key]['basketPrice'] = number_format($priceArray['basketPrice'], 2);
            }
            if ($priceArray['basketRate']) {
                $data['arr'][$key]['basketRate'] = number_format($priceArray['basketRate'], 1);
            }
            $data['arr'][$key]['brand'] = $row->b_title;
            if ($productPicture) {
                foreach ($productPicture as $k => $item) {
                    if (file_exists('uploads/products/min/'.$item->image.'') && $item->image){
                        $data['arr'][$key]['image'][$k] = base_url('/uploads/products/min/'.$item->image);
                    }else{
                        $data['arr'][$key]['image'][$k] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                    }
                }
            }else{
                $data['arr'][$key]['image']['0'] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                $data['arr'][$key]['image']['1'] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
            }
            if ($productCombinationOne->title) {
                $data['arr'][$key]['title'] = $productCombinationOne->title;
            }else{
                $data['arr'][$key]['title'] = $row->title;
            }
            

            if (isset($this->data['user_id'])) {
                $userModels = new UserModels($db);
                if ($productCombinationOne->id) {
                    $favoriteFind = $userModels->favorite_one (["user_id" => $this->data['user_id'] , "product_id" => $row->id, 'variant_id' => $productCombinationOne->id]);
                }else{
                    $favoriteFind = $userModels->favorite_one (["user_id" => $this->data['user_id'] , "product_id" => $row->id]);
                }
                if ($favoriteFind) {
                    $data['arr'][$key]['favorite'] = TRUE;
                }else{
                    $data['arr'][$key]['favorite'] = FALSE;
                }
            }else{
                $data['arr'][$key]['favorite'] = FALSE;
            }
            if (!$row) {
                unset($data['arr'][$key]);
            }
            if ($variant_id && !$productCategoryModels) {
                unset($data['arr'][$key]);
            }
        } 
        return json_encode($data);
	}

    public function desktopSearch()
	{
        $db = db_connect();
        $searchModels = new SearchModels($db);
        $productCategoryModels = new ProductCategoryModels($db);
        $campaignModels = new CampaignModels($db);
        $category = new Category($db);
        $searchKey = $this->request->getPost('searchKey');
        $search_array = [];
        $search_text_url = $searchKey;
        
        $search_text_url = str_replace('\'', '', $search_text_url );
        $search_text = urlutf_8($search_text_url) ;
        $search_text = str_replace('\'', '', $search_text );
        $search_text = clearHTML($search_text) ;
        $textArray = explode(' ', $search_text);
        $textArray = array_values(array_filter($textArray));
        $arrayCount = count($textArray);
        for ($i = $arrayCount; $i >= 1; $i--) {
            $combinations = string_combinations($textArray, $i , $i, ' ');
            $combinations = $combinations->withoutDuplicates();
            $search_array = array_merge($search_array, $combinations->asArray());
        }
        ////////////////////////// Brand Search Query //////////////////////////
            $whereTextBrand .= "(";
            foreach ($search_array as $row) {
                $whereTextBrand .= "(b.title LIKE '%{$row}%'
                ) OR ";
            }
            $orderWhereTextBrand .= 'CASE ';
            foreach ($search_array as $key => $row) {
                $orderWhereTextBrand .= "WHEN (b.title LIKE '%{$row}%') THEN $key ";
            }
            $whereTextBrand = rtrim($whereTextBrand, 'OR ');
            $orderWhereTextBrand .= "ELSE 999 END ASC";
            $whereTextBrand .= ")";
            $brand = $searchModels->searchBrand(['b.is_active' => '1'], $whereTextBrand, $orderWhereTextBrand, 3);
            if ($brand) {
                $data['data']['brand'] = $brand;
            }

        ////////////////////////// Category Search Query //////////////////////////
            $whereTextCategories .= "(";
            foreach ($search_array as $row) {
                $whereTextCategories .= "(c.title LIKE '%{$row}%'
                ) OR ";
            }
            $orderWhereTextCategories .= 'CASE ';
            foreach ($search_array as $key => $row) {
                $orderWhereTextCategories .= "WHEN (c.title LIKE '%{$row}%') THEN $key ";
            }
            $whereTextCategories = rtrim($whereTextCategories, 'OR ');
            $orderWhereTextCategories .= "ELSE 999 END ASC";
            $whereTextCategories .= ")";
            $categories = $searchModels->searchCategories(['c.is_active' => '1'], $whereTextCategories, $orderWhereTextCategories, 3);
            foreach ($categories as $key => $row) {
                $topCatArray = '';
                $topCatName = '';
                if ($row->parent_id == '0') {
                    $firstTopCat = $row->title;
                }
            
                if ($row->parent_id != 0) {
                    $category->veri = array();
                    $topCatArray = array_reverse($category->c_top_all_list('', $row->parent_id)); 
                    foreach ($topCatArray as $item) {
                        $topCatName .= $item['title'] . ' > ' ;
                    }
                }
                $data['data']['categories'][$key]['title'] = $topCatName . $row->title;
                $data['data']['categories'][$key]['slug'] = $row->slug;
                $data['data']['categories'][$key]['id'] = $row->id;
            }
        ////////////////////////// Camping Search Query //////////////////////////
            $whereTextCamping .= "(";
            foreach ($search_array as $row) {
                $whereTextCamping .= "(cp.title LIKE '%{$row}%'
                ) OR ";
            }
            $orderWhereTextCamping .= 'CASE ';
            foreach ($search_array as $key => $row) {
                $orderWhereTextCamping .= "WHEN (cp.title LIKE '%{$row}%') THEN $key ";
            }
            $whereTextCamping = rtrim($whereTextCamping, 'OR ');
            $orderWhereTextCamping .= "ELSE 999 END ASC";
            $whereTextCamping .= ")";
            $camping = $searchModels->searchCamping(['cp.is_active' => '1'], $whereTextCamping, $orderWhereTextCamping, 3);
            if ($camping) {
                $data['data']['camping'] = $camping;
            }







        ////////////////////////// Category Find Search Text ///////////////////
            foreach ($textArray as $row) {
                $firstCategoryFind = $searchModels->searchCategories(['c.is_active' => '1', 'c.parent_id' => '0'], 'c.title LIKE "%'. $row .'%"'); 
                foreach ($firstCategoryFind as $firstCategory) {
                    $firstCategoryIn .= '\''.$firstCategory->id.'\''.',';
                }
                $firstCategoryIn = rtrim($firstCategoryIn, ',');
                
            }
            if ($firstCategoryIn) {
                foreach ($textArray as $row) {
                    $secondCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$firstCategoryIn.') ');
                    foreach ($secondCategoryFind as $secondCategory) {
                        $secondCategoryIn .= '\''.$secondCategory->id.'\''.',';
                    }
                    $secondCategoryIn = rtrim($secondCategoryIn, ',');
                }
                if ($secondCategoryFind) {
                    foreach ($textArray as $row) {
                        $thereCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$secondCategoryIn.')');
                        foreach ($thereCategoryFind as $thereCategory) {
                            $thereCategoryIn .= '\''.$thereCategory->id.'\''.',';
                        }
                        $thereCategoryIn = rtrim($thereCategoryIn, ',');
                    }
                }else{
                    $secondCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'parent_id IN('.$firstCategoryIn.')'); 
                    foreach ($secondCategoryFind as $secondCategory) {
                        $secondCategoryIn .= '\''.$secondCategory->id.'\''.',';
                    }
                    $secondCategoryIn = rtrim($secondCategoryIn, ',');
                    foreach ($textArray as $row) {
                        $thereCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$secondCategoryIn.')');
                        foreach ($thereCategoryFind as $thereCategory) {
                            $thereCategoryIn .= '\''.$thereCategory->id.'\''.',';
                        }
                        $thereCategoryIn = rtrim($thereCategoryIn, ',');
                    }
                    
                }
            }else{
                foreach ($textArray as $row) {
                    $secondCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%"');
                    foreach ($secondCategoryFind as $secondCategory) {
                        $secondCategoryIn .= '\''.$secondCategory->id.'\''.',';
                    }
                    $secondCategoryIn = rtrim($secondCategoryIn, ',');
                }
                if ($secondCategoryFind) {
                    foreach ($textArray as $row) {
                        $thereCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$secondCategoryIn.')');
                        foreach ($thereCategoryFind as $thereCategory) {
                            $thereCategoryIn .= '\''.$thereCategory->id.'\''.',';
                        }
                        $thereCategoryIn = rtrim($thereCategoryIn, ',');
                    }
                    
                }else{
                    $secondCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], ''); 
                    foreach ($secondCategoryFind as $secondCategory) {
                        $secondCategoryIn .= '\''.$secondCategory->id.'\''.',';
                    }
                    $secondCategoryIn = rtrim($secondCategoryIn, ',');
                    foreach ($textArray as $row) {
                        $thereCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$secondCategoryIn.')');
                        foreach ($thereCategoryFind as $thereCategory) {
                            $thereCategoryIn .= '\''.$thereCategory->id.'\''.',';
                        }
                        $thereCategoryIn = rtrim($thereCategoryIn, ',');
                    }
                    
                }
                
            }
            if ($thereCategoryIn) {
                $thereCategoryIn = str_replace('\'\'', ',', $thereCategoryIn);
                $thereCategoryIn = str_replace('\'', '', $thereCategoryIn);
                $categoriesWhereIN = $thereCategoryIn;
            }else if ($secondCategoryIn){
                $secondCategoryIn = str_replace('\'\'', ',', $secondCategoryIn);
                $secondCategoryIn = str_replace('\'', '', $secondCategoryIn);
                $categoriesWhereIN = $secondCategoryIn;
            }else if ($firstCategoryIn){
                $firstCategoryIn = str_replace('\'\'', ',', $firstCategoryIn);
                $firstCategoryIn = str_replace('\'', '', $firstCategoryIn);
                $categoriesWhereIN = $firstCategoryIn;
            }
            if ($categoriesWhereIN) {
                $categoriesWhereArray = explode(',', $categoriesWhereIN);
                $categoriesWhere = ' ( ';
                foreach ($categoriesWhereArray as $row) {
                    $categoriesWhere .= 'FIND_IN_SET(\''.$row.'\' , p.category_id) OR ';
                }
                $categoriesWhere = rtrim($categoriesWhere, 'OR ');
                $categoriesWhere .= ') ';
            }
        ////////////////////////// Brand Search Query //////////////////////////
            $brandBool = FALSE;
            $i = count($textArray);
            foreach ($textArray as $row) {
                $brandFind = $searchModels->searchBrand(['b.is_active' => '1'], 'b.title LIKE "%'. $row .'%"');
                $textCount = count($row);
                if ($brandFind) {
                    foreach ($brandFind as $item) {
                        if (strpos($orderWhereText, "b.id = '{$item->id}'") === false) {
                            $textScore = $textCount *  $i * 10;
                            $brandBool = TRUE;
                            $orderWhereText .= ' ( CASE ';
                            $orderWhereText .= " WHEN (b.id = '{$item->id}') THEN '{$textScore}' ";
                            $orderWhereText .= " ELSE 0 END ) + ";
                        }
                    }
                }
            }
        ////////////////////////// Color Search Query //////////////////////////
            $colorBool = FALSE;
            $i = count($textArray);
            foreach ($textArray as $row) {
                $colorFind = $searchModels->searchColor(['a.is_active' => '1', 'a.attribute_group_id' => '5'], 'a.title LIKE "'. $row .'"');
                $textCount = count($row);
                if ($colorFind) {
                    foreach ($colorFind as $item) {
                        $textScore = $textCount *  $i * 10;
                        $colorBool = TRUE;
                        $orderWhereText .= ' ( CASE ';
                        $orderWhereText .= " WHEN (ac.id = '{$item->id}') THEN '{$textScore}' ";
                        $orderWhereText .= " ELSE 0 END ) + ";
                    }
                }
                $i--;
            }
            $orderWhereText = rtrim($orderWhereText, ' + ');
            if ($colorBool || $brandBool) {
                $orderWhereText .= " DESC";
            }
        ////////////////////////// Product Search Query //////////////////////////
            $whereTextProduct .= "(";
            foreach ($search_array as $row) {
                $whereTextProduct .= "(pa.title LIKE '%{$row}%'
                ) OR ";
            }
            $orderWhereTextProduct .= '(';
            $arrayCount = count($search_array);
            foreach ($search_array as $key => $row) {
                $orderWhereTextProduct .= ' ( CASE ';
                $orderWhereTextProduct .= " WHEN (pa.title LIKE '%{$row}%') THEN $arrayCount ";
                $orderWhereTextProduct .= " ELSE 0 END ) + ";
                $arrayCount--;
            }
            $whereTextProduct = rtrim($whereTextProduct, 'OR ');
            $orderWhereTextProduct = rtrim($orderWhereTextProduct, ' + ');
            $whereTextProduct .= ")";
            $orderWhereTextProduct .= ") DESC";
        ////////////////////////// Product Search Query //////////////////////////


        

        ////////////////////////// Category Find Search Text ///////////////////
            $firstCategoryIn = '';
            $secondCategoryIn = '';
            $thereCategoryIn = '';
            foreach ($textArray as $row) {
                $firstCategoryFind = $searchModels->searchCategories(['c.is_active' => '1', 'c.parent_id' => '0'], 'c.title LIKE "%'. $row .'%"'); 
                foreach ($firstCategoryFind as $firstCategory) {
                    $firstCategoryIn .= '\''.$firstCategory->id.'\''.',';
                }
                $firstCategoryIn = rtrim($firstCategoryIn, ',');
            }
            if ($firstCategoryIn) {
                foreach ($textArray as $row) {
                    $secondCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$firstCategoryIn.') ');
                    foreach ($secondCategoryFind as $secondCategory) {
                        $secondCategoryIn .= '\''.$secondCategory->id.'\''.',';
                    }
                    $secondCategoryIn = rtrim($secondCategoryIn, ',');
                }
                if ($secondCategoryFind) {
                    foreach ($textArray as $row) {
                        $thereCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$secondCategoryIn.')');
                        foreach ($thereCategoryFind as $thereCategory) {
                            $thereCategoryIn .= '\''.$thereCategory->id.'\''.',';
                        }
                        $thereCategoryIn = rtrim($thereCategoryIn, ',');
                    }
                }else{
                    $secondCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'parent_id IN('.$firstCategoryIn.')'); 
                    foreach ($secondCategoryFind as $secondCategory) {
                        $secondCategoryIn .= '\''.$secondCategory->id.'\''.',';
                    }
                    $secondCategoryIn = rtrim($secondCategoryIn, ',');
                    foreach ($textArray as $row) {
                        $thereCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$secondCategoryIn.')');
                        foreach ($thereCategoryFind as $thereCategory) {
                            $thereCategoryIn .= '\''.$thereCategory->id.'\''.',';
                        }
                        $thereCategoryIn = rtrim($thereCategoryIn, ',');
                    }
                    
                }
            }else{
                foreach ($textArray as $row) {
                    $secondCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%"');
                    foreach ($secondCategoryFind as $secondCategory) {
                        $secondCategoryIn .= '\''.$secondCategory->id.'\''.',';
                    }
                    $secondCategoryIn = rtrim($secondCategoryIn, ',');
                }
                if ($secondCategoryFind) {
                    foreach ($textArray as $row) {
                        $thereCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$secondCategoryIn.')');
                        foreach ($thereCategoryFind as $thereCategory) {
                            $thereCategoryIn .= '\''.$thereCategory->id.'\''.',';
                        }
                        $thereCategoryIn = rtrim($thereCategoryIn, ',');
                    }
                   
                }else{
                    $secondCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], ''); 
                    foreach ($secondCategoryFind as $secondCategory) {
                        $secondCategoryIn .= '\''.$secondCategory->id.'\''.',';
                    }
                    $secondCategoryIn = rtrim($secondCategoryIn, ',');
                    foreach ($textArray as $row) {
                        $thereCategoryFind = $searchModels->searchCategories(['c.is_active' => '1'], 'c.title LIKE "%'. $row .'%" AND c.parent_id IN('.$secondCategoryIn.')');
                        foreach ($thereCategoryFind as $thereCategory) {
                            $thereCategoryIn .= '\''.$thereCategory->id.'\''.',';
                        }
                        $thereCategoryIn = rtrim($thereCategoryIn, ',');
                    }
                    
                }
               
            }
            if ($thereCategoryIn) {
                $thereCategoryIn = str_replace('\'\'', ',', $thereCategoryIn);
                $thereCategoryIn = str_replace('\'', '', $thereCategoryIn);
                $categoriesWhereIN = $thereCategoryIn;
            }else if ($secondCategoryIn){
                $secondCategoryIn = str_replace('\'\'', ',', $secondCategoryIn);
                $secondCategoryIn = str_replace('\'', '', $secondCategoryIn);
                $categoriesWhereIN = $secondCategoryIn;
            }else if ($firstCategoryIn){
                $firstCategoryIn = str_replace('\'\'', ',', $firstCategoryIn);
                $firstCategoryIn = str_replace('\'', '', $firstCategoryIn);
                $categoriesWhereIN = $firstCategoryIn;
            }
            if ($categoriesWhereIN) {
                $categoriesWhereArray = explode(',', $categoriesWhereIN);
                $categoriesWhere = ' ( ';
                $categoriesWhereNew = ' ( ';
                foreach ($categoriesWhereArray as $row) {
                    $categoriesWhere .= 'FIND_IN_SET(\''.$row.'\' , category_id) OR ';
                    $categoriesWhereNew .= 'FIND_IN_SET(\''.$row.'\' , p.category_id) OR ';
                }
                $categoriesWhere = rtrim($categoriesWhere, 'OR ');
                $categoriesWhereNew = rtrim($categoriesWhereNew, 'OR ');
                $categoriesWhere .= ') ';
                $categoriesWhereNew .= ') ';
            }
        ////////////////////////// Brand Search Query //////////////////////////
            $orderWhereText = '';
            $orderWhereTextNew = '';
            $brandBool = FALSE;
            $i = count($textArray);
            foreach ($textArray as $row) {
                $brandFind = $searchModels->searchBrand(['b.is_active' => '1'], 'b.title LIKE "%'. $row .'%"');
                $textCount = count($row);
                if ($brandFind) {
                    foreach ($brandFind as $item) {
                        if (strpos($orderWhereText, "brand_id = '{$item->id}'") === false) {
                            $textScore = $textCount *  $i * 10;
                            $brandBool = TRUE;
                            $orderWhereText .= ' ( CASE ';
                            $orderWhereTextNew.= ' ( CASE ';
                            $orderWhereText .= " WHEN (brand_id = '{$item->id}') THEN '{$textScore}' ";
                            $orderWhereTextNew .= " WHEN (brand_id = '{$item->id}') THEN '{$textScore}' ";
                            $orderWhereText .= " ELSE 0 END ) + ";
                            $orderWhereTextNew .= " ELSE 0 END ) + ";
                        }
                    }
                }
            }
        ////////////////////////// Color Search Query //////////////////////////
            $colorBool = FALSE;
            $i = count($textArray);
            foreach ($textArray as $row) {
                $colorFind = $searchModels->searchColor(['a.is_active' => '2', 'a.attribute_group_id' => '5'], 'a.title LIKE "'. $row .'"');
                $textCount = count($row);
                if ($colorFind) {
                    foreach ($colorFind as $item) {
                        $textScore = $textCount *  $i * 10;
                        $colorBool = TRUE;
                        $orderWhereText .= ' ( CASE ';
                        $orderWhereTextNew .= ' ( CASE ';
                        $orderWhereText .= " WHEN (attr_color_id = '{$item->id}') THEN '{$textScore}' ";
                        $orderWhereTextNew .= " WHEN (attr_color_id = '{$item->id}') THEN '{$textScore}' ";
                        $orderWhereText .= " ELSE 0 END ) + ";
                        $orderWhereTextNew .= " ELSE 0 END ) + ";
                    }
                }
                $i--;
            }
            $orderWhereText = rtrim($orderWhereText, ' + ');
            $orderWhereTextNew = rtrim($orderWhereTextNew, ' + ');
      
        ////////////////////////// Product Search Query //////////////////////////
            $textBool .= FALSE;
            $whereTextProduct .= "(";
            $whereTextProductNew .= "(";
            foreach ($search_array as $row) {
                $whereTextProduct .= "(pa_title LIKE '%{$row}%' ) OR ";
                $whereTextProductNew .= "(b.title LIKE '%{$row}%' ) OR ";
            }
            $orderWhereText .= ' + (';
            $orderWhereTextNew .= ' + (';
            $arrayCount = count($search_array);
            foreach ($search_array as $key => $row) {
                $textBool = TRUE;
                $orderWhereText .= ' ( CASE ';
                $orderWhereTextNew .= ' ( CASE ';
                $orderWhereText .= " WHEN (title LIKE '%{$row}%') THEN $arrayCount ";
                $orderWhereTextNew .= " WHEN (category_count_table.title LIKE '%{$row}%') THEN $arrayCount ";
                $orderWhereText .= " ELSE 0 END ) + ";
                $orderWhereTextNew .= " ELSE 0 END ) + ";

                $orderWhereText .= ' ( CASE ';
                $orderWhereTextNew .= ' ( CASE ';
                $orderWhereText .= " WHEN (barcode_no LIKE '%{$row}%') THEN $arrayCount * 2 ";
                $orderWhereTextNew .= " WHEN (category_count_table.barcode_no LIKE '%{$row}%') THEN $arrayCount * 2 ";
                $orderWhereText .= " ELSE 0 END ) + ";
                $orderWhereTextNew .= " ELSE 0 END ) + ";

                $arrayCount--;
            }
            $whereTextProduct = rtrim($whereTextProduct, 'OR ');
            $whereTextProductNew = rtrim($whereTextProductNew, 'OR ');
            $orderWhereText = rtrim($orderWhereText, ' + ');
            $orderWhereText = ltrim($orderWhereText, ' + ');
            $orderWhereTextNew = rtrim($orderWhereTextNew, ' + ');
            $orderWhereTextNew = ltrim($orderWhereTextNew, ' + ');
            $whereTextProduct .= ")";
            $whereTextProductNew .= ")";
            $orderWhereText .= ") ";
            $orderWhereTextNew .= ") ";
            $orderBySelect = $orderWhereText;
            $orderBySelectNew = $orderWhereTextNew;
            if ($colorBool || $brandBool || $textBool) {
                $orderWhereText .= " DESC";
                $orderWhereTextNew .= " DESC";
            }
        ////////////////////////// Product Search Query //////////////////////////
      
        $product = $searchModels->searchProductNew(['is_active' => '1'], ''.$categoriesWhere.'', $orderWhereText, 4, $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn, $orderBySelect);
        if ($product) {
            foreach ($product as $key => $variable) {
                $product_id = $variable->id;
                $variant_id = $variable->pa_id;
                $row = $productCategoryModels->c_one(['p.id' => $product_id, 'p.is_active']); 
                if ($variant_id) {
                    $productCombinationOne = $productCategoryModels->productCombinationOne(['pa.id_product' => $product_id, 'pa.is_active' => '1', 'pa.id' => $variant_id]);
                }else{
                    $productCombinationOne = $productCategoryModels->productCombinationOne(['pa.id_product' => $product_id, 'pa.is_active' => '1']);
                }
                
                $productPicture = $productCategoryModels->attributePictureAll(['pap.product_attribute_id' =>$productCombinationOne->id], 2);
                if (!$productPicture) {
                    $productPicture = $productCategoryModels->c_all_image(['product_id' => $row->id], 2);
                }
                $featureArray = explode(' - ', $row->attr);
                $attrubuteArray = explode(' - ', $productCombinationOne->attr_color);
                $attrubuteIDArray = explode(' - ', $productCombinationOne->attr_color_id);
                $attrubuteSizeArray = explode(' - ', $productCombinationOne->attr_size);
                $attrubuteSizeIDArray = explode(' - ', $productCombinationOne->attr_size_id);
                $categoryArray = explode(',', $row->category_id);
                $color_id = $attrubuteIDArray['0'];
                $color_title = $attrubuteArray['0'];
                $size_id = $attrubuteSizeIDArray['0'];
                $size_title = $attrubuteSizeArray['0'];
                foreach ($categoryArray as $item) {
                    $catFind = '';
                    if ($item) {
                        $catFind = $category->c_all_list('', $item);
                        if(!$catFind){
                            $endCatID = $item;
                        }
                    }
                }
                $endCategoryFind = $category->c_one(["id" =>$endCatID]);
                $discountBool = FALSE;
                $discountRate = '0.00';
                if ($row->campaign_id) {
                    $productCampaign = $campaignModels->c_one_index(['id' => $row->campaign_id, 'is_active' => '1']);
                }else{
                    $productCampaign = '';
                }
                if ($productCombinationOne->sale_price) {
                    $priceArray = priceAreaFunction($productCombinationOne->sale_price, $productCombinationOne->discount_price, $productCombinationOne->basket_price, $productCampaign->discount);
                }else{
                    $priceArray = priceAreaFunction($row->sale_price, $row->discount_price, $row->basket_price, $productCampaign->discount);
                }
                
                $data['arr'][$key]['productID'] = $product_id;
                $data['arr'][$key]['variantID'] = $productCombinationOne->id;
                $data['arr'][$key]['variantBarcode'] = $productCombinationOne->barcode_no;
                $linkID = $productCombinationOne->barcode_no ? $productCombinationOne->barcode_no : $product_id;
                $data['arr'][$key]['link'] = $row->slug .'-p-' . $linkID;
                
                if ($priceArray['discountBool']) {
                    $data['arr'][$key]['discountBool'] = $priceArray['discountBool'];
                }
                if ($priceArray['discountRate']) {
                    $data['arr'][$key]['discountRate'] = number_format($priceArray['discountRate'], 2);
                }
                if ($priceArray['totalPrice']) {
                    $data['arr'][$key]['totalPrice'] = number_format(floor(($priceArray['totalPrice']*100))/100, 2);
                }
                if ($priceArray['discountPrice']) {
                    $data['arr'][$key]['discountPrice'] = number_format(floor(($priceArray['discountPrice']*100))/100, 2);
                }
                if ($priceArray['basketBool']) {
                    $data['arr'][$key]['basketBool'] = number_format($priceArray['basketBool'], 2);
                }
                if ($priceArray['basketPrice']) {
                    $data['arr'][$key]['basketPrice'] = number_format($priceArray['basketPrice'], 2);
                }
                if ($priceArray['basketRate']) {
                    $data['arr'][$key]['basketRate'] = number_format($priceArray['basketRate'], 1);
                }

                $data['arr'][$key]['brand'] = $row->b_title;
                if ($productPicture) {
                    foreach ($productPicture as $k => $item) {
                        if (file_exists('uploads/products/min/'.$item->image.'') && $item->image){
                            $data['arr'][$key]['image'][$k] = base_url('/uploads/products/min/'.$item->image);
                        }else{
                            $data['arr'][$key]['image'][$k] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                        }
                    }
                }else{
                    $data['arr'][$key]['image']['0'] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                    $data['arr'][$key]['image']['1'] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                }
                if ($productCombinationOne) {
                    $data['arr'][$key]['title'] = $productCombinationOne->title;
                }else{
                    $data['arr'][$key]['title'] = $row->title;
                }

                if (isset($this->data['user_id'])) {
                    $userModels = new UserModels($db);
                    if ($productCombinationOne->id) {
                        $favoriteFind = $userModels->favorite_one (["user_id" => $this->data['user_id'] , "product_id" => $row->id, 'variant_id' => $productCombinationOne->id]);
                    }else{
                        $favoriteFind = $userModels->favorite_one (["user_id" => $this->data['user_id'] , "product_id" => $row->id]);
                    }
                    if ($favoriteFind) {
                        $data['arr'][$key]['favorite'] = TRUE;
                    }else{
                        $data['arr'][$key]['favorite'] = FALSE;
                    }
                }else{
                    $data['arr'][$key]['favorite'] = FALSE;
                }
            }
        }else{
            $data['arr'] = FALSE;
        }
        if (!$data['data']) {
            $data['data'] = FALSE;
        }
        return json_encode($data);
	}
    
    public function productGetVariantOption () {
        $db = db_connect();
        $attributeGroupModels = new AttributeGroupModels($db);
        $attributeModels = new AttributeModels($db);
        $id = $this->request->getPost('id');
        $query = $this->request->getPost('query');
        $inSelect = $this->request->getPost('inSelect');
        $attributeGroupFind =  $attributeGroupModels->c_one(['id' => $id]);
        if ($query) {
            $attributeFind = $attributeModels->c_all(['a.attribute_group_id' => $id, 'a.is_active' => '2'], 'a.title LIKE "%'. $query.'%" ');
        }else{
            $attributeFind = $attributeModels->c_all(['a.attribute_group_id' => $id, 'a.is_active' => '2']);
        }
       
        foreach ($attributeFind as $key => $row) {
            $data['results'][$key]['text'] = $row->title;
            $data['results'][$key]['id'] = '{"id":"'.$row->id.'","text":"'.$row->title.'","group_title":"'. $attributeGroupFind->title .'","group_id":"'. $attributeGroupFind->id .'","value":"'. $attributeGroupFind->title .' : '.$row->title.'"}';
            if (in_array($row->id, $inSelect)) {
                $data['results'][$key]['selected'] = true;
            }
        }
        return json_encode($data);
    }

    public function filter_add() {
		$filitre = '';
		if ($_POST) {
			$filitre .= '?filter=';
		}
		$link = '';
		foreach ($_POST as $key => $value) {
			if ($value['0'] || $value['1']) {
				$link .= $key.':';
				foreach ($_POST[$key] as $data) {
					if (!$data) {
						$data = 0 ;
					}
					$link .= $data.',';
				}
				$link = rtrim($link, ',');
				$link = $link . ';';
			}
		}
		$linkPar =  $filitre . '' . $link;
		$lastLink = rtrim($linkPar , ';');
		$dizi['link'] = $lastLink;
		return json_encode($dizi);
	}
}
    
    
