<?php

namespace App\Controllers;

use App\Libraries\LoadView;
use App\Models\Category;
use App\Models\ProductDetailModels;
use App\Models\ProductCategoryModels;
use App\Models\UserModels;
use App\Models\AttributeModels;
use App\Models\AttributeGroupModels;
use App\Models\CampaignModels;
use App\Models\OrderModels;

class ProductDetail extends LoadView
{
	public function index($s = '', $id = '', $page = '1' )
	{
		$data = $this->data;
        $db =  db_connect();
		$category = new Category($db);
		$productDetailModels = new ProductDetailModels($db);
		$productCategoryModels = new ProductCategoryModels($db);
		$attributeModels = new AttributeModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$campaignModels = new CampaignModels($db);
		$orderModels = new OrderModels($db);
		$data['productCategoryModels'] = $productCategoryModels;
		$data['category'] = $category;
		$data['campaignModels'] = $campaignModels;
        if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
		if ($s && $id) {
          
            $row = $productDetailModels->c_one(['p.slug' => $s, 'pa.barcode_no' => $id, 'pa.is_active' => '1']);
            if ($row) {
                $product_id = $row->id ;
                $variant_barcode = $id ;
            }else{
                $row = $productDetailModels->c_one(['p.slug' => $s, 'pa.is_active' => '1', 'pa.id' => $id ]);
                if (!$row) {
                    $row = $productDetailModels->c_one(['p.slug' => $s, 'p.id' => $id ]);
                    $product_id = $row->id ;
                }else{
                    $product_id = $row->id ;
                    $variant_barcode = $row->barcode_no ;
                }
            }
            if (!$product_id) {
                $this->viewLoad('404', $data);
                exit;
            }
            
            if ($variant_barcode) {
                $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.barcode_no' => $variant_barcode]);
            }else{
                $productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.id' => $id]);
            }
            $data['arr']['productPicture'] = $productDetailModels->attributePictureAll(['pap.product_attribute_id' => $productCombinationOne->id]);
            if (!$data['arr']['productPicture']) {
                $data['arr']['productPicture'] = $productDetailModels->c_all_image(['product_id' => $row->id]);
            }
            $featureArray = explode(' - ', $row->attr);
            $categoryArray = explode(',', $row->category_id);

            foreach ($categoryArray as $item) {
                $catFind = '';
                if ($item) {
                    $catFind = $category->c_all_list('', $item);
                    if(!$catFind){
                        $endCatID = $item;
                    }
                }
            }

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
            $data['arr']['topCategoryFind'] = array_reverse($category->c_top_all_list('', $endCatID));
            $endCategoryFind = $category->c_one(["id" =>$endCatID]);
            if ($row->campaign_id) {
                $productCampaign = $campaignModels->c_one_index(['id' => $row->campaign_id, 'is_active' => '1']);
            }else{
                $productCampaign = '';
            }
            $discountBool = FALSE;
            $discountRate = '0.00';

            if ($productCombinationSelect->sale_price) {
                $priceArray = priceAreaFunction($productCombinationSelect->sale_price, $productCombinationSelect->discount_price, $productCombinationSelect->basket_price, $productCampaign->discount);
            }elseif ($productCombinationOne->sale_price) {
                $priceArray = priceAreaFunction($productCombinationOne->sale_price, $productCombinationOne->discount_price, $productCombinationOne->basket_price, $productCampaign->discount);
            }else{
                $priceArray = priceAreaFunction($row->sale_price, $row->discount_price, $row->basket_price, $productCampaign->discount);
            }
            $data['arr']['productID'] = $product_id;
            $data['arr']['variantID'] = $productCombinationOne->id;
            $data['arr']['product_type'] = $row->product_type;
            $data['arr']['variantBarcode'] = $variant_barcode;
            $data['arr']['slug'] = $row->slug;
            $data['arr']['seo_description'] = $row->seo_description;
            $data['arr']['description'] = $row->description;
            $productLinkID = $variant_barcode ? $variant_barcode : $row->id;
            $data['arr']['link'] = $row->slug.'-p-'.$productLinkID;
            if ($priceArray['discountBool']) {
                $data['arr']['discountBool'] = $priceArray['discountBool'];
            }
            if ($priceArray['discountRate']) {
                $data['arr']['discountRate'] = number_format($priceArray['discountRate'], 2);
            }
            if ($priceArray['totalPrice']) {
                $data['arr']['totalPrice'] = number_format(floor( $priceArray['totalPrice'] *100)/100, 2);
            }
            if ($priceArray['discountPrice']) {
                $data['arr']['discountPrice'] = number_format(floor(($priceArray['discountPrice']*100))/100, 2);
            }
            if ($priceArray['basketBool']) {
                $data['arr']['basketBool'] = number_format($priceArray['basketBool'], 2);
            }
            if ($priceArray['basketPrice']) {
                $data['arr']['basketPrice'] = number_format(floor(($priceArray['basketPrice']*100))/100, 2);
            }
            if ($priceArray['basketRate']) {
                $data['arr']['basketRate'] = number_format($priceArray['basketRate'], 1);
            }

            $data['arr']['brandID'] = $row->b_id;
            $data['arr']['brand'] = $row->b_title;
            $data['arr']['brandSlug'] = $row->b_slug;
            $data['arr']['colorTitle'] = $color_title;
            $data['arr']['colorID'] = $color_id;
            $data['arr']['sizeTitle'] = $size_title;
            $data['arr']['sizeID'] = $size_id;
            $data['arr']['stock'] = $productCombinationOne->stock;
            if ($productPicture) {
                foreach ($productPicture as $item) {
                    if (file_exists(base_url('/uploads/products/min/'.$item->image.'')) && $item->image){
                        $data['arr']['image'][] = base_url('/uploads/products/min/'.$item->image);
                    }else{
                        $data['arr']['image'][] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                    }
                    
                }
            }else{
                $data['arr']['image'][] = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
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
            $userProductOrderComment = FALSE;
            if ($user_id) {
                if ($productCombinationOne->id) {
                    $productOrderFindUser = $orderModels->orderDetailAll(['user_id' => $user_id, 'product_id' => $product_id, 'variant_id' => $productCombinationOne->id]);
                }else{
                    $productOrderFindUser = $orderModels->orderDetailAll(['user_id' => $user_id, 'product_id' => $product_id]);
                }
            }
            foreach ($productOrderFindUser as $row) {
                if ($row->status == '4' && (($row->piece - $row->cancellation_count - $row->return_count) > 0)) {
                    $productCommentFind = $productDetailModels->productCommentOne(['pc.user_id' => $user_id, 'pc.order_detail_id' => $row->id]);
                    if (!$productCommentFind) {
                        $userProductOrderComment = TRUE;
                    }
                }
            }
            $data['userProductOrderComment'] = $userProductOrderComment;
            if ($productCombinationOne->id) {
                $productComment = $productCommentFind = $productDetailModels->productCommentAll(['pc.product_id' => $product_id, 'variant_id' => $productCombinationOne->id, 'status' => '1' ]);;
            }else{
                $productComment = $productCommentFind = $productDetailModels->productCommentAll(['pc.product_id' => $product_id, 'status' => '1' ]);;
            }
            if ($productComment) {
                foreach ($productComment as $row) {
                    $totalRate = $totalRate + $row->rate;
                    $totalCount = $totalCount + 1;
                }
                $data['productComment'] = $productComment;
                $data['productRate'] = $totalRate;
                $data['productCount'] = $totalCount;
            }
            $data['orderProductFind'] = $productCategoryModels->c_all_old(['p.is_active' => '1'], $endCatID, ['whereStart' => '0', 'item' => '4']);
            getLogDate($user_id, '38', $data['arr']['productID'].'-'.$data['arr']['variantID'], 'Ürün-Variant ID', '');
        }
		$this->viewLoad('productDetail', $data);
	}

	public function productFavoritesAdd()
	{
		$db = db_connect();
        $productCategoryModels = new ProductCategoryModels($db);
        $userModels = new UserModels($db);
        $product_id = $this->request->getPost('product_id');
        $variant_id = $this->request->getPost('variant_id');
		if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
		if (isset($user_id)) {
			$userFind = $userModels->c_one (["id" => $user_id , "is_active" => '1']);
		}

		if (isset($variant_id)) {
			$prodcutFind = $productCategoryModels->c_one(["p.id" => $product_id, "p.is_active" => '1']);
			$variantFind = $productCategoryModels->productCombinationOne(["pa.id" => $variant_id, 'pa.is_active' => '1' , "pa.id_product" => $product_id]);
			$favoriteFind = $userModels->favorite_one (["user_id" => $user_id , "product_id" => $product_id, 'variant_id' => $variant_id]);
		}else{
			$prodcutFind = $userModels->c_one(["user_id" => $user_id , "is_active" => '1']);
			$favoriteFind = $userModels->favorite_one (["user_id" => $user_id , "product_id" => $product_id]);
		}
		
		if (!isset($user_id)) {
			$data['location'] = 'giris-yap';
		}elseif (!$userFind) {
			$data['error'] = 'Lütfen giriş yapınız veya sayfayı yenileyiniz.';
		}elseif (!$prodcutFind) {
			$data['error'] = 'Beğenmek istediğiniz ürün bulunamadı.';
		}elseif (isset($variant_id) && !$variantFind) {
			$data['error'] = 'Beğenmek istediğiniz ürün bulunamadı.';
		}elseif ($favoriteFind) {
			$data['error'] = 'Bu ürünü daha önce zaten beğendiniz.';
		}else{
			$favoriteProductData = [
				'user_id' => $user_id,
				'product_id' => $product_id,
				'variant_id' => $variant_id,
				'created_at' => created_at()
			];
			$favoriteProduct = $userModels->favoriteProduct($favoriteProductData);
			if ($favoriteProduct) {
				$data['success'] = 'Like';
                getLogDate($user_id, '39', $product_id . '-' . $variant_id, 'Ürün-Variant ID', '');
			}else{
				$data['error'] = 'Beklenmeyen bir hata oluştu.';
			}
		}
		return json_encode($data);
	}

	public function productFavoritesRemove()
	{
		$db = db_connect();
        $productCategoryModels = new ProductCategoryModels($db);
        $userModels = new UserModels($db);
        $product_id = $this->request->getPost('product_id');
        $variant_id = $this->request->getPost('variant_id');
		if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
		if (isset($user_id)) {
			$userFind = $userModels->c_one (["id" => $user_id , "is_active" => '1']);
		}

		if (isset($variant_id)) {
			$prodcutFind = $productCategoryModels->c_one(["p.id" => $product_id, "p.is_active" => '1']);
			$variantFind = $productCategoryModels->productCombinationOne(["pa.id" => $variant_id, 'pa.is_active' => '1' , "pa.id_product" => $product_id]);
			$favoriteFind = $userModels->favorite_one (["user_id" => $user_id , "product_id" => $product_id, 'variant_id' => $variant_id]);
		}else{
			$prodcutFind = $userModels->c_one(["user_id" => $user_id , "is_active" => '1']);
			$favoriteFind = $userModels->favorite_one (["user_id" => $user_id , "product_id" => $product_id]);
		}
		
		if (!isset($user_id)) {
			$data['location'] = 'giris-yap';
		}elseif (!$userFind) {
			$data['error'] = 'Lütfen giriş yapınız veya sayfayı yenileyiniz.';
		}elseif (!$prodcutFind) {
			$data['error'] = 'Beğenmek istediğiniz ürün bulunamadı.';
		}elseif (isset($variant_id) && !$variantFind) {
			$data['error'] = 'Beğenmek istediğiniz ürün bulunamadı.';
		}elseif (!$favoriteFind) {
			$data['error'] = 'Beğeniden çıkara bileceğiniz bir ürün bulunamadı.';
		}else{
			$favoriteProductData = [
				'user_id' => $user_id,
				'product_id' => $product_id,
				'variant_id' => $variant_id,
				'created_at' => created_at()
			];
			$favoriteProductRemove = $userModels->favoriteProductRemove(["id" => $favoriteFind->id, "user_id" => $user_id]);
			if ($favoriteProductRemove) {
				$data['success'] = 'disLike';
                getLogDate($user_id, '40', $product_id . '-' . $variant_id, 'Ürün-Variant ID', '');
			}else{
				$data['error'] = 'Beklenmeyen bir hata oluştu.';
			}
		}
		return json_encode($data);
	}

    public function productCommentAdd()
	{
		$db = db_connect();
        $productCategoryModels = new ProductCategoryModels($db);
        $productDetailModels = new ProductDetailModels($db);
        $userModels = new UserModels($db);
        $orderModels = new OrderModels($db);
        $product_id = $this->request->getPost('product_id');
        $variant_id = $this->request->getPost('variant_id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $subject = $this->request->getPost('subject');
        $comment = $this->request->getPost('comment');
        $rate = $this->request->getPost('rate');
		if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
		if (isset($user_id)) {
			$userFind = $userModels->c_one (["id" => $user_id , "is_active" => '1']);
		}

		if (isset($variant_id)) {
			$prodcutFind = $productCategoryModels->c_one(["p.id" => $product_id, "p.is_active" => '1']);
			$variantFind = $productCategoryModels->productCombinationOne(["pa.id" => $variant_id, 'pa.is_active' => '1' , "pa.id_product" => $product_id]);
		}else{
			$prodcutFind = $userModels->c_one(["user_id" => $user_id , "is_active" => '1']);
		}

        if ($user_id) {
            if ($variantFind->id) {
                $productOrderFindUser = $orderModels->orderDetailAll(['user_id' => $user_id, 'product_id' => $product_id, 'variant_id' => $variantFind->id]);
            }else{
                $productOrderFindUser = $orderModels->orderDetailAll(['user_id' => $user_id, 'product_id' => $product_id]);
            }
        }
        foreach ($productOrderFindUser as $row) {
            if ($row->status == '4' && (($row->piece - $row->cancellation_count - $row->return_count) > 0)) {
                $productCommentFind = $productDetailModels->productCommentOne(['pc.user_id' => $user_id, 'pc.order_detail_id' => $row->id]);
                if (!$productCommentFind) {
                    $order_detail_id = $row->id;
                    $order_id = $row->order_id;
                    break;
                }
            }
        }
		
		if (!isset($user_id)) {
			$data['location'] = 'giris-yap';
		}elseif (!$userFind) {
			$data['error'] = 'Lütfen giriş yapınız veya sayfayı yenileyiniz.';
		}elseif (!$prodcutFind) {
			$data['error'] = 'Yorum yapmak istediğiniz ürün bulunamadı.';
		}elseif (isset($variant_id) && !$variantFind) {
			$data['error'] = 'Yorum yapmak istediğiniz ürün bulunamadı.';
		}elseif (!$order_detail_id && !$order_id) {
			$data['error'] = 'Bu ürüne yorum yapabilmeniz için satın almanız gerekmektedir.';
		}elseif (!$name || !$email || !$subject || !$comment) {
			$data['error'] = 'Lütfen gerekli tüm alanları doldurunuz.';
		}elseif ($rate < 0 || $rate > 5) {
			$data['error'] = 'Ürün için verilen puan 1 ile 5 arasında olmalıdır.';
		}else{
			$commentProductData = [
				'user_id' => $user_id,
				'order_id' => $order_id,
				'order_detail_id' => $order_detail_id,
				'product_id' => $product_id,
				'variant_id' => $variant_id,
				'name' => $name,
				'email' => $email,
				'subject' => $subject,
				'comment' => $comment,
				'rate' => $rate,
				'status' => '1',
				'comment_approval_date' => nowDate(),
				'created_at' => created_at()
			];
			$commentProduct = $productDetailModels->productCommentAdd($commentProductData);
			if ($commentProduct) {
				$data['success'] = 'Yorumunuz başarılı bir şekilde alınmıştır kontrol sonrasında yayına alınacaktır.';
                getLogDate($user_id, '41', $product_id . '-' . $variant_id, 'Ürün-Variant ID', '');
			}else{
				$data['error'] = 'Beklenmeyen bir hata oluştu.';
			}
		}
		return json_encode($data);
	}
}
