<?php 

    namespace App\Controllers\Api;
    
    use App\Controllers\BaseController;
    use App\Models\ProductModels;
    use App\Models\SettingModels;
    use App\Models\Category;
    use App\Models\AttributeModels;
    use App\Models\ProductFeatureModels;
    use App\Models\ProductDetailModels;
    use App\Models\AttributeGroupModels;
    use App\Models\BrandModels;
    use App\Models\UserModels;
    use App\Models\AddressModels;
    use App\Models\OrderModels;

    class Mubiko extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function nebimProductInsert()
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $productModels = new ProductModels($db);
            $category = new Category($db);
            $attributeModels = new AttributeModels($db);
            $brandModels = new BrandModels($db);
            $productFeatureModels = new ProductFeatureModels($db);
            $settingModels = new SettingModels($db);
            $contactSetting = $settingModels->c_all(['type' => 'general']);
            $general = namedSettings($contactSetting);
            $data['productModels'] = $productModels;
            $data['GetProductsNebim'] = GetProductsNebim();
            $productArray = $productModels->c_all_mubiko(['p.is_active' => '1', 'pa.is_active' => '1', 'b.is_mubiko' => '1'], $general['mubiko_sezon']->value);
            foreach ($productArray as $key => $item) {
                $mubikoProduct[$key]['Barcode'] = $item->barcode_no;
                $mubikoProduct[$key]['Envanter'] = $item->stock;
            }
            
            $mubikoProductJson = json_encode($mubikoProduct);
            
            return $mubikoProductJson;
        }

        public function orderInsertMubiko()
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $productFeatureModels = new ProductFeatureModels($db);
            $productDetailModels = new ProductDetailModels($db);
            $userModels = new UserModels($db);
            $orderModels = new OrderModels($db);
            $addressModels = new AddressModels($db);

            $data = $_GET['data'];
            orderNoReturn:
			$hash = crc32(sha1(base64_encode(md5(base64_encode(created_at())))));
			if (strlen(date('n')) == '1' ) {
				$order_no = date('y') . date('n') . substr($hash, 1, 8);
			}else{
				$order_no = date('y') . date('n') . substr($hash, 1, 7); 
			} 
			$orderNoFind = $orderModels->c_one(['order_no' => $order_no]);
			if ($orderNoFind) {
				goto orderNoReturn;
			}
            $userFind = $userModels->c_one(['id' => '52']);
            $userAddressCheack = $addressModels->c_one('user_address', ['id' => '354']);
            $insertUserAddressDate = [
				'user_id' => $userFind->id,
				'nebim_PostalAddressID' => $userAddressCheack->nebim_PostalAddressID,
				'title' => $userAddressCheack->title,
				'receiver_name' => $userAddressCheack->receiver_name,
				'user_town' => $userAddressCheack->user_town,
				'user_city' => $userAddressCheack->user_city,
				'user_neighborhood' => $userAddressCheack->user_neighborhood,
				'address' => $userAddressCheack->address,
				'phone' => $userAddressCheack->phone,
				'email' => $userAddressCheack->email,
				'created_at' => created_at()
			];
			$insertUserAddress = $addressModels->add('order_address_clone', $insertUserAddressDate);
            $userAddressID = $db->insertID();

            $insertOrderData = [
                "user_id" => $userFind->id,
                "order_no" => $order_no,
                "shipping_address" => $userAddressID,
                "billing_address" => $userAddressID,
                "status" => '1',
                "created_at" => created_at(),
            ];
            $insertOrder = $orderModels->add($insertOrderData);
            $orderID = $db->insertID();

            $dataObj = json_decode($data);
            foreach ($dataObj as $item) {
                $thisProductFind = $productDetailModels->c_one(['pa.barcode_no' => $item->Barcode ]);
                $thisVariantFind = $productDetailModels->productCombinationOne(['pa.id_product' => $thisProductFind->id, 'pa.id' => $thisProductFind->pa_id]);
                //////////////////// Product Price Select Area Start ///////////////////
				$last_price = '';
                if ($thisVariantFind->sale_price) {
                    if ($thisVariantFind->basket_price) {
                        $last_price = $thisVariantFind->basket_price;
                    }elseif ($thisVariantFind->discount_price) {
                        $last_price = $thisVariantFind->discount_price;
                    }elseif ($thisVariantFind->sale_price){
                        $last_price = $thisVariantFind->sale_price;
                    }
                }else{
                    if ($thisProductFind->basket_price) {
                        $last_price = $thisProductFind->basket_price;
                    }elseif ($thisProductFind->discount_price) {
                        $last_price = $thisProductFind->discount_price;
                    }elseif ($thisProductFind->sale_price){
                        $last_price = $thisProductFind->sale_price;
                    }
                }
				

                $attrubuteIDArray = explode(' - ', $thisVariantFind->attr_color_id);
                $color_id = $attrubuteIDArray['0'];

                $attrubuteSizeIDArray = explode(' - ', $thisVariantFind->attr_size_id);
                $size_id = $attrubuteSizeIDArray['0'];
			    /////////////////// Product Price Select Area End ///////////////////
                
                if ($thisProductFind) {
                    $orderDetailData = [
                        'order_id' => $orderID,
                        'product_order_no' => $order_no,
                        'user_id' => $userFind->id,
                        'product_id' => $thisProductFind->id,
                        'variant_id' => $thisProductFind->pa_id,
                        'variant_barcode' => $thisProductFind->barcode_no,
                        'vat_rate' => $thisProductFind->tax_rate,
                        'price' => $last_price * $item->Piece,
                        'piece' => $item->Piece,
                        'color_id' => $color_id,
                        'size_id' => $size_id,
                    ];
                    $insertOrderDetail = $orderModels->orderDetailAdd($orderDetailData);
                    $headerBasketPriceSesion += $row['header_basket_price'] * $item->Piece;
                    $overall_total += vat_deducted(($last_price * $item->Envanter), $thisProductFind->tax_rate);
                    $vat_price += ($last_price * $item->Piece) - vat_deducted(($last_price * $item->Piece), $thisProductFind->tax_rate);
                    $basketTotalPrice += $last_price * $item->Piece;
                }
            }

            $updateOrderData = [
                "overall_total" => $overall_total,
                "vat_price" => $vat_price,
                "total_price" => $basketTotalPrice,
                "updated_at" => created_at(),
            ];
            $insertOrder = $orderModels->edit($orderID, $updateOrderData);

            $updateOrderAddressData = [
                "order_id" => $orderID,
                "order_no" => $order_no,
                "updated_at" => created_at(),
            ];
            $insertOrder = $addressModels->edit('order_address_clone', ['id' => $userAddressID], $updateOrderAddressData);
            
            $orderFind = $orderModels->c_one(['id' => $orderID]);
            $orderDetailFind = $orderModels->orderDetailAll(['order_id' => $orderID]);
            $userFind = $userModels->c_one(['id' => '52']);
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
                    "SalespersonCode" => "MUBIKO",
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
                $updateOrder = $orderModels->orderDetailNebimAdd($updateOrderDetailDate);
            }
            $nebimCustomerData['Lines'][]  = [
                "ItemTypeCode" => "5",
                "ItemCode" => "KRG",
                "Qty1" => "1",
                "PriceVI" => floatval(0),
            ];
            $response = $client->request('GET', 'http://193.111.73.193:88/IntegratorService/POST/7E299A342CAD4C448BD8E6EBEAFF3F14?'. json_encode($nebimCustomerData).'', ['connect_timeout' => 0]);
            $returJson = json_decode($response->getBody());
            $nebim_OrderNumber = $returJson->OrderNumber;
            $updateOrderDate = [
                "nebim_OrderNumber" => $nebim_OrderNumber,
                "updated_at" => created_at()
            ];
            $updateOrder = $orderModels->edit($orderID, $updateOrderDate);
            print_r($response->getBody());
        }
          
    }
