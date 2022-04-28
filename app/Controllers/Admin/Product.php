<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\ProductModels;
    use App\Models\UserModels;
    use App\Models\Category;
    use App\Models\AttributeModels;
    use App\Models\ProductFeatureModels;
    use App\Models\AttributeGroupModels;
    use App\Models\BrandModels;

    class Product extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index($page = '1')
        {
            $data['sidebarActive'] = 'product';
            $db = db_connect();
            $productModels = new ProductModels($db);
            $category = new Category($db);
            $data['productModels'] = $productModels;
            if (isset($_GET['filter'])) {
				$filter = $_GET['filter'];
				$filterArrayVal = array();
				$filterArray = array();
			}
            if (isset($filter)) {
				$filterPar = strstr($filter, '=');
				$filter = '?filter='.$filter;
				$filterPar = ltrim($filterPar , '=');
				if (!$filterPar && $_GET['filter']) {
					$filterPar = $_GET['filter'];
				}
				$filterCat = explode(';',$filterPar);
				
				foreach ($filterCat as $value) {
					$prop = strstr($value, ':', true);
					$values = strstr($value, ':');
					$val = ltrim($values, ':');
					$valArray = explode(',',$val);
					$index = '';
					foreach ($valArray as $vals) {
						$vals = urlutf_8($vals) ;
						$filterArrayVal[$prop][] = $vals;
						$index .= ''.$vals.''.',';
					}
					$index = rtrim($index, ',');
					$filterArray[$prop][] = $index;
				}
				foreach ($filterArrayVal as $key => $filterValue) {
					if($key == 'text' && $key){
						$filterWhereIn['title'] = $filterValue;
					}
				}
				$data['in'] = $in;
			}	
			if (isset($filterArrayVal)) {
				$data['filterArrayVal'] = $filterArrayVal;
			}
            $data['filter'] = $filter;
            $data['productCount'] = $productModels->count('', '', $filterWhereIn);
            $item = 12;
			if (!$page) {
				$page = 1;
			}
			$totalItem = $data['productCount'];
			$totalPage = ceil($totalItem / $item);
			if ($totalPage < $page) {
				$page = 1;
				$whereStart = ($page * $item) - $item;
			}else{
				$whereStart = ($page * $item) - $item;
			}
			$data['totalPage'] = $totalPage;
            $data['page'] = $page;
            //$data['productList'] = $productModels->c_all(['1' => 1], 'FIND_IN_SET("155", category_id) ');
            $data['productList'] = $productModels->c_all('', '', ['whereStart' => $whereStart, 'item' => $item], $filterWhereIn);
            $data['categoriesListView'] = categoriesListView($category->c_all_list());
            $data['productCount'] = $productModels->count();
            return view ("admin/product/product-list", $data);
        }
        
        public function add()
        {
            $data['sidebarActive'] = 'product';
            $db = db_connect();
            $productModels = new ProductModels($db);
            $attributeModels = new AttributeModels($db);
            $attributeGroupModels = new AttributeGroupModels($db);
            $data['product'] = $productModels->c_all('', '', '', '');
            $data['categoriesList'] = categoriesAddViewProduct($productModels->c_all_list());
            $data['brandList'] = $productModels->brandAll(["is_active" => '1']);
            $data['attributeGroupList'] = $attributeGroupModels->c_all([
                    "is_active" => 1,
                    "is_combination !=" => 1,
            ]);
            $data['attributeGroupVariantList'] = $attributeGroupModels->c_all([
                    "is_active" => 1,
                    "is_combination =" => 1,
            ]);
            $data['attributeModels'] = $attributeModels;
            ///$data['attributTypeList'] = $attributeModels->c_all(["a.is_active" => '1'], ['6']);
            //$data['attributAgeList'] = $attributeModels->c_all(["a.is_active" => '1'], ['7']);
            //$data['attributGenderList'] = $attributeModels->c_all(["a.is_active" => '1'], ['8']);
            return view ("admin/product/product-add", $data);
        }

        public function edit($id, $par = 'home')
        {
            $data["title"] = "Kategori Düzenle";
            $data['sidebarActive'] = 'product';
            $data["tapStart"] = $par;
            $db = db_connect();
            $productModels = new ProductModels($db);
            $attributeModels = new AttributeModels($db);
            $attributeGroupModels = new AttributeGroupModels($db);
            $productFeatureModels = new ProductFeatureModels($db);
            $userModels = new UserModels($db);
            $data['c_all_type'] = $userModels->c_all_type(['type' => 'frontend']);
            $data['product'] = $productModels->c_all('', '', '', '');
            $data['brandList'] = $productModels->brandAll(["is_active" => '1']);
            $data['product_id'] = $id;
            $data['productModels'] = $productModels;
            $data['productFind'] = $productModels->c_one([
                "id" => $id
            ]);
           
            $data['productCombination'] = $productModels->productCombinationAll(["pa.id_product" => $id, 'pa.is_active != ' => '3']);
            $data['productPicture'] = $productModels->c_all_image([
                "product_id" => $id
            ]);
            $categoryArray = explode(',', $data['productFind']->category_id);
            $data['categoriesList'] = categoriesAddViewProduct($productModels->c_all_list(), '' , $categoryArray);
            
            $attributList = $attributeModels->c_all(["a.is_active" => '2', 'ag.is_combination' => '1'], '', '');
            $data['attributeGroupList'] = $attributeGroupModels->c_all([
                    "is_active" => 1,
                    "is_combination !=" => 1,
            ]);
            $data['attributeGroupVariantList'] = $attributeGroupModels->c_all([
                "is_active" => 1,
                "is_combination =" => 1,
            ]);
            $thisProductPrice = $productModels->product_price_all(['product_id' => $id, 'product_attr_id' => $data['productCombination'][0]->id]);
            foreach ($thisProductPrice as $price) {
                $data['productPrice'][$price->user_type] = $price->price;
            }
            $data['attributeModels'] = $attributeModels;
            $data['productFeatureModels'] = $productFeatureModels;
            $data['productArray'] = explode(',', $data['productCombination']['0']->mf_product);
            $data['similarProductArray'] = explode(',', $data['productFind']->similar_product);
            foreach ($attributList as $key => $item) {
                $data['dizi'][$key]['id'] = $item->id;
                $data['dizi'][$key]['text'] = $item->ag_title . ' : ' .$item->title;
                $data['dizi'][$key]['group_title'] = $item->ag_title;
                $data['dizi'][$key]['group_id'] = $item->ag_id;
                $data['dizi'][$key]['value'] = $item->ag_title . ' : ' .$item->title;
            }
            $data['attributList'] = json_encode($data['dizi']);
            return view ("admin/product/product-edit", $data);
        }
        
        public function combination()
        {
            $validation =  \Config\Services::validation();
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $brand_id = $this->request->getPost('brand_id');
            $productFeature = $this->request->getPost('productFeature');
            $parent_id = rtrim($this->request->getPost('parent_id'), ',');
            $attributeArea = $this->request->getPost('attributeArea');
            $standart_kdv_rate = $this->request->getPost('standart_kdv_rate');
            $special_kdv_rate = $this->request->getPost('special_kdv_rate');
            $sale_price_kdv = $this->request->getPost('sale_price_kdv');
            $discount_price_kdv = $this->request->getPost('discount_price_kdv');
            $basket_price_kdv = $this->request->getPost('basket_price_kdv');
            $outlet_price_kdv = $this->request->getPost('outlet_price_kdv');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $slug = $this->request->getPost('slug');
            $is_active = $this->request->getPost('is_active');
            $selectedAttrArray = json_decode($attributeArea);
            $attrGroupArray = [];
            $db = db_connect();
            $productModels = new ProductModels($db);
            $productFeatureModels = new ProductFeatureModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen ürün adını doldurunuz.';
            }elseif (!$validation->check($parent_id, 'required')) {
                $data['error'] =  'Lütfen ürünü kategorisini belirleyiniz.';
            }elseif (!$validation->check($sale_price_kdv, 'required') && !$validation->check($discount_price_kdv, 'required') && !$validation->check($basket_price_kdv, 'required') && !$validation->check($outlet_price_kdv, 'required')) {
                $data['error'] =  'Lütfen bir fiyat belirleyiniz.';
            }else{
                foreach ($selectedAttrArray as $key => $row) {
                    $attrGroupArray[$row->group_id][$key]["text"] = $row->text;
                    $attrGroupArray[$row->group_id][$key]["value"] = $row->id;
                }
                if (!isset($attrGroupArray['4']) && !isset($attrGroupArray['5'])) {
                   
                }elseif (!isset($attrGroupArray['4'])) {
                    foreach ($attrGroupArray['5'] as $key => $item) {
                        $combinArray[$key][] = $item['value'];
                    }
                }elseif (!isset($attrGroupArray['5'])) {
                    foreach ($attrGroupArray['4'] as $key => $item) {
                        $combinArray[$key][] = $item['value'];
                    }
                }else{
                    foreach ($attrGroupArray['4'] as $keyParent => $row) {
                            $i = 0;
                        foreach ($attrGroupArray['5'] as $key => $item) {
                            $combinArray[$keyParent][$key][] = $row['value'];
                            $combinArray[$keyParent][$key][] = $item['value'];
                        }
                    }
                }
                if ($standart_kdv_rate == '-1') {
                    $kdv = $special_kdv_rate;
                }else{
                    $kdv = $standart_kdv_rate;
                }
                if (!$slug) {
                    $slug = sef_link($title);
                }else{
                    $slug = sef_link($slug);
                }
                $productInsertData = [
                        "title" => $title,
                        "category_id" => $parent_id,
                        "description" => $description,
                        "brand_id" => $brand_id,
                        "product_type" => $product_type,
                        "age_type" => $age_type,
                        "gender_type" => $gender_type,
                        "tax_rate" => $kdv,
                        "sale_price" => $sale_price_kdv,
                        "basket_price" => $basket_price_kdv,
                        "discount_price" => $discount_price_kdv,
                        "outlet_price" => $outlet_price_kdv,
                        "seo_title" => $seo_title,
                        "seo_description" => $seo_description,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "created_at" => created_at()
                ];

                $insertProduct = $productModels->add($productInsertData);
                $product_id = $db->insertID();
                if ($product_id) {
                    $upload_id = session()->get("uploadProductCode");
                    if ($upload_id) {
                        $updateProductImage = $productModels->pictureEditSession($upload_id, ['product_id' => $product_id, "upload_id" => NULL]);
                        if ($updateProductImage) {
                            session()->remove('uploadProductCode');
                        }
                    }
                    $productFeatureModels->deleteRow(['product_id' => $id]);
                    foreach ($productFeature as $key => $item) {
                        if ($item) {
                            $productFeatureData = [
                                    "product_id" => $product_id,
                                    "attribute_group_id" => $key,
                                    "attribute_id" => $item
                            ];
                            $productFeatureModels->add($productFeatureData);
                        }
                    }
                    if (!isset($attrGroupArray['4'])) {
                        foreach ($attrGroupArray['4'] as $key => $item) {
                            $productFeatureData = [
                                    "product_id" => $product_id,
                                    "attribute_group_id" => '4',
                                    "attribute_id" =>$item['value']
                            ];
                            $productFeatureModels->add($productFeatureData);
                        }
                    }
                    if (!isset($attrGroupArray['5'])) {
                        foreach ($attrGroupArray['5'] as $key => $item) {
                            $productFeatureData = [
                                    "product_id" => $product_id,
                                    "attribute_group_id" => '5',
                                    "attribute_id" =>$item['value']
                            ];
                            $productFeatureModels->add($productFeatureData);
                        }
                    }
                }

                if (!isset($attrGroupArray['4']) && !isset($attrGroupArray['5'])) {
                   
                }elseif (!isset($attrGroupArray['4'])) {
                    foreach ($combinArray as $row) {
                        $productAttrInsertData = [
                            "id_product" => $product_id,
                            "created_at" => created_at()
                        ];
                        $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                        $product_attr_id = $db->insertID();
                        foreach ($row as $item) {
                         
                            $productattributecombinationData = [
                                "attribute_id" => $item,
                                "attribute_product_id" => $product_attr_id,
                            ];
                            $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                            $combine_id = $db->insertID();

                            $productFeatureData = [
                                    "product_id" => $product_id,
                                    "product_attribute_id" => $product_attr_id,
                                    "attribute_group_id" => '4',
                                    "attribute_id" => $item
                            ];
                            $productFeatureModels->add($productFeatureData);
                        }
                    }
                }elseif (!isset($attrGroupArray['5'])) {
                    foreach ($combinArray as $row) {
                        $productAttrInsertData = [
                            "id_product" => $product_id,
                            "created_at" => created_at()
                        ];
                        $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                        $product_attr_id = $db->insertID();
                        foreach ($row as $item) {
                            $productattributecombinationData = [
                                "attribute_id" => $item,
                                "attribute_product_id" => $product_attr_id,
                            ];
                            $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                            $combine_id = $db->insertID();

                            $productFeatureData = [
                                "product_id" => $product_id,
                                "product_attribute_id" => $product_attr_id,
                                "attribute_group_id" => '5',
                                "attribute_id" => $item
                            ];
                            $productFeatureModels->add($productFeatureData);
                        }
                    }
                }else{
                    foreach ($combinArray as $key => $row) {
                        foreach ($row as $item) {
                            $productAttrInsertData = [
                                "id_product" => $product_id,
                                "created_at" => created_at()
                            ];
                            $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                            $product_attr_id = $db->insertID();
                            foreach ($item as $lastItem) {
                                $productattributecombinationData = [
                                    "attribute_id" => $lastItem,
                                    "attribute_product_id" => $product_attr_id,
                                ];
                                $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                                $combine_id = $db->insertID();
                            }
                        }
                    }
                }

                if ($combine_id) {
                    $data['success'] = 'Ürün kombinasyonon işlemi başarılı bir şekilde tamamlandı.';
                    $data['location'] = 'product/edit/'.$product_id.'/combination';

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
                }else{
                    $data['error'] = 'Ürün kombinasyonon işlemi yapılamadı lütfen girilen bilgileri kontral ediniz.';
                }
            }
            return json_encode($data);
        }

        public function insert()
        {
            $validation =  \Config\Services::validation();
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $brand_id = $this->request->getPost('brand_id');
            $productFeature = $this->request->getPost('productFeature');
            $parent_id = rtrim($this->request->getPost('parent_id'), ',');
            $attributeArea = $this->request->getPost('attributeArea');
            $standart_kdv_rate = $this->request->getPost('standart_kdv_rate');
            $special_kdv_rate = $this->request->getPost('special_kdv_rate');
            $sale_price_kdv = $this->request->getPost('sale_price_kdv');
            $discount_price_kdv = $this->request->getPost('discount_price_kdv');
            $basket_price_kdv = $this->request->getPost('basket_price_kdv');
            $outlet_price_kdv = $this->request->getPost('outlet_price_kdv');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $product_type = $this->request->getPost('product_type');
            $sku = $this->request->getPost('sku');
            $barcode_no = $this->request->getPost('barcode_no');
            $stock = $this->request->getPost('stock');
            $expiration_date = $this->request->getPost('expiration_date');
            $max_sale = $this->request->getPost('max_sale');
            $mf_required = $this->request->getPost('mf_required');
            $mf_count = $this->request->getPost('mf_count');
            $mf_productArray = $this->request->getPost('mf_product');
            $similar_productArray = $this->request->getPost('similar_product');
            $is_active_sell = $this->request->getPost('is_active_sell');
            $season = $this->request->getPost('season');
            $weight = $this->request->getPost('weight');
            $additional_delivery_times = $this->request->getPost('additional_delivery_times');
            $additional_delivery_price = $this->request->getPost('additional_delivery_price');
            $slug = $this->request->getPost('slug');
            $is_active = $this->request->getPost('is_active');
            $attrID = $this->request->getPost('attrID');
            $barcodeNo = $this->request->getPost('barcodeNo');
            $stockNo = $this->request->getPost('stockNo');
            $selectedAttrArray = json_decode($attributeArea);
            $attrGroupArray = [];
            $db = db_connect();
            $productModels = new ProductModels($db);
            $attributeModels = new AttributeModels($db);
            $productFeatureModels = new ProductFeatureModels($db);
            foreach ($mf_productArray as $row) {
                $mf_product = $row.',';
            }
            $mf_product = rtrim($mf_product, ',');
            foreach ($similar_productArray as $row) {
                $similar_product = $row.',';
            }
            $similar_product = rtrim($similar_product, ',');
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen ürün adını doldurunuz.';
            }elseif (!$validation->check($parent_id, 'required')) {
                $data['error'] =  'Lütfen ürünü kategorisini belirleyiniz.';
            }elseif (!$validation->check($sale_price_kdv, 'required') && !$validation->check($discount_price_kdv, 'required') && !$validation->check($basket_price_kdv, 'required') && !$validation->check($outlet_price_kdv, 'required')) {
                $data['error'] =  'Lütfen bir fiyat belirleyiniz.';
            }elseif ($is_active_sell && !$expiration_date) {
                $data['error'] = 'Lütfen ürünün Miat tarihini giriniz.';
            }elseif ($is_active_sell && ($mf_required && $mf_count && !$mf_product)) {
                $data['error'] = 'Lütfen MF Ürünlerini seçiniz';
            }else{
                if ($selectedAttrArray) {
                    foreach ($selectedAttrArray as $key => $row) {
                        $attrGroupArray[$row->group_id][$key]["text"] = $row->text;
                        $attrGroupArray[$row->group_id][$key]["value"] = $row->id;
                    }
                }
                if ($standart_kdv_rate == '-1') {
                    $kdv = $special_kdv_rate;
                }else{
                    $kdv = $standart_kdv_rate;
                }
                if (!$slug) {
                    $slug = sef_link($title);
                }else{
                    $slug = sef_link($slug);
                }
                $productInsertData = [
                        "title" => $title,
                        "category_id" => $parent_id,
                        "description" => $description,
                        "product_type" => $product_type,
                        "similar_product" => $similar_product,
                        "sku" => $sku,
                        "barcode_no" => $barcode_no,
                        "season" => $season,
                        "weight" => $weight,
                        "additional_delivery_times" => $additional_delivery_times,
                        "additional_delivery_price" => $additional_delivery_price,
                        "brand_id" => $brand_id,
                        "tax_rate" => $kdv,
                        "sale_price" => $sale_price_kdv,
                        "basket_price" => $basket_price_kdv,
                        "discount_price" => $discount_price_kdv,
                        "outlet_price" => $outlet_price_kdv,
                        "seo_title" => $seo_title,
                        "seo_description" => $seo_description,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "created_at" => created_at()
                ];

                $insertProduct = $productModels->add($productInsertData);
                $product_id = $db->insertID();
                if ($product_id) {
                    $upload_id = session()->get("uploadProductCode");
                    if ($upload_id) {
                        $updateProductImage = $productModels->pictureEditSession($upload_id, ['product_id' => $product_id, "upload_id" => NULL]);
                        if ($updateProductImage) {
                            session()->remove('uploadProductCode');
                        }
                    }
                    $productFeatureModels->deleteRow(['product_id' => $product_id]);
                    if ($product_type == '0') {
                        $barcode_no_real = $barcode_no ? $barcode_no : $product_id;
                        $productAttrInsertData = [
                            'id_product' => $product_id,
                            'barcode_no' => $barcode_no_real,
                            'stock' => $stock,
                            'expiration_date' => $expiration_date,
                            'max_sale' => $max_sale,
                            'mf_required' => $mf_required,
                            'mf_count' => $mf_count,
                            'mf_product' => $mf_product,
                            'title' => $title,
                            "tax_rate" => $kdv,
                            "sale_price" => $sale_price_kdv,
                            "basket_price" => $basket_price_kdv,
                            "discount_price" => $discount_price_kdv,
                            'is_active' => $is_active
                        ];
                        $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);

                        $product_attr_id = $db->insertID();

                        foreach ($discount_price_kdv as $key => $item) {
                              $dataPriceUpdate = [
                                'product_id' => $product_id,
                                'product_attr_id' => $product_attr_id,
                                'user_type' => $key,
                                'price' => $item,
                                'is_active' => '1',
                                'status' => '1',
                                'created_at' => created_at(),
                            ];
                            $insertCombinPrice = $productModels->product_price_edit($dataPriceUpdate);
                        }
                        
                    }else{
                        foreach ($attrID as $keyParent => $row) {
                            $productAttrInsertData = [
                                "id_product" => $product_id,
                                "barcode_no" => $barcodeNo[$keyParent],
                                "stock" => $stockNo[$keyParent],
                                "created_at" => created_at()
                            ];
                            $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                            $product_attr_id = $db->insertID();
                            $attrArrayID = explode(',', $row);
                            foreach ($attrArrayID as $key => $item) {
                             
                                $productattributecombinationData = [
                                    "attribute_id" => $item,
                                    "attribute_product_id" => $product_attr_id,
                                ];
                                $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                                $combine_id = $db->insertID();
                                $arrtGroupID = $attributeModels->c_one(['a.id' => $item]);
                                $productFeatureData = [
                                    "product_id" => $product_id,
                                    "product_attribute_id" => $product_attr_id,
                                    "attribute_group_id" => $arrtGroupID->ag_id,
                                    "attribute_id" => $item
                                ];
                                $productFeatureModels->add($productFeatureData);
                            }
                        }
                    }
                    foreach ($productFeature as $key => $item) {
                        if ($item) {
                            $productFeatureData = [
                                    "product_id" => $product_id,
                                    "attribute_group_id" => $key,
                                    "attribute_id" => $item
                            ];
                            $productFeatureModels->add($productFeatureData);
                        }
                    }
                }
                if ($product_id) {
                    $data['success'] = 'Ürün ekleme işlemi başarılı bir şekilde tamamlandı.';
                    $data['location'] = 'product/edit/'.$product_id.'';

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

                }else{
                    $data['error'] = 'Ürün ekleme işlemi yapılamadı lütfen girilen bilgileri kontral ediniz.';
                }
            }
            return json_encode($data);
        }

        public function update()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $brand_id = $this->request->getPost('brand_id');
            $productFeature = $this->request->getPost('productFeature');
            $combinationFeature = $this->request->getPost('combinationFeature');
            $parent_id = rtrim($this->request->getPost('parent_id'), ',');
            $attributeArea = $this->request->getPost('attributeArea');
            $standart_kdv_rate = $this->request->getPost('standart_kdv_rate');
            $special_kdv_rate = $this->request->getPost('special_kdv_rate');
            $sale_price_kdv = $this->request->getPost('sale_price_kdv');
            $discount_price_kdv = $this->request->getPost('discount_price_kdv');
            $basket_price_kdv = $this->request->getPost('basket_price_kdv');
            $outlet_price_kdv = $this->request->getPost('outlet_price_kdv');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $slug = $this->request->getPost('slug');
            $product_type = $this->request->getPost('product_type');
            $barcode_no = $this->request->getPost('barcode_no');
            $stock = $this->request->getPost('stock');
            $expiration_date = $this->request->getPost('expiration_date');
            $max_sale = $this->request->getPost('max_sale');
            $mf_required = $this->request->getPost('mf_required');
            $mf_count = $this->request->getPost('mf_count');
            $mf_productArray = $this->request->getPost('mf_product');
            $similar_productArray = $this->request->getPost('similar_product');
            $is_active_sell = $this->request->getPost('is_active_sell');
            $season = $this->request->getPost('season');
            $is_active = $this->request->getPost('is_active');
            $sku = $this->request->getPost('sku');
            $barcodeNo = $this->request->getPost('barcodeNo');
            $barcodeNoNew = $this->request->getPost('barcodeNoNew');
            $stockNo = $this->request->getPost('stockNo');
            $stockNoNew = $this->request->getPost('stockNoNew');
            $weight = $this->request->getPost('weight');
            $attrID = $this->request->getPost('attrID');
            $additional_delivery_times = $this->request->getPost('additional_delivery_times');
            $additional_delivery_price = $this->request->getPost('additional_delivery_price');
            $selectedAttrArray = json_decode($attributeArea);
            $attrGroupArray = [];
            $db = db_connect();
            $productModels = new ProductModels($db);
            $attributeModels = new AttributeModels($db);
            $productFeatureModels = new productFeatureModels($db);
            if (!$combinationFeature) {
                $combinationFeature = array();
            } 
            foreach ($mf_productArray as $row) {
                $mf_product = $row.',';
            }
            $mf_product = rtrim($mf_product, ',');
            foreach ($similar_productArray as $row) {
                $similar_product = $row.',';
            }
            $similar_product = rtrim($similar_product, ',');
            $barcodeNoError = FALSE;
            foreach ($barcodeNo as $key => $row) {
                if (!$row) {
                    $barcodeNoError = TRUE;
                }
            }  
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen ürün adını doldurunuz.';
            }elseif (!$validation->check($parent_id, 'required')) {
                $data['error'] =  'Lütfen ürünü kategorisini belirleyiniz.';
            }elseif (!$validation->check($sale_price_kdv, 'required') && !$validation->check($discount_price_kdv, 'required') && !$validation->check($basket_price_kdv, 'required') && !$validation->check($outlet_price_kdv, 'required')) {
                $data['error'] =  'Lütfen bir fiyat belirleyiniz.';
            }elseif ($is_active_sell && !$expiration_date) {
                $data['error'] = 'Lütfen ürünün Miat tarihini giriniz.';
            }elseif ($is_active_sell && ($mf_required && $mf_count && !$mf_product)) {
                $data['error'] = 'Lütfen MF Ürünlerini seçiniz';
            }else{
                if ($selectedAttrArray) {
                   
                    foreach ($selectedAttrArray as $key => $row) {
                        $attrGroupArray[$row->group_id][$key]["text"] = $row->text;
                        $attrGroupArray[$row->group_id][$key]["value"] = $row->id;
                    }
                }
               
                if ($standart_kdv_rate == '-1') {
                    $kdv = $special_kdv_rate;
                }else{
                    $kdv = $standart_kdv_rate;
                }
                if (!$slug) {
                    $slug = sef_link($title);
                }else{
                    $slug = sef_link($slug);
                }
                $productInsertData = [
                    "title" => $title,
                    "category_id" => $parent_id,
                    "description" => $description,
                    "brand_id" => $brand_id,
                    "seo_title" => $seo_title,
                    "seo_description" => $seo_description,
                    "slug" => $slug,
                    "product_type" => $product_type,
                    "similar_product" => $similar_product,
                    "sku" => $sku,
                    "barcode_no" => $barcode_no,
                    "season" => $season,
                    "weight" => $weight,
                    "additional_delivery_times" => $additional_delivery_times,
                    "additional_delivery_price" => $additional_delivery_price,
                    "is_active" => $is_active,
                    "created_at" => created_at()
                ];
                $updateCategory = $productModels->edit($id,$productInsertData);
                if ($updateCategory) {
                    $product_id = $id;
                    if ($product_id) {
                        $upload_id = session()->get("uploadProductCode");
                        if ($upload_id) {
                            $updateProductImage = $productModels->pictureEditSession($upload_id, ['product_id' => $product_id, "upload_id" => NULL]);
                            if ($updateProductImage) {
                                session()->remove('uploadProductCode');
                            }
                        }
                        $productFeatureModels->deleteRow(['product_id' => $id], 'attribute_group_id NOT IN("5","4")');
                        foreach ($productFeature as $key => $item) {
                            if ($item) {
                                $productFeatureData = [
                                        "product_id" => $product_id,
                                        "attribute_group_id" => $key,
                                        "attribute_id" => $item
                                ];
                                $productFeatureModels->add($productFeatureData);
                            }
                        }

                    }

                    $rand = rand(0,999999999);

                    if (isset($barcodeNo)) {
                        foreach ($barcodeNo as $key => $row) {
                            $productCombinationUpdateData = [
                                "barcode_no" => $row,
                                "rand" => $rand
                            ];
                            $productCombinationUpdate = $productModels->productCombinationUpdate(['id' => $key], $productCombinationUpdateData);
                        }  
                    }
                    if (isset($stockNo)) {
                        foreach ($stockNo as $key => $row) {
                            $productCombinationUpdateStockData = [
                                "stock" => $row,
                                "rand" => $rand
                            ];
                            $productCombinationUpdate = $productModels->productCombinationUpdate(['id' => $key], $productCombinationUpdateStockData);
                        }
                    }

                    if ($product_type == '0') {
                        $barcode_no_real = $barcode_no ? $barcode_no : $product_id;
                        $productCombinationOneFind = $productModels->productCombinationOne(["id_product" => $id, 'barcode_no' => $barcode_no_real]);
                        if ($productCombinationOneFind) {
                            $productAttrInsertData = [
                                'id_product' => $product_id,
                                'barcode_no' => $barcode_no_real,
                                'stock' => $stock,
                                'expiration_date' => $expiration_date,
                                'max_sale' => $max_sale,
                                'mf_required' => $mf_required,
                                'mf_count' => $mf_count,
                                'mf_product' => $mf_product,
                                'title' => $title,
                                "tax_rate" => $kdv,
                                "sale_price" => $sale_price_kdv,
                                "basket_price" => $basket_price_kdv,
                                'is_active' => $is_active_sell
                            ];
                            $insertProductAttr = $productModels->productCombinationUpdate(['id' => $productCombinationOneFind->id], $productAttrInsertData);

                            foreach ($discount_price_kdv as $key => $item) {
                                $thisProductPrice = $productModels->product_price_one(['product_id' => $product_id, 'product_attr_id' => $productCombinationOneFind->id, 'user_type' => $key]);
                                $dataPriceUpdate = [
                                    'product_id' => $product_id,
                                    'product_attr_id' => $productCombinationOneFind->id,
                                    'user_type' => $key,
                                    'price' => $item,
                                    'is_active' => '1',
                                    'status' => '1',
                                    'created_at' => created_at(),
                                ];
                                if ($thisProductPrice) {
                                    $insertCombinPrice = $productModels->product_price_edit(['id' => $thisProductPrice->id], $dataPriceUpdate);
                                }else{
                                    $insertCombinPrice = $productModels->product_price_add($dataPriceUpdate);
                                }
                            }
                        }else{
                            $productAttrInsertData = [
                                'id_product' => $product_id,
                                'barcode_no' => $barcode_no_real,
                                'stock' => $stock,
                                'expiration_date' => $expiration_date,
                                'max_sale' => $max_sale,
                                'mf_required' => $mf_required,
                                'mf_count' => $mf_count,
                                'mf_product' => $mf_product,
                                'title' => $title,
                                "tax_rate" => $kdv,
                                "sale_price" => $sale_price_kdv,
                                "basket_price" => $basket_price_kdv,
                                "discount_price" => $discount_price_kdv,
                                'is_active' => $is_active_sell
                            ];
                            $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                            $product_attr_id = $db->insertID();

                            foreach ($discount_price_kdv as $key => $item) {
                                  $dataPriceUpdate = [
                                    'product_id' => $product_id,
                                    'product_attr_id' => $product_attr_id,
                                    'user_type' => $key,
                                    'price' => $item,
                                    'is_active' => '1',
                                    'status' => '1',
                                    'created_at' => created_at(),
                                ];
                                $insertCombinPrice = $productModels->product_price_edit($dataPriceUpdate);
                            }
                        }
                    }else {
                        foreach ($attrID as $keyParent => $row) {
                            $productAttrInsertData = [
                                "id_product" => $product_id,
                                "barcode_no" => $barcodeNoNew[$keyParent],
                                "stock" => $stockNoNew[$keyParent],
                                "created_at" => created_at()
                            ];
                            $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                            $product_attr_id = $db->insertID();
                            $attrArrayID = explode(',', $row);
                            foreach ($attrArrayID as $key => $item) {
                             
                                $productattributecombinationData = [
                                    "attribute_id" => $item,
                                    "attribute_product_id" => $product_attr_id,
                                ];
                                $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                                $combine_id = $db->insertID();
                                $arrtGroupID = $attributeModels->c_one(['a.id' => $item]);
                                $productFeatureData = [
                                    "product_id" => $product_id,
                                    "product_attribute_id" => $product_attr_id,
                                    "attribute_group_id" => $arrtGroupID->ag_id,
                                    "attribute_id" => $item
                                ];
                                $productFeatureModels->add($productFeatureData);
                            }
                        }
                    }
                    $data['error'] = "Ürün düzenleme işlemi başarılı bir şekilde yapıldı.";
                  
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

                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
            return json_encode($data);
        }

        public function combinationNew($id)
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $brand_id = $this->request->getPost('brand_id');
            $productFeature = $this->request->getPost('productFeature');
            $combinationFeature = $this->request->getPost('combinationFeature');
            $parent_id = rtrim($this->request->getPost('parent_id'), ',');
            $attributeArea = $this->request->getPost('attributeArea');
            $standart_kdv_rate = $this->request->getPost('standart_kdv_rate');
            $special_kdv_rate = $this->request->getPost('special_kdv_rate');
            $sale_price_kdv = $this->request->getPost('sale_price_kdv');
            $discount_price_kdv = $this->request->getPost('discount_price_kdv');
            $basket_price_kdv = $this->request->getPost('basket_price_kdv');
            $outlet_price_kdv = $this->request->getPost('outlet_price_kdv');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $slug = $this->request->getPost('slug');
            $is_active = $this->request->getPost('is_active');
            $barcodeNo = $this->request->getPost('barcodeNo');
            $stockNo = $this->request->getPost('stockNo');
            $attr = $this->request->getPost('attr');
            $selectedAttrArray = json_decode($attributeArea);
            $newAttrArray = json_decode($attr);
            $attrGroupArray = [];
            $db = db_connect();
            $productModels = new ProductModels($db);
            $productFeatureModels = new productFeatureModels($db);
            $combinArray = array();
            if (!$combinationFeature) {
                $combinationFeature = array();
            } 
            $barcodeNoError = FALSE;
            if (isset($barcodeNo)) {
                foreach ($barcodeNo as $key => $row) {
                    if (!$row) {
                        $barcodeNoError = TRUE;
                    }
                }  
            }
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen ürün adını doldurunuz.';
            }elseif (!$validation->check($parent_id, 'required')) {
                $data['error'] =  'Lütfen ürünü kategorisini belirleyiniz.';
            }elseif (!$validation->check($sale_price_kdv, 'required') && !$validation->check($discount_price_kdv, 'required') && !$validation->check($basket_price_kdv, 'required') && !$validation->check($outlet_price_kdv, 'required')) {
                $data['error'] =  'Lütfen bir fiyat belirleyiniz.';
            }else{
                /*
                    if ($selectedAttrArray) {
                        foreach ($selectedAttrArray as $key => $row) {
                            $attrGroupArray[$row->group_id][] = $row->id;
                        }
                    }
                
                    foreach ($attrGroupArray as $key => $item) {
                    
                    }
                    exit;
                    if (!isset($attrGroupArray['4']) && !isset($attrGroupArray['5'])) {
                    
                    }elseif (!isset($attrGroupArray['4'])) {
                        foreach ($attrGroupArray['5'] as $key => $item) {
                            $insertCombin = '0';
                            $productCombinationAll = $productModels->productCombinationAll(["pa.id_product" => $id]);
                            foreach ($productCombinationAll as $combination) {
                                if ($combination->attr_id == $item['value']) {
                                    $insertCombin = '1';
                                }
                            }
                            if ($insertCombin == '0') {
                                $combinArray[$key]['5'] = $item['value'];
                            }
                        }
                    }elseif (!isset($attrGroupArray['5'])) {
                        foreach ($attrGroupArray['4'] as $key => $item) {
                            $insertCombin = '0';
                            $productCombinationAll = $productModels->productCombinationAll(["pa.id_product" => $id]);
                            foreach ($productCombinationAll as $combination) {
                                if ($combination->attr_id == $item['value']) {
                                    $insertCombin = '1';
                                }
                            }
                            if ($insertCombin == '0') {
                                $combinArray[$key]['4'] = $item['value'];
                            }
                        }
                    }else{
                        foreach ($attrGroupArray['4'] as $keyParent => $row) {
                                $i = 0;
                            foreach ($attrGroupArray['5'] as $key => $item) {
                                $insertCombin = '0';
                                $productCombinationAll = $productModels->productCombinationAll(["pa.id_product" => $id]);
                                foreach ($productCombinationAll as $combination) {
                                    if ($combination->attr_id == $item['value']. ' - ' .$row['value'] || $combination->attr_id == $row['value']. ' - ' .$item['value'] ) {
                                        $insertCombin = '1';
                                    }
                                }
                                if ($insertCombin == '0') {
                                    $combinArray[$keyParent][$key]['4'] = $row['value'];
                                    $combinArray[$keyParent][$key]['5'] = $item['value'];
                                }
                            }
                        }
                    }
                */
                if ($standart_kdv_rate == '-1') {
                    $kdv = $special_kdv_rate;
                }else{
                    $kdv = $standart_kdv_rate;
                }
                if (!$slug) {
                    $slug = sef_link($title);
                }else{
                    $slug = sef_link($slug);
                }
                $productInsertData = [
                    "title" => $title,
                    "category_id" => $parent_id,
                    "description" => $description,
                    "brand_id" => $brand_id,
                    "tax_rate" => $kdv,
                    "sale_price" => $sale_price_kdv,
                    "basket_price" => $basket_price_kdv,
                    "discount_price" => $discount_price_kdv,
                    "outlet_price" => $outlet_price_kdv,
                    "seo_title" => $seo_title,
                    "seo_description" => $seo_description,
                    "slug" => $slug,
                    "is_active" => $is_active,
                    "created_at" => created_at()
                ];
                $updateCategory = $productModels->edit($id,$productInsertData);
                if ($updateCategory) {
                    $product_id = $id;
                    if ($product_id) {
                        $upload_id = session()->get("uploadProductCode");
                        if ($upload_id) {
                            $updateProductImage = $productModels->pictureEditSession($upload_id, ['product_id' => $product_id, "upload_id" => NULL]);
                            if ($updateProductImage) {
                                session()->remove('uploadProductCode');
                            }
                        }
                        $productFeatureModels->deleteRow(['product_id' => $id], 'attribute_group_id NOT IN("5","4")');
                        foreach ($productFeature as $key => $item) {
                            if ($item) {
                                $productFeatureData = [
                                        "product_id" => $product_id,
                                        "attribute_group_id" => $key,
                                        "attribute_id" => $item
                                ];
                                
                                $productFeatureModels->add($productFeatureData);
                            }
                        }
                    }

                    foreach ($newAttrArray as $row) {
                        $productAttrInsertData = [
                            "id_product" => $product_id,
                            "created_at" => created_at()
                        ];
                        $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                        $product_attr_id = $db->insertID();
                        foreach ($row as $key => $item) {
                         
                            $productattributecombinationData = [
                                "attribute_id" => $item,
                                "attribute_product_id" => $product_attr_id,
                            ];
                            $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                            $combine_id = $db->insertID();

                            $productFeatureData = [
                                "product_id" => $product_id,
                                "product_attribute_id" => $product_attr_id,
                                "attribute_group_id" => $key,
                                "attribute_id" =>$item
                            ];
                            $productFeatureModels->add($productFeatureData);
                        }
                    }
                    /*
                        if (!isset($attrGroupArray['4']) && !isset($attrGroupArray['5'])) {
                        
                        }elseif (!isset($attrGroupArray['4'])) {
                            foreach ($combinArray as $row) {
                                $productAttrInsertData = [
                                    "id_product" => $product_id,
                                    "created_at" => created_at()
                                ];
                                $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                                $product_attr_id = $db->insertID();
                                foreach ($row as $item) {
                                
                                    $productattributecombinationData = [
                                        "attribute_id" => $item,
                                        "attribute_product_id" => $product_attr_id,
                                    ];
                                    $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                                    $combine_id = $db->insertID();

                                    $productFeatureData = [
                                        "product_id" => $product_id,
                                        "product_attribute_id" => $product_attr_id,
                                        "attribute_group_id" => '4',
                                        "attribute_id" =>$item
                                    ];
                                    $productFeatureModels->add($productFeatureData);
                                }
                            }
                        }elseif (!isset($attrGroupArray['5'])) {
                            foreach ($combinArray as $row) {
                                $productAttrInsertData = [
                                    "id_product" => $product_id,
                                    "created_at" => created_at()
                                ];
                                $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                                $product_attr_id = $db->insertID();
                                foreach ($row as $item) {
                                
                                    $productattributecombinationData = [
                                        "attribute_id" => $item,
                                        "attribute_product_id" => $product_attr_id,
                                    ];
                                    $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                                    $combine_id = $db->insertID();

                                    $productFeatureData = [
                                        "product_id" => $product_id,
                                        "product_attribute_id" => $product_attr_id,
                                        "attribute_group_id" => '5',
                                        "attribute_id" =>$item
                                    ];
                                    $productFeatureModels->add($productFeatureData);
                                }
                            }
                        }else{
                            foreach ($combinArray as $row) {
                                
                                foreach ($row as $item) {
                                    $productAttrInsertData = [
                                        "id_product" => $product_id,
                                        "created_at" => created_at()
                                    ];
                                    $insertProductAttr = $productModels->productAttrInsert($productAttrInsertData);
                                    $product_attr_id = $db->insertID();
                                    foreach ($item as $key => $lastItem) {
                                        $productattributecombinationData = [
                                            "attribute_id" => $lastItem,
                                            "attribute_product_id" => $product_attr_id,
                                        ];
                                        $productattributecombination = $productModels->productattributecombination($productattributecombinationData);
                                        $combine_id = $db->insertID();

                                        $productFeatureData = [
                                            "product_id" => $product_id,
                                            "product_attribute_id" => $product_attr_id,
                                            "attribute_group_id" => $key,
                                            "attribute_id" =>$lastItem
                                        ];
                                        $productFeatureModels->add($productFeatureData);
                                    }
                                }
                            }
                        }
                    */
                    if (isset($barcodeNo)) {
                        foreach ($barcodeNo as $key => $row) {
                            $productCombinationUpdateData = [
                                "barcode_no" => $row,
                            ];
                            $productCombinationUpdate = $productModels->productCombinationUpdate(['id' => $key], $productCombinationUpdateData);
                        }  
                    }
                    if (isset($stockNo)) {
                        foreach ($stockNo as $key => $row) {
                            $productCombinationUpdateStockData = [
                                "stock" => $row,
                            ];
                            $productCombinationUpdate = $productModels->productCombinationUpdate(['id' => $key], $productCombinationUpdateStockData);
                        }
                    }

                    $data['success'] = "Ürün düzenleme işlemi başarılı bir şekilde yapıldı.";
                    
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
                    
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
            return json_encode($data);
        }
        
        public function productCombinationDefault()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $value = $this->request->getPost('value');
            $db = db_connect();
            $productModels = new ProductModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $productModels->productCombinationOne([
                    "pa.id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $productModels->productCombinationUpdate(["id" => $id], $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";

                        
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

                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function productCombinationEdit()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $standart_kdv_rate = $this->request->getPost('standart_kdv_rate');
            $special_kdv_rate = $this->request->getPost('special_kdv_rate');
            $sale_price = $this->request->getPost('sale_price_kdv');
            $discount_price = $this->request->getPost('discount_price_kdv');
            $basket_price = $this->request->getPost('basket_price_kdv');
            $outlet_price = $this->request->getPost('outlet_price_kdv');
            $pictureSelect = $this->request->getPost('pictureSelect');
            $pictureSelectCover = $this->request->getPost('pictureSelectCover');
            $db = db_connect();
            $productModels = new ProductModels($db);
            if (!$validation->check($standart_kdv_rate, 'required') ) {
                $data['error'] =  'Lütfen standart satiş fiyatı alanını doldurunuz.';
            }elseif(!$validation->check($standart_kdv_rate, 'numeric')){
                $data['error'] =  'Lütfen kdv oranını düzgün bir biçimde giriniz.';
            }else{
                $categoryCheack = $productModels->productCombinationOne([
                    "pa.id" => $id 
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Düzenlemek değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    if ($standart_kdv_rate == '-1') {
                        $kdv = $special_kdv_rate;
                    }else{
                        $kdv = $standart_kdv_rate;
                    }
                    $dataInsert = [
                        "tax_rate" => $kdv,
                        "sale_price" => $sale_price,
                        "discount_price" => $discount_price,
                        "basket_price" => $basket_price,
                        "outlet_price" => $outlet_price,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $productModels->productCombinationUpdate(["id" => $id], $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Varyasyon düzenleme işlemi başarılı bir şekilde yapıldı.";
                        $dataInsert = [
                            "default_on" => '0',
                            "updated_at" => created_at(),
                        ];
                        $orderCombinationColor = $productModels->orderCombinationColorSelect([ 'pa.id_product' => $categoryCheack->id_product, 'ag.is_color' => '1', 'ag.is_combination' => '1', 'pac.attribute_id' => $categoryCheack->attribute_id]);
                        foreach ($orderCombinationColor as $item) {
                            $deleteAttributePicture = $productModels->attributePictureDelete(["product_attribute_id " => $item->id]);
                        }
                        foreach ($pictureSelect as $row) {
                            $combinationImage = $productModels->c_one_image(['id' => $row]);
                            if (in_array($row, $pictureSelectCover)) {
                                $combinationImage = $productModels->pictureEdit($row, ['is_cover' => '1']);
                            }else {
                                $combinationImage = $productModels->pictureEdit($row, ['is_cover' => '0']);
                            }
                            foreach ($orderCombinationColor as $item) {
                                $attributePictureDate = [
                                    "product_attribute_id" => $item->id,
                                    "product_image_id" => $row,
                                    "rank" => $combinationImage->rank,
                                ];
                                $attributePicture = $productModels->attributePictureSelect($attributePictureDate);
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

                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }
        
        public function productAttributeDelete()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('value');
            $db = db_connect();
            $productModels = new ProductModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $categoryCheack = $productModels->productCombinationOne([
                    "pa.id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz Varyant mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteAttribute = $productModels->productAttributeDelete($id);
                    if ($deleteAttribute) {
                        $data['success'] = "Varyant başarılı bir şekilde silindi.";
                        
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
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }
        
        public function status()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $value = $this->request->getPost('value');
            $db = db_connect();
            $productModels = new ProductModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $productModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $productModels->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                        
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
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function delete()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('value');
            $db = db_connect();
            $productModels = new ProductModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $categoryCheack = $productModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz Ürün mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteCategory = $productModels->delete($id);
                    if ($deleteCategory) {
                        $data['success'] = "Ürün başarılı bir şekilde silindi.";
                        
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
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function imageUpload()
        {
            helper('text');
            $avatar = $this->request->getFile('file');
            $imageName = $avatar->getRandomName();
            $imageExt = $avatar->getClientExtension();

            if (!session()->get("uploadProductCode") ) {
                $upload_id = random_string("crypto", 15);
                session()->set("uploadProductCode", $upload_id );
            }else{
                $upload_id = session()->get("uploadProductCode");
            }

            $image = \Config\Services::image()
            ->withFile($this->request->getFile('file'))
            ->resize(1200, 1800, true, 'width')
            ->save("./uploads/products/".$imageName);

            $image = \Config\Services::image()
            ->withFile($this->request->getFile('file'))
            ->fit(500, 750, 'center')
            ->convert(IMAGETYPE_WEBP)
            ->save("./uploads/products/min/".$imageName);

            $image = \Config\Services::image()
            ->withFile($this->request->getFile('file'))
            ->fit(250, 375, 'center')
            ->convert(IMAGETYPE_WEBP)
            ->save("./uploads/products/cat/".$imageName);

            $image = \Config\Services::image()
            ->withFile($this->request->getFile('file'))
            ->fit(50, 75, 'center')
            ->convert(IMAGETYPE_WEBP)
            ->save("./uploads/products/thumb/".$imageName);

            $db = db_connect();
            $productModels = new ProductModels($db);

            $dataInsert = [
                "product_id" => '0',
                "upload_id" => $upload_id,
                "image" => $imageName,
                "is_active" => '1',
                "is_cover" => '0',
                "created_at" => created_at(),
            ];
            $insertProductPicture = $productModels->pictureAdd($dataInsert);
            $last_id = $db->insertID();
            if ($insertProductPicture) {
                $data['success'] = "Resim başarılı bir şekilde kaydedildi.";
                $data['upload'] = "uploads/products/min/".$imageName;
                $data['uploadID'] = $last_id;
                
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
            }else{
                $data['error'] = "Beklenmeyen bir hata oluştu ve resim kaydedilemedi.";
            }
            return json_encode($data);
        }

        public function imageDelete()
        {
            $upload_id = $this->request->getPost('value');
            $db = db_connect();
            $productModels = new ProductModels($db);
            $thisCheack = $productModels->c_one_image([
                "id" => $upload_id,
            ]);
            if (!$thisCheack) {
                $data['error'] = "Silmek istediğiniz resim bulunamadı.";
            }else{
                $thisDelete = $productModels->deleteImage($upload_id);
                if ($thisDelete) {
                    $data['success'] = "Resim başarılı bir şekilde silindi.";
                    
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
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
           
            return json_encode($data);
        }

        public function imageDeleteEdit()
        {
            $upload_id = $this->request->getPost('value');
            $db = db_connect();
            $productModels = new ProductModels($db);
            $thisCheack = $productModels->c_one_image([
                "id" => $upload_id,
            ]);
          
            if (!$thisCheack) {
                $data['error'] = "Silmek istediğiniz resim bulunamadı.";
            }else{
               
                $thisDelete = $productModels->deleteImage($upload_id);
                if ($thisDelete) {
                    $data['success'] = "Resim başarılı bir şekilde silindi.";
                    
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
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
           
            return json_encode($data);
        }

        public function pictureRankChange()
        {
            helper('text');
            $pictureRank = $this->request->getPost('deleteItemArea');
            $db = db_connect();
            $productModels = new ProductModels($db);
            foreach ($pictureRank as $key => $row) {
                $updateDate = [
                    'rank' => $key
                ];
                $pictureEdit = $productModels->pictureEdit($row, $updateDate);
            }
            $data['success'] = 'Resim Sıralaması başarılı bir şekilde düzenlendi.';
            
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
            return json_encode($data);
        }
        
    }
