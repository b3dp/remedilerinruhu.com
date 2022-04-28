<?php

namespace App\Controllers;

use App\Libraries\LoadView;
use App\Models\Category;
use App\Models\ProductFilterModels;
use App\Models\ProductDetailModels;
use App\Models\UserModels;
use App\Models\OrderModels;
use App\Models\AttributeGroupModels;
use App\Models\AttributeModels;
use App\Models\CampaignModels;
use App\Models\CouponModels;
use App\Models\BasketModels;
class Basket extends LoadView
{
	
	public function index () {
		$data = $this->data;
		$this->session = \Config\Services::session();
		$db =  db_connect();
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$campaignModels = new CampaignModels($db);
		$orderModels = new OrderModels($db);
		$data['headerBasketDisabled'] = '1';
		$order = $this->session->get('order');
		$order_product = $this->session->get('order.product');
		foreach ($order_product as $row) {
			$thisProductFind = $productDetailModels->c_one(['p.id' => $row['id'], 'p.is_active' => '1']);
			if ($row['variant_id']) {
				$thisVariantFind = $productDetailModels->productCombinationOne(['pa.id_product' => $row['id'], 'pa.is_active' => '1', 'pa.id' => $row['variant_id']]);
			}

			$productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' =>$thisVariantFind->id], 1);
            if (!$productPicture) {
                $productPicture = $productDetailModels->c_all_image(['product_id' => $row['id']], 1);
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

			if ($thisProductFind->campaign_id) {
				$productCampaign = $campaignModels->c_one_index(['id' => $thisProductFind->campaign_id, 'is_active' => '1']);
			}
			//////////////////// Product Price Select Area Start ///////////////////
	
			if ($thisVariantFind->sale_price) {
                $priceArray = priceAreaFunction($thisVariantFind->sale_price, $thisVariantFind->discount_price, $thisVariantFind->basket_price, $productCampaign->discount);
            }else{
                $priceArray = priceAreaFunction($thisProductFind->sale_price, $thisProductFind->discount_price, $thisProductFind->basket_price, $productCampaign->discount);
            }
			if ($priceArray['discountPrice']) {
				$headerBasketPrice = $priceArray['discountPrice'];
			}else {
				$headerBasketPrice = $priceArray['totalPrice'];
			}

			if ($priceArray['basketBool'] && $priceArray['basketPrice']) {
				$last_price = $priceArray['basketPrice'];
			}elseif ($priceArray['discountBool'] && $priceArray['discountPrice']) {
				$last_price = $priceArray['discountPrice'];
			}else{
				$last_price = $priceArray['totalPrice'];
			}
			if ($thisVariantFind->tax_rate) {
				$vat_rate = $thisVariantFind->tax_rate;
			}else{
				$vat_rate = $thisProductFind->tax_rate;
			}
			/////////////////// Product Price Select Area End ///////////////////
			
			//////////////////// Product Title Select Area Start ///////////////////
				$featureArray = explode(' - ', $thisProductFind->attr);
				$categoryArray = explode(',', $thisProductFind->category_id);
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
				if ($thisVariantFind->title) {
					$title = $thisVariantFind->title;
				}else{
					$title = $thisProductFind->title;
				}
				$link = $thisProductFind->slug.'-p-'.$thisVariantFind->barcode_no;
			//////////////////// Product Title Select Area End ///////////////////
			$productDataView = [
				'product_id' => $row['id'],
				'variant_id' => $row['variant_id'],
				'image' => $image,
				'title' => $title,
				'link' => $link,
				'size_title' => $thisVariantFind->attr_size,
				'color_title' => $thisVariantFind->attr_color,
				'max_stock' => $thisVariantFind->stock,
				'piece' => $row['piece'],
				'vat_rate' => $row['vat_rate'],
				'coupon_rate' => $row['coupon_rate'],
				'coupon_discount_type' => $row['coupon_discount_type'],
				'total_price' => $priceArray['totalPrice'],
				'discount_price' => $priceArray['discountPrice'],
				'basket_price' => $priceArray['basketPrice'],
				'basketRate' => $priceArray['basketRate'],
				'last_price' => $row['last_price'],
				'price_coupon' => $row['price_coupon'],
			]; 
			foreach ($row['combanition'] as $key => $value) {
				$productDataView['combanition'][$key]['id'] = $value['id'];
				$productDataView['combanition'][$key]['title'] = $value['title'];
				$productDataView['combanition'][$key]['group_title'] = $value['group_title'];
				$productDataView['combanition'][$key]['group_id'] = $value['group_id'];
			}
			$data['basketProduct'][$row['variant_id'] ? $row['variant_id'] : $row['id']] = $productDataView;
		}
		$data['couponCode'] = $order['coupon_code'];
		$this->viewLoad('basket', $data);
	}

	public function productBasketAdd()
	{
        $db =  db_connect();
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$attributeModels = new AttributeModels($db);
		$orderModels = new OrderModels($db);
		$couponModels = new CouponModels($db);
		$campaignModels = new CampaignModels($db);
		$basketModels = new BasketModels($db);

		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_product = $this->session->get('order.product');
		if (!$order['order_no']) {
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
			$this->session->set('order', ["order_no" => $order_no]);
		}else{
			$order_no = $order['order_no'];
		}

		$delivery_options_first = $orderModels->delivery_c_one(['is_default' => '1', 'is_active' => '1']);

        $combination = $this->request->getPost('combination');
        $variant_id = $this->request->getPost('variant_id');
        $product_id = $this->request->getPost('product_id');
        $select_piece = $this->request->getPost('select_piece');
		if ($this->data['user_id']) {
            $user_id = $this->data['user_id'];
        }
		$selectPiece = $select_piece + $order_product[$variant_id ? $variant_id : $product_id]['piece'];
		if ($select_piece) {
			$selectPieceControl = $select_piece + $order_product[$variant_id ? $variant_id : $product_id]['piece'];
		}else{
			$selectPieceControl = 1 + $order_product[$variant_id ? $variant_id : $product_id]['piece'];
		}
		$thisProductFind = $productDetailModels->c_one(['p.id' => $product_id, 'p.is_active' => '1']);
		$thisProductOrderFind = $orderModels->c_one(['user_id' => $user_id, 'order_no' => $order_no]);
		if ($variant_id) {
			$thisVariantFind = $productDetailModels->productCombinationOne(['pa.id_product' => $product_id, 'pa.is_active' => '1', 'pa.id' => $variant_id]);
			$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'user_id' => $user_id, 'product_id' => $product_id, 'variant_id' => $variant_id]);
			$maxPiece = $thisVariantFind->stock;
		}else{
			$maxPiece = $thisProductFind->stock;
			$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'user_id' => $user_id, 'product_id' => $product_id]);
		}

		$productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' => $variant_id ], 1);
		if (!$productPicture) {
			$productPicture = $productDetailModels->c_all_image(['product_id' => $product_id], 1);
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
		if ($thisProductFind->campaign_id) {
			$productCampaign = $campaignModels->c_one_index(['id' => $thisProductFind->campaign_id, 'is_active' => '1']);
		}

		//////////////////// Product Price Select Area Start ///////////////////
	
			if ($thisVariantFind->sale_price) {
                $priceArray = priceAreaFunction($thisVariantFind->sale_price, $thisVariantFind->discount_price, $thisVariantFind->basket_price, $productCampaign->discount);
            }else{
                $priceArray = priceAreaFunction($thisProductFind->sale_price, $thisProductFind->discount_price, $thisProductFind->basket_price, $productCampaign->discount);
            }
			
			if ($priceArray['discountPrice']) {
				$headerBasketPrice = $priceArray['discountPrice'];
				$headerBasketPriceView = $priceArray['totalPrice'];
			}else {
				$headerBasketPrice = $priceArray['totalPrice'];
				$headerBasketPriceView = $priceArray['totalPrice'];
			}



			if ($priceArray['discountBool'] && $priceArray['discountPrice']) {
				$last_price = $priceArray['discountPrice'];
				$last_price_session = $priceArray['discountPrice'];
			}else{
				$last_price = $priceArray['totalPrice'];
				$last_price_session = $priceArray['totalPrice'];
			}

			if ($priceArray['basketBool'] && $priceArray['basketPrice']) {
				$basketPrice = $priceArray['basketPrice'];
				$basketRate = $priceArray['basketRate'];
				$last_price_session = $priceArray['basketPrice'];
			}

			if ($thisVariantFind->tax_rate) {
				$vat_rate = $thisVariantFind->tax_rate;
			}else{
				$vat_rate = $thisProductFind->tax_rate;
			}
		/////////////////// Product Price Select Area End ///////////////////

		//////////////////// Product Title Select Area Start ///////////////////
			$featureArray = explode(' - ', $thisProductFind->attr);
			$categoryArray = explode(',', $thisProductFind->category_id);
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
			if ($thisVariantFind->title) {
				$title = $thisVariantFind->title;
			}else{
				$title = $thisProductFind->title;
			}
			$link = $thisProductFind->slug.'-p-'.$thisVariantFind->barcode_no;
		//////////////////// Product Title Select Area End ///////////////////

		if (!$thisProductFind ) {
			$data['error'] = 'Sepete eklemek istediğiniz ürün bulunamadı';
		}elseif (!$thisVariantFind && $variant_id) {
			$data['error'] = 'Sepete eklemek istediğiniz ürün bulunamadı';
		}elseif ($maxPiece < $selectPieceControl) {
			$data['error'] = 'Maksimum '.$maxPiece.' Adet ürün seçebilirsiniz.';
		}elseif ($maxPiece < 1) {
			$data['error'] = 'Eklemek istediğinizi ürün stoklarımızda mevcut değildir.';
		}else{
			$data['success'] = 'Ürün sepetinize başarılı bir şekilde eklendi.';
			if ($data['success']){
				if ($select_piece) {
					$newPiece = $selectPiece;
				}else{
					$newPiece = $selectPiece + 1;
				}
				$basketArray = [
					'id' => $product_id,
					'variant_id' => $variant_id,
					'variant_barcode' => $thisVariantFind->barcode_no,
					'image' => $image,
					'title' => $title,
					'link' => $link,
					'max_stock' => $thisVariantFind->stock,
					'header_basket_price' => $headerBasketPrice,
					'basket_price' => $basketPrice,
					'basketRate' => $basketRate,
					'last_price' => $last_price_session,
					'vat_rate' => $vat_rate,
					'piece' => $newPiece,
				];
				foreach ($combination as $key => $value) {
					$attrGroupFind = $attributeModels->c_one(['a.id' => $value, 'a.is_active' => '2']);
					$basketArray['combanition'][$key]['id'] = $attrGroupFind->id;
					$basketArray['combanition'][$key]['title'] = $attrGroupFind->title;
					$basketArray['combanition'][$key]['group_title'] = $attrGroupFind->ag_title;
					$basketArray['combanition'][$key]['group_id'] = $attrGroupFind->ag_id;
				}
				$basketDatebaseData = [
					'user_id' => $user_id,
					'product_id' => $product_id,
					'variant_id' => $variant_id,
					'color_id' => $color_id,
					'size_id' => $size_id,
					'piece' => $newPiece,
				];
				if ($user_id) {
					$basketFind = $basketModels->c_one(['user_id' => $user_id, 'product_id' => $product_id, 'variant_id' => $variant_id]);
					if ($basketFind) {
						$basketModels->edit($basketFind->id, $basketDatebaseData);
					}else{
						$basketModels->add($basketDatebaseData);
					}
				}
				$_SESSION['order']['product'][$variant_id ? $variant_id : $product_id] = $basketArray;
				$data['basketCount'] = count($_SESSION['order']['product']);
				$productDataView = [
					'id' => $variant_id ? $variant_id : $product_id,
					'product_id' => $product_id,
					'variant_id' => $variant_id,
					'image' => $image,
					'title' => $title,
					'link' => $link,
					'max_stock' => $thisVariantFind->stock,
					'kdv' => $vat_rate,
					'piece' => $newPiece,
					'basket_price' => number_format(($basketPrice * $newPiece), 2),
					'basketRate' => $basketRate,
					'header_basket_price' => number_format(($headerBasketPriceView * $newPiece), 2),
					'last_price' => number_format(($last_price * $newPiece), 2)
				]; 
				foreach ($combination as $key => $value) {
					$attrGroupFind = $attributeModels->c_one(['a.id' => $value, 'a.is_active' => '2']);
					$productDataView['combanition'][$key]['id'] = $attrGroupFind->id;
					$productDataView['combanition'][$key]['title'] = $attrGroupFind->title;
					$productDataView['combanition'][$key]['group_title'] = $attrGroupFind->ag_title;
					$productDataView['combanition'][$key]['group_id'] = $attrGroupFind->ag_id;
				}
				$data['basketProduct'] = $productDataView;

				if ($_SESSION['order']['coupon_id']) {

					$product_coupon_id = $variant_id ? $variant_id : $product_id;
					$couponFind = $couponModels->c_one(['id' => $_SESSION['order']['coupon_id'], 'is_active' => '1']);
					$userArray = explode(',', $couponFind->user_id);
					$categoryArray = explode(',', $couponFind->category_id);
					$brandArray = explode(',', $couponFind->brand_id);
					$productArray = explode(',', $couponFind->product_id);
					$categoryFindBool = TRUE;
					$brandFindBool = TRUE;
					$productFindBool = TRUE;
					$productDiscountBool = TRUE;
					$thisProductFind = $productDetailModels->c_one(['p.id' => $product_id, 'p.is_active' => '1']);
					$categoryProductArray = explode(',', $thisProductFind->category_id);

					foreach ($categoryProductArray as $item) {
						foreach ($categoryArray as $value) {
							if ($item == $value) {
								if ($couponFind->brand_id && $brandArray) {
									if (in_array($thisProductFind->brand_id, $brandArray)) {
										$categoryFindBool = FALSE;
									}
								}else{
									$categoryFindBool = FALSE;
								}
							}
						}
					}
					foreach ($brandArray as $item) {
						if ($item == $thisProductFind->brand_id) {
							if ($couponFind->category_id && $categoryArray && $categoryArray == FALSE) {
								$brandFindBool = FALSE;
							}else{
								$brandFindBool = FALSE;
							}
						}
					}
	
					foreach ($productArray as $item) {
						if ($item == $thisProductFind->id) {
							if ($couponFind->product_id && $productArray) {
								$productFindBool = FALSE;
							}else{
								$productFindBool = FALSE;
							}
						}
					}

					if (($categoryArray && $couponFind->category_id)) {
						if ($categoryFindBool == FALSE) {
							$productDiscountBool = FALSE;
						}else{
							$productDiscountBool = TRUE;
						}
					}
					
					if (($brandArray && $couponFind->brand_id)) {
						if (($categoryArray && $couponFind->category_id)) {
							if ($productDiscountBool == FALSE) {
								if ($brandFindBool == FALSE) {
									$productDiscountBool = FALSE;
								}else{
									$productDiscountBool = TRUE;
								}
							}
						}else{
							if ($brandFindBool == FALSE) {
								$productDiscountBool = FALSE;
							}else{
								$productDiscountBool = TRUE;
							}
						}
					}

					if (($productArray && $couponFind->product_id)) {
						if ($productFindBool == FALSE) {
							$productDiscountBool = FALSE;
						}else{
							$productDiscountBool = TRUE;
						}
					}

					if ($productDiscountBool == FALSE && !isset($row['coupon_id'])) {
						if ($couponFind->discount_type == '1') {
							$discount_price = fiyatHesaplamaNot($last_price, $couponFind->discount);
							$last_price = $last_price - $discount_price;
							$coupon_discount_total = $coupon_discount_total + ($last_price * $newPiece  - $last_price * $newPiece);
						}else{
							$last_price = $last_price;
						}
						$coupon_id = $couponFind->id;
						$coupon_discount_type = $couponFind->discount_type;
						$coupon_discount = $last_price - $last_price;
						
						$coupon_rate = $couponFind->discount;
						$newArray = [
							'id' => $product_id,
							'variant_id' => $variant_id,
							'variant_barcode' => $thisVariantFind->barcode_no,
							'image' => $image,
							'title' => $title,
							'link' => $link,
							'max_stock' => $thisVariantFind->stock,
							'header_basket_price' => $headerBasketPrice,
							'last_price' => $last_price,
							"coupon_id" => $coupon_id,
							"coupon_discount_type" => $coupon_discount_type,
							"coupon_rate" => $coupon_rate,
							'vat_rate' => $vat_rate,
							'piece' => $newPiece,
							'color_id' => $color_id,
							'color_title' => $thisVariantFind->attr_color,
							'size_id' => $size_id,
							'size_title' => $thisVariantFind->attr_size
						];
						$_SESSION['order']['product'][$product_coupon_id] = $newArray;
					}
				}

				foreach ($_SESSION['order']['product'] as $row) {
					$headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
					$basketTotalPrice += $row['last_price'] * $row['piece'];
				}
				
				foreach ($_SESSION['order']['product'] as $key => $row) {
					if ($row['coupon_id']) {
						$totalDiscountPrice = $totalDiscountPrice + $row['last_price'] * $row['piece'];
					}
					if ($_SESSION['order']['coupon_discount_type'] == '2') {
						$price_coupon = (($row['last_price'] * $row['piece']) * $_SESSION['order']['coupon_discount_rate']) / $basketTotalPrice;
						$last_price = $row['last_price'];
						$coupon_id = $couponFind->id;
						$coupon_discount_type = $couponFind->discount_type;
						$coupon_discount = $row['last_price'] - $last_price;
						
						$coupon_rate = $couponFind->discount;
						$_SESSION['order']['product'][$key]['price_coupon'] = $price_coupon;
					}
				}
	
				if ($totalDiscountPrice) {
					if ($_SESSION['order']['coupon_discount_type'] == '1') {
						$coupon_discount = fiyatHesaplamaPlus($totalDiscountPrice, $_SESSION['order']['coupon_discount_rate']) - $totalDiscountPrice;
						$coupon_discount_total = $coupon_discount;
					}else{
						if ($totalDiscountPrice < $_SESSION['order']['coupon_discount_rate']) {
							$coupon_discount = $totalDiscountPrice;
						}else{
							$coupon_discount = $_SESSION['order']['coupon_discount_rate'];
						}
						$basketTotalPriceNew = $basketTotalPrice - $coupon_discount;
						$coupon_discount = $basketTotalPrice - $basketTotalPriceNew;
						$coupon_discount_total = $coupon_discount;
						$basketTotalPrice = $basketTotalPriceNew;
					}
				}

				if ($delivery_options_first->free_shipping_price >= $basketTotalPrice) {
					$basketTotalPrice = $basketTotalPrice + $delivery_options_first->shipping_price;
					$headerBasketPriceSesion = $headerBasketPriceSesion;
					$data['free_shipping_price'] = FALSE;
					$this->session->push('order', ['shipping_price' => $delivery_options_first->shipping_price]);
					$data['free_shipping'] = number_format($delivery_options_first->shipping_price , 2). ' TL';
					$data['free_shipping_price'] = $delivery_options_first->shipping_price;
				}else{
					$data['free_shipping_price'] = TRUE;
					$data['free_shipping_price'] = '';
					unset($_SESSION['order']['shipping_price']);
				}

				$data['basket_total_price_first'] = number_format($basketTotalPrice, 2);
				$this->session->push('order', ['header_basket_price' => $headerBasketPriceSesion]);
				$this->session->push('order', ['basket_total_price' => $basketTotalPrice]);
				$this->session->push('order', ['coupon_discount' => $coupon_discount_total]);
				$data['disconce_price'] = number_format(($headerBasketPriceSesion - $basketTotalPrice + $data['free_shipping_price']) , 2);
				$data['basket_total_price'] = number_format($basketTotalPrice, 2);
				$data['headerBasketPriceSesion'] = number_format($headerBasketPriceSesion, 2);
				getLogDate($user_id, '32', $product_id.'-'.$variant_id, 'Ürün-Variant ID', '');
			} 
		}
		echo json_encode($data);
	}

	public function basketProductPiece() {
		$db =  db_connect();
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$orderModels = new OrderModels($db);
		$campaignModels = new CampaignModels($db);
		$basketModels = new BasketModels($db);

		$delivery_options_first = $orderModels->delivery_c_one(['is_default' => '1', 'is_active' => '1']);
		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_product = $this->session->get('order.product');
		$order_no = $order['order_no'];

		$id = $this->request->getPost('id');
        $value = $this->request->getPost('value');

		if ($this->data['user_id']) {
            $user_id = $this->data['user_id'];
        }
		$thisProductOrderFind = $orderModels->c_one(['user_id' => $user_id, 'order_no' => $order_no]);
		$thisVariantFind = $productDetailModels->productCombinationOne(['pa.id' => $id, 'pa.is_active' => '1']);
		if (!$thisVariantFind) {
			$thisProductFind = $productDetailModels->c_one(['p.id' => $id, 'p.is_active' => '1']);
			$maxPiece = $thisProductFind->stock;
			$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'user_id' => $user_id, 'product_id' => $id]);
		}else{
			$thisProductFind = $productDetailModels->c_one(['p.id' => $thisVariantFind->id_product, 'p.is_active' => '1']);
			$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'user_id' => $user_id, 'product_id' => $thisVariantFind->id_product, 'variant_id' => $id]);
			$maxPiece = $thisVariantFind->stock;
		}

		if ($thisProductFind->campaign_id) {
			$productCampaign = $campaignModels->c_one_index(['id' => $thisProductFind->campaign_id, 'is_active' => '1']);
		}

		//////////////////// Product Price Select Area Start ///////////////////
		
			if ($thisVariantFind->sale_price) {
				$priceArray = priceAreaFunction($thisVariantFind->sale_price, $thisVariantFind->discount_price, $thisVariantFind->basket_price, $productCampaign->discount);
			}else{
				$priceArray = priceAreaFunction($thisProductFind->sale_price, $thisProductFind->discount_price, $thisProductFind->basket_price, $productCampaign->discount);
			}
			if ($priceArray['basketBool'] && $priceArray['basketPrice']) {
				$price = $priceArray['basketPrice'];
			}elseif ($priceArray['discountBool'] && $priceArray['discountPrice']) {
				$price = $priceArray['discountPrice'];
			}else{
				$price = $priceArray['totalPrice'];
			}
		/////////////////// Product Price Select Area End ///////////////////

		if (!$thisVariantFind && !$thisProductFind) {
			$data['error'] = 'Adet sayısını değiştirebileceğiniz ürün bulunamadı.';
		}elseif ($maxPiece < $value) {
			$data['error'] = 'En fazla '.$maxPiece.' adet ürün alabilirsiniz. ';
		}elseif ('1' > $value) {
			$data['error'] = 'En az bir adet ürün almanız gerekmektedir.';
		}else {
			$newPiece = $value;
			$newPrice = $price * $newPiece;
			$_SESSION['order']['product'][$id]['piece'] = $value;
			$_SESSION['order']['product'][$id]['price'] = $newPrice;
			if ($thisProductOrderFind && $thisProductOrderDetailFind) {
				$updateOrderDetailData = [
					"piece" => $value,
					"price" => $newPrice,
					"updated_at" => created_at(),
				];
				$insertOrderDetail = $orderModels->orderDetailUpdate($thisProductOrderDetailFind->id, $updateOrderDetailData);
			}
			$data['price'] = [
				'total_price' => number_format(floor(($priceArray['totalPrice'] * $newPiece *100))/100, 2),
				'discount_price' => number_format(floor(($priceArray['discountPrice'] * $newPiece *100))/100, 2),
				'basket_price' => number_format(floor(($priceArray['basketPrice'] * $newPiece * 100))/100, 2),
			];

			foreach ($_SESSION['order']['product'] as $row) {
				$headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
				$basketTotalPrice += $row['last_price'] * $row['piece'];
			}

			foreach ($_SESSION['order']['product'] as $key => $row) {
				if ($row['coupon_id']) {
					$totalDiscountPrice = $totalDiscountPrice + $row['last_price'] * $row['piece'];
				}
			}

			foreach ($_SESSION['order']['product'] as $key => $row) {
				if ($_SESSION['order']['coupon_discount_type'] == '2' && $_SESSION['order']['coupon_discount_min'] < $basketTotalPrice) {
					$price_coupon = (($row['last_price'] * $row['piece']) * $_SESSION['order']['coupon_discount_rate']) / $basketTotalPrice;
					$last_price = $row['last_price'];
					$coupon_id = $couponFind->id;
					$coupon_discount_type = $couponFind->discount_type;
					$coupon_discount = $row['last_price'] - $last_price;
					
					$coupon_rate = $couponFind->discount;
					$_SESSION['order']['product'][$key]['price_coupon'] = $price_coupon;
				}else{
					$_SESSION['order']['product'][$key]['price_coupon'] = '';
				}
			}

			foreach ($_SESSION['order']['product'] as $row) {
				if ($row['vat_rate']) {
					$data[vatRate][$row['vat_rate']] = number_format($data[vatRate][$row['vat_rate']] + vat_add(($row['last_price'] * $row['piece'] - $row['price_coupon']), $row['vat_rate']), 2);
					$totalVatRate = $totalVatRate + vat_add($row['last_price'], $row['vat_rate']);
				}
			}

			if ($totalDiscountPrice) {
				if ($_SESSION['order']['coupon_discount_type'] == '1') {
					$coupon_discount = fiyatHesaplamaPlus($totalDiscountPrice, $_SESSION['order']['coupon_discount_rate']) - $totalDiscountPrice;
					$coupon_discount_total = $coupon_discount;
				}else{
					if ($_SESSION['order']['coupon_discount_type'] == '2' && $_SESSION['order']['coupon_discount_min'] < $basketTotalPrice) {
						if ($totalDiscountPrice < $_SESSION['order']['coupon_discount_rate']) {
							$coupon_discount = $totalDiscountPrice;
						}else{
							$coupon_discount = $_SESSION['order']['coupon_discount_rate'];
						}
						$basketTotalPriceNew = $basketTotalPrice - $coupon_discount;
						$coupon_discount = $basketTotalPrice - $basketTotalPriceNew;
						$coupon_discount_total = $coupon_discount;
						$basketTotalPrice = $basketTotalPriceNew;
					}else{
						$data['discount_min_error'] = 'Kodu Kullanabilmeniz İçin sepetinizde '. number_format($_SESSION['order']['coupon_discount_min'], 2) .' TL ürün bulunması gerekmektedir.';
					}
				}
			}
			
			if ($delivery_options_first->free_shipping_price >= $basketTotalPrice) {
				$basketTotalPrice = $basketTotalPrice + $delivery_options_first->shipping_price;
				$headerBasketPriceSesion = $headerBasketPriceSesion;
				$this->session->push('order', ['shipping_price' => $delivery_options_first->shipping_price]);
				$data['free_shipping_price'] = FALSE;
				$data['free_shipping'] = number_format($delivery_options_first->shipping_price , 2). ' TL';
				$data['free_shipping_price'] = $delivery_options_first->shipping_price;
			}else{
				$data['free_shipping_price'] = TRUE;
				$data['free_shipping_price'] = '';
				unset($_SESSION['order']['shipping_price']);
			}

			$this->session->push('order', ['header_basket_price' => $headerBasketPriceSesion]);
			$this->session->push('order', ['basket_total_price' => $basketTotalPrice]);
			$data['headerBasketPriceSesion'] = number_format($headerBasketPriceSesion, 2);

			$data['basket_total_price_first'] = number_format($basketTotalPrice, 2);
			$data['disconce_price'] = number_format($headerBasketPriceSesion - $basketTotalPrice + $data['free_shipping_price'] , 2);
			$data['basket_total_price'] = number_format($basketTotalPrice, 2);
			$data['header_basket_price'] = number_format($headerBasketPriceSesion, 2);
			$this->session->push('order', ['coupon_discount' => $coupon_discount_total]);
			$data['coupon_price'] = number_format($coupon_discount_total, 2);
			$data['piece'] = $value;
			$data['coupon_discount_type'] = $_SESSION['order']['coupon_discount_type'];
			$data['success'] = 'Adet sayısı başarılı bir şekilde güncellendi.';
			
			$basketDatebaseData = [
				'user_id' => $user_id,
				'product_id' => $thisVariantFind->id_product,
				'variant_id' => $id,
				'piece' => $newPiece,
			];
			$basketFind = $basketModels->c_one(['user_id' => $user_id, 'product_id' => $thisVariantFind->id_product, 'variant_id' => $id]);
			if ($basketFind) {
				 $basketModels->edit($basketFind->id, $basketDatebaseData);
			}else{
				$basketModels->add($basketDatebaseData);
			}
		}
		echo json_encode($data);
	}
	
	public function basketProductDelete(){
		$db =  db_connect();
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$orderModels = new OrderModels($db);
		$basketModels = new BasketModels($db);
		$delivery_options_first = $orderModels->delivery_c_one(['is_default' => '1', 'is_active' => '1']);
		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_product = $this->session->get('order.product');
		$order_no = $order['order_no'];

		$id = $this->request->getPost('value');

		if ($this->data['user_id']) {
            $user_id = $this->data['user_id'];
        }

		$thisProductOrderFind = $orderModels->c_one(['user_id' => $user_id, 'order_no' => $order_no]);
		$thisVariantFind = $productDetailModels->productCombinationOne(['pa.id' => $id, 'pa.is_active' => '1']);
		if (!$thisVariantFind) {
			$thisProductFind = $productDetailModels->c_one(['p.id' => $id, 'p.is_active' => '1']);
			$price = $thisProductFind->sale_price;
			$maxPiece = $thisProductFind->stock;
			$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'user_id' => $user_id, 'product_id' => $id]);
		}else{
			$thisProductFind = $productDetailModels->c_one(['p.id' => $thisVariantFind->id_product, 'p.is_active' => '1']);
			$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'user_id' => $user_id, 'product_id' => $thisVariantFind->id_product, 'variant_id' => $id]);
			$maxPiece = $thisVariantFind->stock;
			$price = $thisVariantFind->sale_price;
		}

		if (!$thisVariantFind && !$thisProductFind) {
			$data['error'] = 'Sepetinizden çıkartabileceğiniz bir ürün bulunamadı.';
		}else {
			unset($_SESSION['order']['product'][$id]);
			$deleteOrderDetail = $orderModels->orderDetailDelete(['id' => $thisProductOrderDetailFind->id]);
			foreach ($_SESSION['order']['product'] as $row) {
				$headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
				$basketTotalPrice += $row['last_price'] * $row['piece'];
			}

			foreach ($_SESSION['order']['product'] as $key => $row) {
				if ($row['coupon_id']) {
					$totalDiscountPrice = $totalDiscountPrice + $row['last_price'] * $row['piece'];
				}
			}

			foreach ($_SESSION['order']['product'] as $key => $row) {
				if ($row['coupon_id']) {
					$totalDiscountPrice = $totalDiscountPrice + $row['last_price'] * $row['piece'];
				}
			}

			foreach ($_SESSION['order']['product'] as $key => $row) {
				if ($_SESSION['order']['coupon_discount_type'] == '2' && $_SESSION['order']['coupon_discount_min'] < $basketTotalPrice) {
					$price_coupon = (($row['last_price'] * $row['piece']) * $_SESSION['order']['coupon_discount_rate']) / $basketTotalPrice;
					$last_price = $row['last_price'];
					$coupon_id = $couponFind->id;
					$coupon_discount_type = $couponFind->discount_type;
					$coupon_discount = $row['last_price'] - $last_price;
					
					$coupon_rate = $couponFind->discount;
					$_SESSION['order']['product'][$key]['price_coupon'] = $price_coupon;
				}else{
					$_SESSION['order']['product'][$key]['price_coupon'] = '';
				}
			}
			
			foreach ($_SESSION['order']['product'] as $row) {
				if ($row['vat_rate']) {
					$data[vatRate][$row['vat_rate']] = number_format($data[vatRate][$row['vat_rate']] + vat_add(($row['last_price'] * $row['piece'] - $row['price_coupon']), $row['vat_rate']), 2);
					$totalVatRate = $totalVatRate + vat_add($row['last_price'], $row['vat_rate']);
				}
			}

			if ($totalDiscountPrice) {
				if ($_SESSION['order']['coupon_discount_type'] == '1') {
					$coupon_discount = fiyatHesaplamaPlus($totalDiscountPrice, $_SESSION['order']['coupon_discount_rate']) - $totalDiscountPrice;
					$coupon_discount_total = $coupon_discount;
				}else{
					if ($_SESSION['order']['coupon_discount_type'] == '2' && $_SESSION['order']['coupon_discount_min'] < $basketTotalPrice) {
						if ($totalDiscountPrice < $_SESSION['order']['coupon_discount_rate']) {
							$coupon_discount = $totalDiscountPrice;
						}else{
							$coupon_discount = $_SESSION['order']['coupon_discount_rate'];
						}
						$basketTotalPriceNew = $basketTotalPrice - $coupon_discount;
						$coupon_discount = $basketTotalPrice - $basketTotalPriceNew;
						$coupon_discount_total = $coupon_discount;
						$basketTotalPrice = $basketTotalPriceNew;
					}else{
						$data['discount_min_error'] = 'Kodu Kullanabilmeniz İçin sepetinizde '. number_format($_SESSION['order']['coupon_discount_min'], 2) .' TL ürün bulunması gerekmektedir.';
					}
				}
			}

			$data['disconce_price'] = number_format($headerBasketPriceSesion - $basketTotalPrice + $data['free_shipping_price'] , 2);
		
			if ($delivery_options_first->free_shipping_price >= $basketTotalPrice) {
				$basketTotalPrice = $basketTotalPrice + $delivery_options_first->shipping_price;
				$headerBasketPriceSesion = $headerBasketPriceSesion;
				$this->session->push('order', ['shipping_price' => $delivery_options_first->shipping_price]);
				$data['free_shipping_price'] = FALSE;
				$data['free_shipping'] = number_format($delivery_options_first->shipping_price , 2). ' TL';
				$data['free_shipping_price'] = $delivery_options_first->shipping_price;
			}else{
				$data['free_shipping_price'] = TRUE;
				$data['free_shipping_price'] = '';
				unset($_SESSION['order']['shipping_price']);
			}
			$data['basket_total_price_first'] = number_format($basketTotalPrice, 2);
			$basketFind = $basketModels->c_one(['user_id' => $user_id, 'variant_id' => $id]);
			if ($basketFind) {
				 $basketModels->deleteRow(['id' => $basketFind->id]);
			}

			$this->session->push('order', ['header_basket_price' => $headerBasketPriceSesion]);
			$this->session->push('order', ['basket_total_price' => $basketTotalPrice]);

			$data['header_basket_price'] = number_format($headerBasketPriceSesion, 2);
			$data['basket_total_price'] = number_format($basketTotalPrice, 2);
			$data['basketCount'] = count($_SESSION['order']['product']);
			$this->session->push('order', ['coupon_discount' => $coupon_discount_total]);
			$data['coupon_price'] = number_format($coupon_discount_total, 2);
			$data['headerBasketPriceSesion'] = number_format($headerBasketPriceSesion, 2);
			$data['success'] = 'Sepetinizdenki ürün başarılı bir şekilde çıkartıldı.';
			getLogDate($user_id, '33', $thisVariantFind->id_product.'-'.$id, 'Ürün-Variant ID', '');
		}
		echo json_encode($data);
	}
	
	public function promationCodeUse(){
		$db =  db_connect();

		$couponModels = new CouponModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$orderModels = new OrderModels($db);
		$delivery_options_first = $orderModels->delivery_c_one(['is_default' => '1', 'is_active' => '1']);
		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_product = $this->session->get('order.product');
		$order_no = $order['order_no'];

		$cartCouponCode = $this->request->getPost('cartCouponCode');

		if ($this->data['user_id']) {
            $user_id = $this->data['user_id'];
        }
		
		$couponFind = $couponModels->c_one(['code' => $cartCouponCode, 'is_active' => '1']);
		$orderCouponFind = $orderModels->c_one(['user_id' => $user_id, 'coupon_id' => $couponFind->id, 'status !=' => '99']);
		$categoryFindBool = TRUE;
		$brandFindBool = TRUE;
		$productFindBool = TRUE;
		$userArray = explode(',', $couponFind->user_id);
		$categoryArray = explode(',', $couponFind->category_id);
		$brandArray = explode(',', $couponFind->brand_id);
		$productArray = explode(',', $couponFind->product_id);
		foreach ($order_product as $row) {
			$thisProductFind = $productDetailModels->c_one(['p.id' => $row['id'], 'p.is_active' => '1']);
			$categoryProductArray = explode(',', $thisProductFind->category_id);

			foreach ($categoryProductArray as $item) {
				foreach ($categoryArray as $value) {
					if ($item == $value) {
						if ($couponFind->brand_id && $brandArray) {
							if (in_array($thisProductFind->brand_id, $brandArray)) {
								$categoryFindBool = FALSE;
							}
						}else{
							$categoryFindBool = FALSE;
						}
					}
				}
			}

			foreach ($brandArray as $item) {
				if ($item == $thisProductFind->brand_id) {
					if ($couponFind->category_id && $categoryArray && $categoryArray == FALSE) {
						$brandFindBool = FALSE;
					}else{
						$brandFindBool = FALSE;
					}
				}
			}

			foreach ($productArray as $item) {
				if ($item == $thisProductFind->id) {
					$productFindBool = FALSE;
				}
			}
		}
		if (!$user_id) {
			$data['error'] = 'Kupon kullamanız için önce giriş yapmanız gerekmektedir.';
			$data['location'] = 'giris-yap?return=sepetim';
		}elseif (!$couponFind) {
			$data['error'] = 'Girmiş olduğunuz kupon kodu bulunamadı.';
		}elseif ($order['coupon_id']) {
			$data['error'] = 'Sepet için daha önce bir kupon kodu kullanılmıştır.';
		}elseif ($orderCouponFind) {
			$data['error'] = 'Girmiş olduğunuz kupon kodu daha önce siparişinizde kullanılmıştır.';
		}elseif ($couponFind->user_id && $userArray && !in_array($user_id, $userArray) ) {
			$data['error'] = 'Kupon kodunu kullanma yetkiniz bulunmamaktadır.';
		}elseif ($couponFind->end_at && $couponFind->end_at < nowDate()) {
			$data['error'] = 'Kullanmak istediğiniz indirim kodunun son kullanma tarihi geçmiştir.';
		}elseif (($couponFind->category_id && $categoryArray ) && $categoryFindBool ) {
			$data['error'] = 'Girmiş olduğunuz kupon sepetinizde bulunan ürün kategorilerini veya markaları için kullanılamamktadır.';
		}elseif (($couponFind->brand_id && $brandArray) && $brandFindBool ) {
			$data['error'] = 'Girmiş olduğunuz kupon sepetinizde bulunan ürün kategorilerini veya markaları için kullanılamamktadır.';
		}elseif (($couponFind->product_id && $productArray) && $productFindBool ) {
			$data['error'] = 'Girmiş olduğunuz kupon sepetinizde bulunan ürünler ile eşleşmemektedir.';
		}elseif (($couponFind->discount_type == '2' && $couponFind->discount_min) && $_SESSION['order']['basket_total_price'] < $couponFind->discount_min) {
			$data['error'] = 'Sepette bulunan ürünlerin toplamı en az '. number_format($couponFind->discount_min, 2) .' TL olmalıdır.';
		}else{
			$coupon_count = '0';
			foreach ($order_product as $key => $row) {
				$categoryFindBool = TRUE;
				$brandFindBool = TRUE;
				$productFindBool = TRUE;
				$productDiscountBool = TRUE;
				$thisProductFind = $productDetailModels->c_one(['p.id' => $row['id'], 'p.is_active' => '1']);
				$categoryProductArray = explode(',', $thisProductFind->category_id);

				foreach ($categoryProductArray as $item) {
					foreach ($categoryArray as $value) {
						if ($item == $value) {
							if ($couponFind->brand_id && $brandArray) {
								if (in_array($thisProductFind->brand_id, $brandArray)) {
									$categoryFindBool = FALSE;
								}
							}else{
								$categoryFindBool = FALSE;
							}
						}
					}
				}

				foreach ($brandArray as $item) {
					if ($item == $thisProductFind->brand_id) {
						if ($couponFind->category_id && $categoryArray && $categoryArray == FALSE) {
							$brandFindBool = FALSE;
						}else{
							$brandFindBool = FALSE;
						}
					}
				}

				foreach ($productArray as $item) {
					if ($item == $thisProductFind->id) {
						$productFindBool = FALSE;
					}
				}

				if (($categoryArray && $couponFind->category_id) && $categoryFindBool == FALSE) {
					$productDiscountBool = FALSE;
				}
				
				if (($brandArray && $couponFind->brand_id) && $brandFindBool == FALSE) {
					$productDiscountBool = FALSE;
				}

				if (($productArray && $couponFind->product_id) && $productFindBool == FALSE) {
					$productDiscountBool = FALSE;
				}
				
				if ($couponFind->coupon_type == '0') {
					$productDiscountBool = FALSE;
				}
				
				if ($productDiscountBool == FALSE && !isset($row['coupon_id'])) {
					$totalDiscountPrice = $totalDiscountPrice + $row['last_price'] * $row['piece'];

					if ($couponFind->discount_type == '1') {
						$discount_price = fiyatHesaplamaNot($row['last_price'], $couponFind->discount);
						$last_price = $row['last_price'] - $discount_price;
						$coupon_discount_total = $coupon_discount_total + ($row['last_price'] * $row['piece']  - $last_price * $row['piece']);
					}else{
						$price_coupon = (($row['last_price'] * $row['piece']) * $couponFind->discount) / $_SESSION['order']['basket_total_price'];
						$last_price = $row['last_price'];
					}
					$coupon_id = $couponFind->id;
					$coupon_discount_type = $couponFind->discount_type;
					$coupon_discount = $row['last_price'] - $last_price;
					
					$coupon_rate = $couponFind->discount;
					$newArray = [
						"id" => $row['id'],
						"variant_id" => $row['variant_id'],
						"variant_barcode" => $row['variant_barcode'],
						"image" => $row['image'],
						"title" => $row['title'],
						"link" => $row['link'],
						"max_stock" => $row['max_stock'],
						"header_basket_price" => $row['header_basket_price'],
						"last_price" => $last_price,
						"coupon_id" => $coupon_id,
						"coupon_discount_type" => $coupon_discount_type,
						"coupon_discount" => $coupon_discount,
						"coupon_rate" => $coupon_rate,
						"vat_rate" => $row['vat_rate'],
						"piece" => $row['piece'],
						"color_id" => $row['color_id'],
						"color_title" => $row['color_title'],
						"size_id" => $row['size_id'],
						"size_title" => $row['size_title'],
						"price" => $row['price'],
						"price_coupon" => $price_coupon,
					];
					$_SESSION['order']['product'][$key] = $newArray;
				}else{
					if ($couponFind->discount_type == '2') {
						$price_coupon = (($row['last_price'] * $row['piece']) * $couponFind->discount) / $_SESSION['order']['basket_total_price'];
						$last_price = $row['last_price'];
						$coupon_id = $couponFind->id;
						$coupon_discount_type = $couponFind->discount_type;
						$coupon_discount = $row['last_price'] - $last_price;
						
						$coupon_rate = $couponFind->discount;
						$newArray = [
							"id" => $row['id'],
							"variant_id" => $row['variant_id'],
							"variant_barcode" => $row['variant_barcode'],
							"image" => $row['image'],
							"title" => $row['title'],
							"link" => $row['link'],
							"max_stock" => $row['max_stock'],
							"header_basket_price" => $row['header_basket_price'],
							"last_price" => $last_price,
							"coupon_id" => $coupon_id,
							"coupon_discount_type" => $coupon_discount_type,
							"coupon_discount" => $coupon_discount,
							"coupon_rate" => $coupon_rate,
							"vat_rate" => $row['vat_rate'],
							"piece" => $row['piece'],
							"color_id" => $row['color_id'],
							"color_title" => $row['color_title'],
							"size_id" => $row['size_id'],
							"size_title" => $row['size_title'],
							"price" => $row['price'],
							"price_coupon" => $price_coupon,
						];
						$_SESSION['order']['product'][$key] = $newArray;
					}
			
				}
			}

			foreach ($_SESSION['order']['product'] as $row) {
				$headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
				$basketTotalPrice += $row['last_price'] * $row['piece'];
			}

			if ($couponFind->id) {
				if ($couponFind->discount_type == '1') {
					$newPrice = fiyatHesaplamaNot($totalDiscountPrice, $couponFind->discount);
					$last_price = $newPrice;
					$coupon_discount = $coupon_discount_total;
				}else{
					if ($couponFind->discount_type == '2' && $couponFind->discount_min < $basketTotalPrice) {
						if ($totalDiscountPrice < $_SESSION['order']['coupon_discount_rate']) {
							$coupon_discount = $totalDiscountPrice;
						}else{
							$coupon_discount = $couponFind->discount;
						}
						$basketTotalPriceNew = $basketTotalPrice - $coupon_discount;
						$coupon_discount = $basketTotalPrice - $basketTotalPriceNew;
						$coupon_discount_total = $coupon_discount;
						$basketTotalPrice = $basketTotalPriceNew;
					}else{
						$data['discount_min_error'] = 'Kodu Kullanabilmeniz İçin sepetinizde '. number_format($_SESSION['order']['coupon_discount_min'], 2) .' TL ürün bulunması gerekmektedir.';
					}
				}
			}

			foreach ($_SESSION['order']['product'] as $row) {
				if ($row['vat_rate']) {
					$data[vatRate][$row['vat_rate']] = number_format($data[vatRate][$row['vat_rate']] + vat_add(($row['last_price'] * $row['piece'] - $row['price_coupon']), $row['vat_rate']), 2);
					$totalVatRate = $totalVatRate + vat_add($row['last_price'], $row['vat_rate']);
				}
			}

		
			
			if ($delivery_options_first->free_shipping_price >= $basketTotalPrice) {
				$basketTotalPrice = $basketTotalPrice + $delivery_options_first->shipping_price;
				$headerBasketPriceSesion = $headerBasketPriceSesion;
				$this->session->push('order', ['shipping_price' => $delivery_options_first->shipping_price]);
				$data['free_shipping_price'] = FALSE;
				$data['free_shipping'] = number_format($delivery_options_first->shipping_price , 2). ' TL';
				$data['free_shipping_price'] = $delivery_options_first->shipping_price;
			}else{
				$data['free_shipping_price'] = TRUE;
				$data['free_shipping_price'] = '';
				unset($_SESSION['order']['shipping_price']);
			}

			$this->session->push('order', ['header_basket_price' => $headerBasketPriceSesion]);
			$this->session->push('order', ['basket_total_price' => $basketTotalPrice]);
			
			$data['basket_total_price_first'] = number_format($basketTotalPrice, 2);
			$data['disconce_price'] = number_format($headerBasketPriceSesion - $basketTotalPrice +$data['free_shipping_price'] , 2);
			$data['basket_total_price'] = number_format($basketTotalPrice, 2);
			$data['header_basket_price'] = number_format($headerBasketPriceSesion, 2);
			$this->session->push('order', ['coupon_id' => $couponFind->id]);
			$this->session->push('order', ['coupon_discount_type' => $couponFind->discount_type]);
			$this->session->push('order', ['coupon_discount' => $coupon_discount_total]);
			$this->session->push('order', ['coupon_code' => $couponFind->code]);
			$this->session->push('order', ['coupon_discount_rate' => $couponFind->discount]);
			$this->session->push('order', ['coupon_discount_min' => $couponFind->discount_min]);
			$data['coupon_code'] = $couponFind->code;
			$data['coupon_price'] = number_format($coupon_discount_total, 2);
			$data['success'] = 'İndirim Kuponu başarılı bir şekilde eklendi.';

		}

		return json_encode($data);
	}

	public function promationCodeCanceled(){
		$db =  db_connect();

		$couponModels = new CouponModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$orderModels = new OrderModels($db);
		$delivery_options_first = $orderModels->delivery_c_one(['is_default' => '1', 'is_active' => '1']);
		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_product = $this->session->get('order.product');
		$order_no = $order['order_no'];

		unset($_SESSION['order']['coupon_id']);
		unset($_SESSION['order']['coupon_discount_type']);
		unset($_SESSION['order']['coupon_discount']);
		unset($_SESSION['order']['coupon_code']);
		unset($_SESSION['order']['coupon_discount_rate']);
		unset($_SESSION['order']['coupon_discount_min']);
		unset($_SESSION['order']['total_']);

		foreach ($order_product as $key => $row) {
			unset($_SESSION['order']['product'][$key]);
			$this->productUserLoginBasketAdd($row['color_id'], $row['size_id'], $row['id'] , $row['variant_id'], $row['piece'] , '');
		}
		$data['success'] = 'İndirim Kuponu başarılı bir şekilde kaldırıldı..';
		return json_encode($data);
	}
	
	public function productUserLoginBasketAdd($color_id, $size_id, $product_id, $variant_id, $select_piece, $user_id)
	{
        $db =  db_connect();
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$orderModels = new OrderModels($db);
		$couponModels = new CouponModels($db);
		$campaignModels = new CampaignModels($db);
		$basketModels = new BasketModels($db);

		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_product = $this->session->get('order.product');

		if (!$order['order_no']) {
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
			$this->session->set('order', ["order_no" => $order_no]);
		}else{
			$order_no = $order['order_no'];
		}

		$delivery_options_first = $orderModels->delivery_c_one(['is_default' => '1', 'is_active' => '1']);

		if ($this->data['user_id']) {
            $user_id = $this->data['user_id'];
        }
		$selectPiece = $select_piece + $order_product[$variant_id ? $variant_id : $product_id]['piece'];
		$thisProductFind = $productDetailModels->c_one(['p.id' => $product_id, 'p.is_active' => '1']);
		$thisProductOrderFind = $orderModels->c_one(['user_id' => $user_id, 'order_no' => $order_no]);
		
		if ($variant_id) {
			$thisVariantFind = $productDetailModels->productCombinationOne(['pa.id_product' => $product_id, 'pa.is_active' => '1', 'pa.stock >' => '0', 'pa.id' => $variant_id]);
			$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'user_id' => $user_id, 'product_id' => $product_id, 'variant_id' => $variant_id]);
			$maxPiece = $thisVariantFind->stock;
		}else{
			$maxPiece = $thisProductFind->stock;
			$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'user_id' => $user_id, 'product_id' => $product_id]);
		}

		$productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' =>$thisVariantFind->id], 1);
		if (!$productPicture) {
			$productPicture = $productDetailModels->c_all_image(['product_id' => $row['id']], 1);
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
		if ($thisProductFind->campaign_id) {
			$productCampaign = $campaignModels->c_one_index(['id' => $thisProductFind->campaign_id, 'is_active' => '1']);
		}

		//////////////////// Product Price Select Area Start ///////////////////
	
			if ($thisVariantFind->sale_price) {
				$priceArray = priceAreaFunction($thisVariantFind->sale_price, $thisVariantFind->discount_price, $thisVariantFind->basket_price, $productCampaign->discount);
			}else{
				$priceArray = priceAreaFunction($thisProductFind->sale_price, $thisProductFind->discount_price, $thisProductFind->basket_price, $productCampaign->discount);
			}
			if ($priceArray['discountPrice']) {
				$headerBasketPrice = $priceArray['discountPrice'];
			}else {
				$headerBasketPrice = $priceArray['totalPrice'];
			}

			if ($priceArray['basketBool'] && $priceArray['basketPrice']) {
				$last_price = $priceArray['basketPrice'];
			}elseif ($priceArray['discountBool'] && $priceArray['discountPrice']) {
				$last_price = $priceArray['discountPrice'];
			}else{
				$last_price = $priceArray['totalPrice'];
			}

			if ($thisVariantFind->tax_rate) {
				$vat_rate = $thisVariantFind->tax_rate;
			}else{
				$vat_rate = $thisProductFind->tax_rate;
			}
		/////////////////// Product Price Select Area End ///////////////////

		//////////////////// Product Title Select Area Start ///////////////////
			$featureArray = explode(' - ', $thisProductFind->attr);
			$categoryArray = explode(',', $thisProductFind->category_id);
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
			if ($thisVariantFind->title) {
				$title = $thisVariantFind->title;
			}else{
				$title = $thisProductFind->title;
			}
			$link = $thisProductFind->slug.'-p-'.$thisVariantFind->barcode_no;
		//////////////////// Product Title Select Area End ///////////////////

		if ($select_piece) {
			$newPiece = $selectPiece;
		}else{
			$newPiece = $selectPiece + 1;
		}
		if ($thisProductFind && $thisVariantFind) {
			$basketArray = [
				'id' => $product_id,
				'variant_id' => $variant_id,
				'variant_barcode' => $thisVariantFind->barcode_no,
				'image' => $image,
				'title' => $title,
				'link' => $link,
				'max_stock' => $thisVariantFind->stock,
				'header_basket_price' => $headerBasketPrice,
				'last_price' => $last_price,
				'vat_rate' => $vat_rate,
				'piece' => $newPiece,
				'color_id' => $color_id,
				'color_title' => $thisVariantFind->attr_color,
				'size_id' => $size_id,
				'size_title' => $thisVariantFind->attr_size
			];
			$_SESSION['order']['product'][$variant_id ? $variant_id : $product_id] = $basketArray;
		}
		
		$data['basketCount'] = count($_SESSION['order']['product']);
		if ($thisProductFind && $thisVariantFind) {
			$productDataView = [
				'id' => $variant_id ? $variant_id : $product_id,
				'product_id' => $product_id,
				'variant_id' => $variant_id,
				'image' => $image,
				'title' => $title . $variant_id,
				'link' => $link,
				'size_title' => $thisVariantFind->attr_size,
				'color_title' => $thisVariantFind->attr_color,
				'max_stock' => $thisVariantFind->stock,
				'kdv' => $vat_rate,
				'piece' => $newPiece,
				'header_basket_price' => number_format(($headerBasketPrice * $newPiece), 2),
				'last_price' => number_format(($last_price * $newPiece), 2)
			]; 
			$data['basketProduct'] = $productDataView;
		}
		if ($_SESSION['order']['coupon_id']) {

			$product_coupon_id = $variant_id ? $variant_id : $product_id;
			$couponFind = $couponModels->c_one(['id' => $_SESSION['order']['coupon_id'], 'is_active' => '1']);
			$userArray = explode(',', $couponFind->user_id);
			$categoryArray = explode(',', $couponFind->category_id);
			$brandArray = explode(',', $couponFind->brand_id);
			$productArray = explode(',', $couponFind->product_id);
			$categoryFindBool = TRUE;
			$brandFindBool = TRUE;
			$productFindBool = TRUE;
			$productDiscountBool = TRUE;
			$thisProductFind = $productDetailModels->c_one(['p.id' => $product_id, 'p.is_active' => '1']);
			$categoryProductArray = explode(',', $thisProductFind->category_id);

			foreach ($categoryProductArray as $item) {
				foreach ($categoryArray as $value) {
					if ($item == $value) {
						if ($couponFind->brand_id && $brandArray) {
							if (in_array($thisProductFind->brand_id, $brandArray)) {
								$categoryFindBool = FALSE;
							}
						}else{
							$categoryFindBool = FALSE;
						}
					}
				}
			}
			foreach ($brandArray as $item) {
				if ($item == $thisProductFind->brand_id) {
					if ($couponFind->category_id && $categoryArray && $categoryArray == FALSE) {
						$brandFindBool = FALSE;
					}else{
						$brandFindBool = FALSE;
					}
				}
			}

			foreach ($productArray as $item) {
				if ($item == $thisProductFind->id) {
					if ($couponFind->product_id && $productArray) {
						$productFindBool = FALSE;
					}else{
						$productFindBool = FALSE;
					}
				}
			}

			if (($categoryArray && $couponFind->category_id)) {
				if ($categoryFindBool == FALSE) {
					$productDiscountBool = FALSE;
				}else{
					$productDiscountBool = TRUE;
				}
			}
			
			if (($brandArray && $couponFind->brand_id)) {
				if (($categoryArray && $couponFind->category_id)) {
					if ($productDiscountBool == FALSE) {
						if ($brandFindBool == FALSE) {
							$productDiscountBool = FALSE;
						}else{
							$productDiscountBool = TRUE;
						}
					}
				}else{
					if ($brandFindBool == FALSE) {
						$productDiscountBool = FALSE;
					}else{
						$productDiscountBool = TRUE;
					}
				}
			}

			if (($productArray && $couponFind->product_id)) {
				if ($productFindBool == FALSE) {
					$productDiscountBool = FALSE;
				}else{
					$productDiscountBool = TRUE;
				}
			}

			if ($productDiscountBool == FALSE && !isset($row['coupon_id'])) {
				if ($couponFind->discount_type == '1') {
					$discount_price = fiyatHesaplamaNot($last_price, $couponFind->discount);
					$last_price = $last_price - $discount_price;
					$coupon_discount_total = $coupon_discount_total + ($last_price * $newPiece  - $last_price * $newPiece);
				}else{
					$last_price = $last_price;
				}
				$coupon_id = $couponFind->id;
				$coupon_discount_type = $couponFind->discount_type;
				$coupon_discount = $last_price - $last_price;
				
				$coupon_rate = $couponFind->discount;
				$newArray = [
					'id' => $product_id,
					'variant_id' => $variant_id,
					'variant_barcode' => $thisVariantFind->barcode_no,
					'image' => $image,
					'title' => $title,
					'link' => $link,
					'max_stock' => $thisVariantFind->stock,
					'header_basket_price' => $headerBasketPrice,
					'last_price' => $last_price,
					"coupon_id" => $coupon_id,
					"coupon_discount_type" => $coupon_discount_type,
					"coupon_rate" => $coupon_rate,
					'vat_rate' => $vat_rate,
					'piece' => $newPiece,
					'color_id' => $color_id,
					'color_title' => $thisVariantFind->attr_color,
					'size_id' => $size_id,
					'size_title' => $thisVariantFind->attr_size
				];
				$_SESSION['order']['product'][$product_coupon_id] = $newArray;
			}
		}

		foreach ($_SESSION['order']['product'] as $row) {
			$headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
			$basketTotalPrice += $row['last_price'] * $row['piece'];
			if ($user_id) {
				$basketDatebaseData = [
					'user_id' => $user_id,
					'product_id' => $row['id'],
					'variant_id' => $row['variant_id'],
					'color_id' => $row['color_id'],
					'size_id' => $row['size_id'],
					'piece' => $row['piece'],
				];
				$basketFind = $basketModels->c_one(['user_id' => $user_id, 'product_id' => $row['id'], 'variant_id' => $row['variant_id']]);
				if (!$basketFind) {
					$basketModels->add($basketDatebaseData);
				}
			}
		}
		foreach ($_SESSION['order']['product'] as $key => $row) {
			if ($row['coupon_id']) {
				$totalDiscountPrice = $totalDiscountPrice + $row['last_price'] * $row['piece'];
			}
			if ($_SESSION['order']['coupon_discount_type'] == '2') {
				$price_coupon = (($row['last_price'] * $row['piece']) * $_SESSION['order']['coupon_discount_rate']) / $basketTotalPrice;
				$last_price = $row['last_price'];
				$coupon_id = $couponFind->id;
				$coupon_discount_type = $couponFind->discount_type;
				$coupon_discount = $row['last_price'] - $last_price;
				
				$coupon_rate = $couponFind->discount;
				$_SESSION['order']['product'][$key]['price_coupon'] = $price_coupon;
			}
		}

		if ($totalDiscountPrice) {
			if ($_SESSION['order']['coupon_discount_type'] == '1') {
				$coupon_discount = fiyatHesaplamaPlus($totalDiscountPrice, $_SESSION['order']['coupon_discount_rate']) - $totalDiscountPrice;
				$coupon_discount_total = $coupon_discount;
			}else{
				if ($totalDiscountPrice < $_SESSION['order']['coupon_discount_rate']) {
					$coupon_discount = $totalDiscountPrice;
				}else{
					$coupon_discount = $_SESSION['order']['coupon_discount_rate'];
				}
				$basketTotalPriceNew = $basketTotalPrice - $coupon_discount;
				$coupon_discount = $basketTotalPrice - $basketTotalPriceNew;
				$coupon_discount_total = $coupon_discount;
				$basketTotalPrice = $basketTotalPriceNew;
			}
		}
		
		if ($delivery_options_first->free_shipping_price >= $basketTotalPrice) {
			$basketTotalPrice = $basketTotalPrice + $delivery_options_first->shipping_price;
			$headerBasketPriceSesion = $headerBasketPriceSesion;
			$this->session->push('order', ['shipping_price' => $delivery_options_first->shipping_price]);
			$data['free_shipping_price'] = FALSE;
			$data['free_shipping'] = number_format($delivery_options_first->shipping_price , 2). ' TL';
			$data['free_shipping_price'] = $delivery_options_first->shipping_price;
		}else{
			$data['free_shipping_price'] = TRUE;
			$data['free_shipping_price'] = '';
			unset($_SESSION['order']['shipping_price']);
		}
		$data['basket_total_price_first'] = number_format($basketTotalPrice, 2);
		$this->session->push('order', ['header_basket_price' => $headerBasketPriceSesion]);
		$this->session->push('order', ['basket_total_price' => $basketTotalPrice]);
		$this->session->push('order', ['coupon_discount' => $coupon_discount_total]);
		$data['disconce_price'] = number_format($headerBasketPriceSesion - $basketTotalPrice +$data['free_shipping_price'] , 2);
		$data['basket_total_price'] = number_format($basketTotalPrice, 2);
		$data['headerBasketPriceSesion'] = number_format($headerBasketPriceSesion, 2);
	}
}
