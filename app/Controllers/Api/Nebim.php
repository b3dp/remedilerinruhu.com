<?php 

    namespace App\Controllers\Api;
    
    use App\Controllers\BaseController;
    use App\Controllers\SendMail;
    use App\Models\ProductModels;
    use App\Models\Category;
    use App\Models\AttributeModels;
    use App\Models\ProductFeatureModels;
    use App\Models\AttributeGroupModels;
    use App\Models\BrandModels;
    use App\Models\UserModels;
    use App\Models\AddressModels;
    use App\Models\OrderModels;

    class Nebim extends BaseController
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
            $data['productModels'] = $productModels;
            $data['GetProductsNebim'] = GetProductsNebim();
            $productArray = json_decode($data['GetProductsNebim'], true);
                $productColorArray = array();
                $productSizeArray = array();
                $productBrandArray = array();
                $newColorMail = FALSE;

                foreach ($productArray as $row) {
                    if (!in_array($row['ColorCode'], $productColorArray)) {
                        $productColorArray[] = $row['ColorCode'];
                    }
                }
                foreach ($productColorArray as $key => $row) {
                    $title = str_replace('_', '-', $row) ;
                    $slug = sef_link($title);
                    $is_active = '1';
                    $dataInsert = [
                        "attribute_group_id" => '5',
                        "title" => $title,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "rank" => $key,
                        "created_at" => created_at(),
                    ];
                    $attributeFind = $attributeModels->c_one(['a.attribute_group_id' => 5, 'a.slug' => $slug]);
                    if (!$attributeFind) {
                        $attributeModels->add($dataInsert);
                    }
                }
                foreach ($productArray as $row) {
                    if (!in_array($row['ItemDim1Code'], $productSizeArray)) {
                        $productSizeArray[] = $row['ItemDim1Code'];
                    }
                }
                foreach ($productSizeArray as $key => $row) {
                    $title = str_replace('_', '-', $row) ;
                    $slug = sef_link($title);
                    $is_active = '1';
                    $dataInsert = [
                        "attribute_group_id" => '4',
                        "title" => $title,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "rank" => $key,
                        "created_at" => created_at(),
                    ];
                    $attributeFind = $attributeModels->c_one(['a.attribute_group_id' => 4, 'a.slug' => $slug]);
                    if (!$attributeFind) {
                        $attributeModels->add($dataInsert);
                    }
                }
                foreach ($productArray as $row) {
                    if (!in_array($row['ProductAtt02'], $productBrandArray)) {
                        $productBrandArray[] = $row['ProductAtt02'];
                    }
                }
          
                foreach ($productBrandArray as $key => $row) {
                    $title = str_replace('_', '-', $row) ;
                    $slug = sef_link($title);
                    $is_active = '1';
                    $dataInsert = [
                        "title" => $title,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "rank" => $key,
                        "created_at" => created_at(),
                    ];
                    $productBrandFind = $brandModels->c_one(["slug" => $slug]);
                    if (!$productBrandFind) {
                        $brandModels->add($dataInsert);    
                    }
                }
                $rand = rand(0,999999999);
                foreach ($productArray as $key => $row) {

                    $productCategoryFirst = $row['ProductHierarchyLevel02'];
                    $productCategoryFirstSlug = sef_link($row['ProductHierarchyLevel02']);
                    $productCategorySecend = $row['ProductHierarchyLevel01'];
                    $productCategorySecendSlug = sef_link($row['ProductHierarchyLevel01']);
                    $productCategoryTree = $row['ProductHierarchyLevel03'];
                    $productCategoryTreeSlug = sef_link($row['ProductHierarchyLevel03']);
                    $categoryFirst = $category->c_one(["slug" => $productCategoryFirstSlug]);
                    if (!$categoryFirst && $productCategoryFirst) {
                        $dataInsertFirst = [
                            "title" => $productCategoryFirst,
                            "parent_id" => '0',
                            "slug" => $productCategoryFirstSlug,
                            "is_active" => '1',
                            "rank" => $key,
                            "created_at" => created_at(),
                        ];
                        $insertFirstCategory =  $category->add($dataInsertFirst);
                        $insertFirstCategoryID = $db->insertID();
                    }else{
                        $insertFirstCategoryID = $categoryFirst->id;
                    }

                    $categorySecend = $category->c_one(["slug" => $productCategorySecendSlug, "parent_id" => $insertFirstCategoryID]);
                    if (!$categorySecend && $productCategorySecend) {
                        $dataInsertSecend = [
                            "title" => $productCategorySecend,
                            "parent_id" => $insertFirstCategoryID,
                            "slug" => $productCategorySecendSlug,
                            "is_active" => '1',
                            "rank" => $key,
                            "created_at" => created_at(),
                        ];
                        $insertSecendCategory =  $category->add($dataInsertSecend);
                        $insertSecendCategoryID = $db->insertID();
                    }else{
                        $insertSecendCategoryID = $categorySecend->id;
                    }

                    $categoryTree = $category->c_one(["slug" => $productCategoryTreeSlug, "parent_id" => $insertSecendCategoryID]);
                    if (!$categoryTree && $productCategoryTree) {
                        $dataInsertTree = [
                            "title" => $productCategoryTree,
                            "parent_id" => $insertSecendCategoryID,
                            "slug" => $productCategoryTreeSlug,
                            "is_active" => '1',
                            "rank" => $key,
                            "created_at" => created_at(),
                        ];
                        $insertTreeCategory =  $category->add($dataInsertTree);
                        $insertTreeCategoryID = $db->insertID();
                    }else{
                        $insertTreeCategoryID = $categoryTree->id;
                    }
                    $productBrand = sef_link($row['ProductAtt02']);
                    $productBrandFind = $brandModels->c_one(["slug" => $productBrand]);
                    if (!is_numeric($row['ItemCode'])) {
                        $productItemCode = str_replace('.', '', $row['ItemCode']);
                    }else{
                        $productItemCode = str_replace('.', '', $row['ItemCode']);
                    }
                    $productItemTitle = $row['ProductAtt02'] .' '. $row['ItemCode'] .' '. $row['ProductAtt03'] . ' ' .  $row['ProductHierarchyLevel02'] . ' ' . $row['ProductHierarchyLevel03'];
                    $productItemSlug = sef_link($productItemTitle);
                    $productItemTaxRate = str_replace('%', '', $row['ItemTaxGrCode']);
                    $productItemSeason = str_replace('SS', '', $row['ProductAtt03Desc']);
                    $productItemSeason = str_replace('SEZONSUZ', '', $productItemSeason);
                    $productItemSeason = str_replace('FW', '', $productItemSeason);
                    $productItemSalePrice = $row['RetailSalePrice'];
                    $productItemCategoryId = $insertFirstCategoryID.','. $insertSecendCategoryID.','. $insertTreeCategoryID;
                    $productItemBrandId = $productBrandFind->id;
                    $productFind = $productModels->c_one(["ItemCode" => $productItemCode]);
                    if ($productItemCategoryId != ',,' && $row['Barcode'] != '') {
                        if (!$productFind) {
                            $insertProductData = [
                                "ItemCode" => $productItemCode,
                                "category_id" => $productItemCategoryId,
                                "brand_id" => $productItemBrandId,
                                "tax_rate" => $productItemTaxRate,
                                "season" => $productItemSeason,
                                "sale_price" => $productItemSalePrice,
                                "title" => $productItemTitle,
                                "slug" => $productItemSlug,
                                "is_active" => '1',
                                "rank" => $key,
                                "created_at" => created_at()
                            ];
                            $insertProduct = $productModels->add($insertProductData);
                            $insertProductID = $db->insertID();
                        }else{
                            $insertProductData = [
                                "ItemCode" => $productItemCode,
                                "category_id" => $productItemCategoryId,
                                "brand_id" => $productItemBrandId,
                                "tax_rate" => $productItemTaxRate,
                                "season" => $productItemSeason,
                                "sale_price" => $productItemSalePrice,
                                "title" => $productItemTitle,
                                "slug" => $productItemSlug,
                                "rank" => $key,
                                "updated_at" => created_at()
                            ];
                            $insertProduct = $productModels->edit($productFind->id, $insertProductData);
                            $insertProductID = $productFind->id;
                        }
                        if ($insertProductID && $row['Barcode'] != '') {
                            $variantFind = $productModels->productCombinationOne(["barcode_no" => $row['Barcode'], 'id_product' => $insertProductID]);
                            if (!$variantFind) {
                                
                                $attrColorFind = $attributeModels->c_one(["a.slug" => sef_link($row['ColorCode']), "attribute_group_id" => '5']);
                                $attrColorChangeFind = $attributeModels->attribute_change_c_one(["nebim_id" => $attrColorFind->id ]);
                                if ($attrColorChangeFind) {
                                    $attrColorFind = $attributeModels->c_one(["a.id" => $attrColorChangeFind->biltstore_id ]);
                                }else {
                                    $newColorMail = TRUE;
                                }
                                $productItemAttrTitle = $row['ProductAtt02']. ' ' . $attrColorFind->title . ' ' .  $row['ProductHierarchyLevel02'] . ' ' . $row['ProductHierarchyLevel03'] . ' ' . $row['ItemCode'];
                                $insertVariantData = [
                                    "id_product" => $insertProductID,
                                    "barcode_no" => $row['Barcode'],
                                    "stock" => $row['Envanter'],
                                    "is_active" => '1',
                                    "title" => $productItemAttrTitle,
                                    "rand" => $rand,
                                    "created_at" => created_at()
                                ];
                                $productModels->productAttrInsert($insertVariantData);
                                $insertVariantID = $db->insertID();
                                if ($insertVariantID) {
                                   
                                    $attrSizeFind = $attributeModels->c_one(["a.slug" => sef_link($row['ItemDim1Code']), "attribute_group_id" => '4']);
                                    if ($attrColorFind) {
                                        $insertVariantCombinationColorData = [
                                            "attribute_product_id" => $insertVariantID,
                                            "attribute_id" => $attrColorFind->id
                                        ];
                                        $productModels->productattributecombination($insertVariantCombinationColorData);
    
                                        $productFeatureData = [
                                                "product_id" => $insertProductID,
                                                "product_attribute_id" => $insertVariantID,
                                                "attribute_group_id" => '5',
                                                "attribute_id" => $attrColorFind->id
                                        ];
                                        $productFeatureModels->add($productFeatureData);
                                    }
                                
                                    if ($attrSizeFind) {
                                        $insertVariantCombinationSizeData = [
                                            "attribute_product_id" => $insertVariantID,
                                            "attribute_id" => $attrSizeFind->id
                                        ];
                                    
                                        $productModels->productattributecombination($insertVariantCombinationSizeData);
                                        $productFeatureData = [
                                                "product_id" => $insertProductID,
                                                "product_attribute_id" => $insertVariantID,
                                                "attribute_group_id" => '4',
                                                "attribute_id" => $attrSizeFind->id
                                        ];
                                        $productFeatureModels->add($productFeatureData);
                                    }
    
                                    $shoppingCenterFind = $productModels->shoppingCenterAll();
                                    foreach ($shoppingCenterFind as $item) {
                                        $productShoppingCenterStock = $row[$item->title_nebim];
                                        if ($productShoppingCenterStock) {
                                            $productShoppingCentreData = [
                                                'product_id' => $insertProductID,
                                                'variant_id' => $insertVariantID,
                                                'shopping_centre_id' => $item->id,
                                                'stock' => $productShoppingCenterStock,
                                                'created_at' => created_at()
                                            ];
                                            $insertProductShoppingCenter = $productModels->prodcutShoppingCenterAdd($productShoppingCentreData);
                                        } 
                                    }
                                }
                            }else{
                            
                                $attrColorFind = $attributeModels->c_one(["a.slug" => sef_link($row['ColorCode']), "attribute_group_id" => '5']);
                                $attrColorChangeFind = $attributeModels->attribute_change_c_one(["nebim_id" => $attrColorFind->id ]);
                                if ($attrColorChangeFind) {
                                    $attrColorFind = $attributeModels->c_one(["a.id" => $attrColorChangeFind->biltstore_id ]);
                                }else {
                                    $newColorMail = TRUE;
                                }
                                $productItemAttrTitle = $row['ProductAtt02']. ' ' . $attrColorFind->title . ' ' .  $row['ProductHierarchyLevel02'] . ' ' . $row['ProductHierarchyLevel03'] . ' ' . $row['ItemCode'];
                                $insertVariantData = [
                                    "stock" => $row['Envanter'],
                                    "reference" => '1',
                                    "title" => $productItemAttrTitle,
                                    "sale_price" => $productItemSalePrice,
                                    "rand" => $rand,
                                    "updated_at" => created_at()
                                ];
                                $updateAttr = $productModels->productAttrUpdate(['id' => $variantFind->id], $insertVariantData);

                                if ($updateAttr) {
                                    $insertVariantID = $variantFind->id;
                                    $attrSizeFind = $attributeModels->c_one(["a.slug" => str_replace('_', '-', sef_link($row['ItemDim1Code'])), "attribute_group_id" => '4']);
                                    if ($attrColorFind) {
                                        $productattributecombination_c_one = $productModels->productattributecombination_c_one(['attribute_product_id' => $insertVariantID, "attribute_id" => $attrColorFind->id ]);
                                   
                                        if (!$productattributecombination_c_one) {
                                            $insertVariantCombinationColorData = [
                                                "attribute_product_id" => $insertVariantID,
                                                "attribute_id" => $attrColorFind->id
                                            ];
                                           
                                            $productModels->productattributecombination($insertVariantCombinationColorData);

                                            $productFeatureData = [
                                                "product_id" => $insertProductID,
                                                "product_attribute_id" => $insertVariantID,
                                                "attribute_group_id" => '5',
                                                "attribute_id" => $attrColorFind->id
                                            ];
                                            $productFeatureModels->add($productFeatureData);
                                        }
                                    }
                                
                                    if ($attrSizeFind) {
                                        $productattributecombination_c_one_size = $productModels->productattributecombination_c_one(['attribute_product_id' => $insertVariantID, "attribute_id" => $attrSizeFind->id ]);
                                        if (!$productattributecombination_c_one_size) {
                                            $insertVariantCombinationSizeData = [
                                                "attribute_product_id" => $insertVariantID,
                                                "attribute_id" => $attrSizeFind->id
                                            ];
                                        
                                            $productModels->productattributecombination($insertVariantCombinationSizeData);
                                            $productFeatureData = [
                                                    "product_id" => $insertProductID,
                                                    "product_attribute_id" => $insertVariantID,
                                                    "attribute_group_id" => '4',
                                                    "attribute_id" => $attrSizeFind->id
                                            ];
                                            $productFeatureModels->add($productFeatureData);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $insertVariantData = [
                    "stock" => '0',
                    "is_active" => '0',
                    "updated_at" => created_at()
                ];
                $updateAttr = $productModels->productAttrUpdate2($rand, $insertVariantData);
                if ($newColorMail) {
                    $sendMail = new SendMail();
                    $mailContent = '
                       Biltstore.com Yeni ürünler ve tanımlanamayan renkler mevcuttur.
                    ';
                    $registerMail = $sendMail->SendMail('', "serkan@minisoft.com.tr", "", "Yeni Renkler", $mailContent);
                }

                $url = base_url().'/api/searchProductInsert?apiKey=953E1C7C06494141B8DF4BBBDE76ED5E';
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

            return view ("admin/product/nebim", $data);
        }

        public function nebimProductView()
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $productModels = new ProductModels($db);
            $category = new Category($db);
            $attributeModels = new AttributeModels($db);
            $brandModels = new BrandModels($db);
            $productFeatureModels = new ProductFeatureModels($db);
            $data['productModels'] = $productModels;
            $data['GetProductsNebim'] = GetProductsNebim();
            print_r($data['GetProductsNebim']);
            $productArray = json_decode($data['GetProductsNebim'], true);
            return view ("admin/product/nebim", $data);
        }

        public function nebimProductStockUpdate()
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $productModels = new ProductModels($db);
            $category = new Category($db);
            $attributeModels = new AttributeModels($db);
            $brandModels = new BrandModels($db);
            $productFeatureModels = new ProductFeatureModels($db);
            $data['productModels'] = $productModels;
            $data['GetProductsNebim'] = GetProductsNebim();
            $productArray = json_decode($data['GetProductsNebim'], true);

                foreach ($productArray as $key => $row) {

                    $productCategoryFirst = $row['ProductHierarchyLevel02'];
                    $productCategoryFirstSlug = sef_link($row['ProductHierarchyLevel02']);
                    $productCategorySecend = $row['ProductHierarchyLevel01'];
                    $productCategorySecendSlug = sef_link($row['ProductHierarchyLevel01']);
                    $productCategoryTree = $row['ProductHierarchyLevel03'];
                    $productCategoryTreeSlug = sef_link($row['ProductHierarchyLevel03']);

                    if (!is_numeric($row['ItemCode'])) {
                        $productItemCode = str_replace('.', '', $row['ItemCode']);
                    }else{
                        $productItemCode = str_replace('.', '', $row['ItemCode']);
                    }
                    $productFind = $productModels->c_one(["ItemCode" => $productItemCode]);
                    if ($productFind) {
                        $insertProductID = $productFind->id;
                        if ($insertProductID) {
                            $variantFind = $productModels->productCombinationOne(["barcode_no" => $row['Barcode']]);
                            if ($variantFind) {
                                $insertVariantData = [
                                    "stock" => $row['Envanter'],
                                    "updated_at" => created_at()
                                ];
                                $updateAttr = $productModels->productAttrUpdate(['id' => $variantFind->id],$insertVariantData);
                            }
                        }
                    }
                }
            
            $url = base_url().'/api/searchProductInsert?apiKey=953E1C7C06494141B8DF4BBBDE76ED5E';
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
            return view ("admin/product/nebim", $data);
        }

        public function nebimOrderInsert($user_id, $order_id)
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $productFeatureModels = new ProductFeatureModels($db);
            $userModels = new UserModels($db);
            $orderModels = new OrderModels($db);
            $addressModels = new AddressModels($db);

            $orderFind = $orderModels->c_one(['id' => $order_id]);
            $orderDetailFind = $orderModels->orderDetailAll(['order_id' => $order_id]);
            $userFind = $userModels->c_one(['id' => $user_id]);
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
                for ($i = 1; $i <= $row->piece; $i++) {
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
                    $updateOrder = $orderModels->orderDetailNebimAdd($updateOrderDetailDate);
                }
            }
            
            $nebimCustomerData['Lines'][]  = [
                "ItemTypeCode" => "5",
                "ItemCode" => "KRG",
                "Qty1" => "1",
                "PriceVI" => floatval($orderFind->shipping_price),
            ];
            
            $response = $client->request('GET', 'http://193.111.73.193:88/IntegratorService/POST/7E299A342CAD4C448BD8E6EBEAFF3F14?'. json_encode($nebimCustomerData).'', ['connect_timeout' => 0]);
            $returJson = json_decode($response->getBody());
            $nebim_OrderNumber = $returJson->OrderNumber;
            $updateOrderDate = [
                "nebim_OrderNumber" => $nebim_OrderNumber,
                "updated_at" => created_at()
            ];
            $updateOrder = $orderModels->edit($order_id, $updateOrderDate);
            print_r($response->getBody());
        }

        public function nebimOrderDetailCanceled($order_id, $order_detail_id, $selectCount = '0')
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $productFeatureModels = new ProductFeatureModels($db);
            $userModels = new UserModels($db);
            $orderModels = new OrderModels($db);
            $addressModels = new AddressModels($db);

            $orderFind = $orderModels->c_one(['id' => $order_id]);
            $orderDetailFind = $orderModels->orderDetailNebimOne(['id' => $order_detail_id]);
            $userFind = $userModels->c_one(['id' => $user_id]);
            $userShippingAdress = $addressModels->c_one('order_address_clone', ['id' => $orderFind->shipping_address]);
            $userBillingAdress = $addressModels->c_one('order_address_clone', ['id' => $orderFind->billing_address]);
            $client = \Config\Services::curlrequest();
            $nebim_OrderNumber = $orderFind->nebim_OrderNumber;
            $singlePrice = $orderDetailFind->price;
            $cancellationPrice = $singlePrice * $selectCount;
            $nebimCustomerData = [
                "ModelType" => '6',
                "OrderNumber" => $orderFind->nebim_OrderNumber,
            ];

            $response = $client->request('GET', 'http://193.111.73.193:88/IntegratorService/POST/7E299A342CAD4C448BD8E6EBEAFF3F14?'. json_encode($nebimCustomerData).'', ['connect_timeout' => 0]);
            $returJson = json_decode($response->getBody());
            print_r($response->getBody());
        }

        public function nebimUserInsert($user_id)
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $userModels = new UserModels($db);
            $userFind = $userModels->c_one(['id' => $user_id]);
            $client = \Config\Services::curlrequest();
            if ($userFind->nebim_CurrAccCode) {
                $nebimCustomerData = [
                    "ModelType" => '3',
                    "CurrAccCode" => "". $userFind->nebim_CurrAccCode ."",
                    "FirstName" => "". $userFind->name ."",
                    "LastName" => "". $userFind->surname ."",
                    "OfficeCode" => "S008",
                    "CreditLimit" => "0",
                    "CurrencyCode" => "TRY",
                ];
            }else{
                $nebimCustomerData = [
                    "ModelType" => '3',
                    "CurrAccCode" => "",
                    "FirstName" => "". $userFind->name ."",
                    "LastName" => "". $userFind->surname ."",
                    "OfficeCode" => "S008",
                    "CreditLimit" => "0",
                    "CurrencyCode" => "TRY",
                    "Communications" => [
                        [
                            "CommunicationTypeCode" => 7,
                            "CommAddress" => "".$userFind->phone.""
                        ]
                    ],
                    "PostalAddresses" => [
                        [
                            "AddressTypeCode" => 1,
                        ]
                    ]
                ];
            }
            $response = $client->request('GET', 'http://193.111.73.193:88/IntegratorService/POST/7E299A342CAD4C448BD8E6EBEAFF3F14?'. str_replace(' ', '%20',json_encode($nebimCustomerData)) .'', ['connect_timeout' => 0]);
            $returJson = json_decode($response->getBody());
            $nebim_CurrAccCode = $returJson->CurrAccCode;
            $userUpdateDate = [
                'nebim_CurrAccCode' => $nebim_CurrAccCode,
                'updated_at' => created_at()
            ];
            $userFind = $userModels->edit(['id' => $user_id], $userUpdateDate);
            if ($userFind) {
                print_r($response->getBody());
            }else{

            }
        }

        public function nebimUserAddressInsert($user_id, $address_id)
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $userModels = new UserModels($db);
            $addressModels = new AddressModels($db);
            $userFind = $userModels->c_one(['id' => $user_id]);
            $addressFind = $addressModels->c_one('user_address',['user_id' => $user_id, 'id' => $address_id]);
            $town = $addressModels->c_one('town', ['TownID' => $addressFind->user_town], 'TownName ASC');
            $city = $addressModels->c_one('city', ['CityID' => $addressFind->user_city], 'CityName ASC');
            $neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $addressFind->user_neighborhood], 'NeighborhoodName ASC');
            $address = $addressFind->address . ' ' . $neighborhood->NeighborhoodName . ' ' . $town->TownName . '/' . $city->CityName;
            helper('text');
            $PostalAddressID = "". random_string('crypto', 8) ."-". random_string('crypto', 4) ."-". random_string('crypto', 4) ."-". random_string('crypto', 4) ."-". random_string('crypto', 12) ."";
            //echo $PostalAddressID;
            $client = \Config\Services::curlrequest();
            if ($userFind && $addressFind) {
                if ($userFind->nebim_CurrAccCode) {
                    $nebimCustomerData = [
                        "ModelType" => '3',
                        "CurrAccCode" => "". $userFind->nebim_CurrAccCode ."",
                        "FirstName" => "". $userFind->name ."",
                        "LastName" => "". $userFind->surname ."",
                        "OfficeCode" => "S008",
                        "CreditLimit" => "0",
                        "CurrencyCode" => "TRY",
                        "PostalAddresses" => [
                            [
                                "PostalAddressID" => $PostalAddressID,
                                "AddressTypeCode" => 1,
                                "CountryCode" => "TR",
                                "StateCode" => "".$town->StateCode."",
                                "CityCode" => "".$town->CityCode."",
                                "DistrictCode" => "".$town->TownCode."",
                                "Address" => "".$address.""
                            ]
                        ]
                    ];
                }else{
                    $nebimCustomerData = [
                        "ModelType" => '3',
                        "CurrAccCode" => "",
                        "FirstName" => "". $userFind->name ."",
                        "LastName" => "". $userFind->surname ."",
                        "OfficeCode" => "S008",
                        "CreditLimit" => "0",
                        "CurrencyCode" => "TRY",
                        "Communications" => [
                            [
                                "CommunicationTypeCode" => 7,
                                "CommAddress" => "".$userFind->phone.""
                            ]
                        ],
                        "PostalAddresses" => [
                            [
                                "AddressTypeCode" => 1,
                                "CountryCode" => "TR",
                                "StateCode" => "".$town->StateCode."",
                                "CityCode" => "".$town->CityCode."",
                                "DistrictCode" => "".$town->TownCode."",
                                "Address" => "".$address.""
                            ]
                        ]
                    ];
                }
                $response = $client->request('GET', 'http://193.111.73.193:88/IntegratorService/POST/7E299A342CAD4C448BD8E6EBEAFF3F14?'. str_replace(' ', '%20',json_encode($nebimCustomerData)) .'', ['connect_timeout' => 0]);
                $returJson = json_decode($response->getBody());
                $nebim_CurrAccCode = $returJson->CurrAccCode;
                $updateSetData = [
                    'nebim_PostalAddressID' => $PostalAddressID,
                    'updated_at' => created_at()
                ];
                $updateUserAddress = $addressModels->edit('user_address', ['user_id' => $user_id, 'id' => $address_id ], $updateSetData);
                print_r($response->getBody());
            }
        }

        public function nebimUserAddressUpdate($user_id, $address_id)
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $userModels = new UserModels($db);
            $addressModels = new AddressModels($db);
            $userFind = $userModels->c_one(['id' => $user_id]);
            $addressFind = $addressModels->c_one('user_address',['user_id' => $user_id, 'id' => $address_id]);
            $town = $addressModels->c_one('town', ['TownID' => $addressFind->user_town], 'TownName ASC');
            $city = $addressModels->c_one('city', ['CityID' => $addressFind->user_city], 'CityName ASC');
            $neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $addressFind->user_neighborhood], 'NeighborhoodName ASC');
            $nebim_CurrAccCode = 'Biltstore-'.$user_id.'';
            $address = $addressFind->address . ' ' . $neighborhood->NeighborhoodName . ' ' . $town->TownName . '/' . $city->CityName;
            helper('text');
            $PostalAddressID = "". random_string('crypto', 8) ."-". random_string('crypto', 4) ."-". random_string('crypto', 4) ."-". random_string('crypto', 4) ."-". random_string('crypto', 12) ."";
            //echo $PostalAddressID;
            $client = \Config\Services::curlrequest();
            if ($userFind && $addressFind) {
                if ($userFind->nebim_CurrAccCode) {
                    $nebimCustomerData = [
                        "ModelType" => '2',
                        "CurrAccCode" => "". $userFind->nebim_CurrAccCode ."",
                        "CurrAccDescription" => "Musteri",
                        "CurrencyCode" => "TRY",
                        "CustomerTypeCode" => 0,
                        "OfficeCode" => "S008",
                        "PostalAddresses" => [
                            [
                                "PostalAddressID" => $PostalAddressID,
                                "AddressTypeCode" => 1,
                                "CountryCode" => "TR",
                                "StateCode" => "".$town->StateCode."",
                                "CityCode" => "".$town->CityCode."",
                                "DistrictCode" => "".$town->TownCode."",
                                "Address" => "".$address.""
                            ]
                        ]
                    ];
                }else{
                    $nebimCustomerData = [
                        "ModelType" => '2',
                        "CurrAccCode" => "",
                        "FirstName" => "$userFind->name",
                        "LastName" => "$userFind->surname",
                        "CurrAccDescription" => "Musteri",
                        "CurrencyCode" => "TRY",
                        "CustomerTypeCode" => 0,
                        "OfficeCode" => "S008",
                        "Communications" => [
                            [
                                "CommunicationTypeCode" => 7,
                                "CommAddress" => "".$userFind->phone.""
                            ]
                        ],
                        "PostalAddresses" => [
                            [
                                "PostalAddressID" => $PostalAddressID,
                                "AddressTypeCode" => 1,
                                "CountryCode" => "TR",
                                "StateCode" => "".$town->StateCode."",
                                "CityCode" => "".$town->CityCode."",
                                "DistrictCode" => "".$town->TownCode."",
                                "Address" => "".$address.""
                            ]
                        ]
                    ];
                }
                $response = $client->request('GET', 'http://193.111.73.193:88/IntegratorService/POST/7E299A342CAD4C448BD8E6EBEAFF3F14?'. str_replace(' ', '%20',json_encode($nebimCustomerData)) .'', ['connect_timeout' => 0]);
                $returJson = json_decode($response->getBody());
                $nebim_CurrAccCode = $returJson->CurrAccCode;
                $updateSetData = [
                    'nebim_PostalAddressID' => $PostalAddressID,
                    'updated_at' => created_at()
                ];
                $updateUserAddress = $addressModels->edit('user_address', ['user_id' => $user_id, 'id' => $address_id ], $updateSetData);
                print_r($response->getBody());
            }
        }
          
    }
