<?php 

    namespace App\Controllers\Api;
    
    use App\Controllers\BaseController;
    use App\Models\ProductModels;
    use App\Models\Category;
    use App\Models\AttributeModels;
    use App\Models\ProductFeatureModels;
    use App\Models\ProductDetailModels;
    use App\Models\AttributeGroupModels;
    use App\Models\BrandModels;
    use App\Models\UserModels;
    use App\Models\AddressModels;
    use App\Models\OrderModels;

    class SearchApi extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function searchProductInsert()
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $productModels = new ProductModels($db);
            $category_count_table = $productModels->c_all_search_view();
            $rand = rand(10000,99999);
            foreach ($category_count_table as $item) {
                $productFind = $productModels->c_one_search(['id' => $item->id, 'pa_id' => $item->pa_id]);
                $productData = [
                    'id' => $item->id,
                    'cat_g_title' => $item->cat_g_title,
                    'cat_g_slug' => $item->cat_g_slug,
                    'cat_title' => $item->cat_title,
                    'cat_slug' => $item->cat_slug,
                    'all_barcode' => $item->all_barcode,
                    'season' => $item->season,
                    'category_id' => $item->category_id,
                    'campaign_id' => $item->campaign_id,
                    'brand_id' => $item->brand_id,
                    'tax_rate' => $item->tax_rate,
                    'c_discount' => $item->c_discount,
                    'discount_rate' => $item->discount_rate,
                    'campaign_price' => $item->campaign_price,
                    'sale_price' => $item->sale_price,
                    'discount_price' => $item->discount_price,
                    'slug' => $item->slug,
                    'basket_price' => $item->basket_price,
                    'outlet_price' => $item->outlet_price,
                    'is_active' => $item->is_active,
                    'b_id' => $item->b_id,
                    'b_title' => $item->b_title,
                    'b_slug' => $item->b_slug,
                    'pa_id' => $item->pa_id,
                    'barcode_no' => $item->barcode_no,
                    'title' => $item->title,
                    'attr_combination_id' => $item->attr_combination_id,
                    'attr_combination_title' => $item->attr_combination_title,
                    'attr_combination_slug' => $item->attr_combination_slug,
                    'rand' => $rand,
                    'attr' => $item->attr,
                    'attr_slug' => $item->attr_slug,
                    'order_count' => $item->order_count
                ];
                if ($productFind) {
                    $productModels->edit_search(['id' => $item->id, 'pa_id' => $item->pa_id], $productData);
                }else{
                    $productModels->add_search($productData);
                }
            }
            $productModels->delete_search('rand NOT IN('.$rand.')');
        }

    }
