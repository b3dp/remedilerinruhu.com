<?php

namespace App\Controllers;

use App\Libraries\LoadView;
use App\Models\Category;
use App\Controllers\CategoryList;
use App\Models\ProductDetailModels;
use App\Models\ProductModels;
use App\Models\ProductCategoryModels;
use App\Models\UserModels;
use App\Models\SliderModels;
use App\Controllers\ProductDetail;
use App\Controllers\Basket;
use App\Controllers\Checkout;
use App\Controllers\CampaignList;
use App\Controllers\BrandList;
use App\Models\CampaignModels;
use App\Models\BrandModels;
use App\Models\fixedFieldsModels;
class Home extends LoadView
{
	public function index($do = '', $s = '', $id = '' )
	{
		$data = $this->data;
		$categoryList = new CategoryList();
		$productDetail = new ProductDetail();
		$checkout = new Checkout();
		$basket = new Basket();
		$campaignList = new CampaignList();
		$brandList = new BrandList();
		$db =  db_connect();
		$category = new Category($db);
		$sliderModels = new SliderModels($db);
		$campaignModels = new CampaignModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$productCategoryModels = new ProductCategoryModels($db);
		$userModels = new UserModels($db);
		$productModels = new ProductModels($db);
		$brandModels = new BrandModels($db);
		$fixedFieldsModels = new fixedFieldsModels($db);
		if ($do != '') {
			$categoryFind = $category->c_one(['CONCAT(CONCAT(slug , "-c-"), id) ' => $do ]);
			if ($categoryFind) {
				$categoryList->index($categoryFind->slug, $categoryFind->id, $s);
			}else{
				$categoryFindTest = $category->c_one(['CONCAT(CONCAT(slug , "-t-"), id) ' => $do ]);
				if ($categoryFindTest) {
					$categoryList->indexTest($categoryFindTest->slug, $categoryFindTest->id, $s);
				}else{
					$productFind = $productDetailModels->c_one(['CONCAT(CONCAT(p.slug , "-p-"), pa.barcode_no) ' => $do ]);
					$productFindTwo = $productDetailModels->c_one(['CONCAT(CONCAT(p.slug , "-p-"), pa.id) ' => $do ]);
					$productFindThrere = $productDetailModels->c_one(['CONCAT(CONCAT(p.slug , "-p-"), p.id) ' => $do ]);
					if ($productFind) {
						$productDetail->index($productFind->slug, $productFind->barcode_no, $s);
					}elseif ($productFindTwo) {
						$productDetail->index($productFindTwo->slug, $productFindTwo->pa_id, $s);
					}elseif ($productFindThrere) {
						$productDetail->index($productFindThrere->slug, $productFindThrere->id, $s);
					}else{
						if ($do == 'sepetim') {
							$basket->index();
						}else{
							if ($do == 'siparis') {
								$checkout->index();
							}else{
								if ($do == 'kampanya') {
									$campaignFind = $campaignModels->c_one(['CONCAT(CONCAT(slug , "-c-"), id) ' => $s ]);
									if ($campaignFind) {
										$campaignList->index($campaignFind->slug, $campaignFind->id, $id);
									}
								}else{
									$brandFind = $brandModels->c_one(['CONCAT(CONCAT(slug , "-b-"), id) ' => $do ]);
									if ($brandFind) {
										$brandList->index($brandFind->slug, $brandFind->id, $s);
									}else{
										$this->viewLoad('404', $data);	
									}
								}
							}
						}
					}
				}
			}
		}else{
			$data['sliders'] = $sliderModels->c_all(['is_active' => '1']);
			$data['campaigns'] = $campaignModels->c_all_index(['is_active' => '1', 'is_visible' => '1'], ['item' => '12', 'whereStart' => '0']);
			$data['popularBrand'] = $brandModels->c_all_index(['is_active' => '1', 'is_popular' => '1'], ['item' => '8', 'whereStart' => '0']);
			$data['fixedNewSession'] = $fixedFieldsModels->c_one(['is_active' => '1', 'group_id' => '2']);
			$data['newProduct'] = $productModels->c_all(['p.is_active' => '1', 'pa.id !=' => NULL] );
			$data['productCategoryModels'] = $productCategoryModels;
			$data['campaignModels'] = $campaignModels;
			$data['category'] = $category;
			$data['userModels'] = $userModels;
			$this->viewLoad('index', $data);
		}
	}
}
