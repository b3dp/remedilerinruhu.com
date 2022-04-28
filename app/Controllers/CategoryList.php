<?php

namespace App\Controllers;

use App\Libraries\LoadView;
use App\Models\Category;
use App\Models\ProductFilterModels;
use App\Models\ProductCategoryModels;
use App\Models\UserModels;
use App\Models\AttributeGroupModels;
use App\Models\CampaignModels;
use App\Models\AttributeModels;
class CategoryList extends LoadView
{
	public function index($s = '', $id = '', $page = '1' )
	{
		$data = $this->data;
        $db =  db_connect();
		$pager = \Config\Services::pager();
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productCategoryModels = new ProductCategoryModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$campaignModels = new CampaignModels($db);
		$attributeModels = new AttributeModels($db);
		
		if ($s && $id) {
            $data['id'] = $id;
            $data['link'] = $s. '-c-'. $id;
            $data['page'] = $page;
            $data['categoryFind'] = $category->c_one(['slug' => $s, 'id' => $id]);
            $data['topCategoryFind'] = array_reverse($category->c_top_all_list('', $data['categoryFind']->parent_id));
            $data['category'] = $category;
            $data['productCategoryModels'] = $productCategoryModels;
            $data['campaignModels'] = $campaignModels;
            $data['attributeModels'] = $attributeModels;
            $data['attributeGroupModels'] = $attributeGroupModels;
		
			if (isset($_GET['filter'])) {
				$filter = $_GET['filter'];
				$filterArrayVal = array();
				$filterArray = array();
			}
			if ($_GET['searchKey']) {
				$data['searchKey'] = $_GET['searchKey'];
				$data['searchKeySlug'] = sef_link($_GET['searchKey']);
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
					$attrFind = $attributeGroupModels->c_one(["slug" => $key]);
					$value1 = '';
					if ($attrFind) {
						if ($attrFind->is_combination == '1') {
							$filterWhereAttrCombineIn[$key]['a.slug'] = $filterValue;
						}else{
							$filterWhereAttrIn[$key]['a.slug'] = $filterValue;
						}
						
						foreach ($filterValue as $item1) {
							$value1 .= '\''.$item1.'\''. ',';
						}
						$value1 = rtrim($value1, ',');
						$where[$attrFind->slug] .= 'a.slug IN('.$value1.')';
						$order[$attrFind->slug] .= 'at.slug IN('.$value1.') AND agt.attribute_group_id = '.$attrFind->id.' ';
						$in[$attrFind->slug] .= '(a.slug IN('.$value1.') AND a.attribute_group_id = '.$attrFind->id.') ';
					}

					if($key == 'brand' && $key){
						$filterWhereIn['b.slug'] = $filterValue;
					}
					
					if($key == 'kategori' && $key){
						$filterWhereIn['categories'] = $filterValue;
					}

					if($key == 'cinsiyet' && $key){
						$filterWhereIn['gender'] = $filterValue;
					}

					if($key == 'rating' && $key){
						if ($filterValue['0'] == 'increasing') {
							$filterRank = 'last_price ASC';
						}elseif ($filterValue['0'] == 'decreasing') {
							$filterRank = 'last_price DESC';
						}elseif ($filterValue['0'] == 'discount') {
							$filterRank = 'discount DESC';
						}elseif ($filterValue['0'] == 'last') {
							$filterRank = 'id DESC';
						}elseif ($filterValue['0'] == 'sale') {
							$filterRank = 'order_count DESC';
						}
					}
				}
				$data['in'] = $in;
			}	
			if (isset($filterArrayVal)) {
				$data['filterArrayVal'] = $filterArrayVal;
			}
			$data['filter'] = $filter;
			$data['filterWhereAttrIn'] = $filterWhereAttrIn;
			$data['filterWhereAttrCombineIn'] = $filterWhereAttrCombineIn;
			$data['filterWhereIn'] = $filterWhereIn;
			
			$productCountWhere = $productCategoryModels->count_new(['is_active' => '1'], $id, '', $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn);
			$data['productCount'] = $productCountWhere;
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
			$data['productFind'] = $productCategoryModels->c_all_new(['is_active' => '1'], $id, ['whereStart' => $whereStart, 'item' => $item], $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn, $filterRank);

			if (isset($this->data['user_id'])) {
				$userModels = new UserModels($db);
				$data['userModels'] = $userModels;
			}
			$data['attrFind'] = $attributeGroupModels->c_all(["is_active" => '1']);
			$data['where'] = $where;
			/*
				$filterColorArea = $productCategoryModels->filterColorArea(['is_active' => '1'], $id, $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn);
				foreach ($filterColorArea as $row) {
					$filterArrayColor[$row->attr_color] = $row->attr_color;
				}
			
				$filterSizeArea = $productCategoryModels->filterSizeArea(['is_active' => '1'], $id, $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn);
				foreach ($filterSizeArea as $row) {
					$sizeArray = explode(',', $row->attr_size_in);
					foreach ($sizeArray as $item) {
						$filterArraySize[$item] = $item;
					}
				}
			*/
					
			$filterCombinationArea = $productCategoryModels->filterCombinationArea(['is_active' => '1'], $id, $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn);
			if ($filterCombinationArea) {
				foreach ($filterCombinationArea as $row) {
					if ($row->attr_combination_slug) {
						$combinationArray = explode(',', $row->attr_combination_slug);
						if ($combinationArray) {
							foreach ($combinationArray as $item) {
								$combinationAttrArray = $attributeModels->c_one(['a.slug' => $item, 'a.is_active' => '2']);
								$filterArrayCombination[$combinationAttrArray->attribute_group_id][$item]['title'] = $combinationAttrArray->title;
								$filterArrayCombination[$combinationAttrArray->attribute_group_id][$item]['slug'] = $combinationAttrArray->slug;
							}
						}
					}
				}
			}
			$filterOrderArea = $productCategoryModels->filterOrderArea(['is_active' => '1'], $id, $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn);
			if ($filterOrderArea) {
				foreach ($filterOrderArea as $row) {
					if ($row->attr) {
						$orderArray = explode(',', $row->attr);
						if ($orderArray) {
							foreach ($orderArray as $item) {
								$orderAttrArray = $attributeModels->c_one(['a.slug' => $item, 'a.is_active' => '2']);
								$filterArrayOrder[$orderAttrArray->attribute_group_id][$item]['title'] = $orderAttrArray->title;
								$filterArrayOrder[$orderAttrArray->attribute_group_id][$item]['slug'] = $orderAttrArray->slug;
							}
						}
					}
				}
			}

			$filterGenderArea = $productCategoryModels->filterGenderArea(['is_active' => '1'], $id, $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn);
			if ($filterGenderArea) {
				foreach ($filterGenderArea as $keyParent => $row) {
					if ($row->cat_g_title) {
						$genderTitleArray = explode(',', $row->cat_g_title);
						$genderSlugArray = explode(',', $row->cat_g_slug);
						if ($genderTitleArray) {
							foreach ($genderTitleArray as $key => $item) {
								$filterArrayGender['title'][$keyParent] = $item;
								$filterArrayGender['slug'][$keyParent] = $genderSlugArray[$key];
							}
						}
					}
				}
			}

			$filterBrandArea = $productCategoryModels->filterBrandArea(['is_active' => '1'], $id, $filterWhereAttrIn, $filterWhereAttrCombineIn, $filterWhereIn);
			if ($filterBrandArea) {
				foreach ($filterBrandArea as $keyParent => $row) {
					if ($row->b_title) {
						$filterArrayBrand[$keyParent]['title'] = $row->b_title;
						$filterArrayBrand[$keyParent]['slug'] = $row->b_slug;
					}
				}
			}

			$data['filterArrayCombination'] = $filterArrayCombination;
			$data['filterArrayOrder'] = $filterArrayOrder;
			$data['filterArrayGenderTitle'] = array_unique($filterArrayGender['title']);
			$data['filterArrayGenderSlug'] = array_unique($filterArrayGender['slug']);
			$data['filterArrayBrand'] = $filterArrayBrand;

			$brandProductWhereIn = rtrim($brandProductWhereIn, ',');
			$brandProductAttrWhereIn = rtrim($brandProductAttrWhereIn, ',');
			$data['sideBarFilterArea'] = $productFilterModels;
        }
		$this->viewLoad('category', $data);
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
