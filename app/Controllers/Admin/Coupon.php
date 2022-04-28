<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Category;
use App\Models\AttributeModels;
use App\Models\BrandModels;
use App\Models\CouponModels;
use App\Models\ProductModels;
use App\Models\UserModels;

class Coupon extends BaseController
{
    public function _construct()
    {
        
    }

    public function add() 
    {   
        $data['sidebarActive'] = 'coupon';
        $db = db_connect();
        $category = new Category($db);
        $brandModels = new BrandModels($db);
        $productModels = new ProductModels($db);
        $user = new UserModels($db);
        $attributeModels = new AttributeModels($db);
        $data['categoriesList'] = $category->c_all();
        $data['categoriesCount'] = $category->count();
        $data['category'] = $category;
        $data['brands'] = $brandModels->c_all(['is_active' => '1']);
        $data['product'] = $productModels->c_all('', '', '', '');
        $data['user'] = $user->c_all(['ut.type' => 'frontend']);
        $data['userSeller'] = $user->c_all(['ut.type' => 'frontend', 'users.is_sellers' => '1']);
        $data['attribute'] = $attributeModels->c_all(["a.attribute_group_id" => '9', 'a.is_active' => '2']);
        return view ("admin/coupon/coupon-add", $data);
    }

    public function edit($id)
    {
        $data['sidebarActive'] = 'coupon';
        $db = db_connect();
        $couponModels = new CouponModels($db);
        $category = new Category($db);
        $brandModels = new BrandModels($db);
        $productModels = new ProductModels($db);
        $user = new UserModels($db);
        $attributeModels = new AttributeModels($db);
        
        $data['categoriesList'] = $category->c_all();
        $data['categoriesCount'] = $category->count();
        $data['category'] = $category;
        $data['brands'] = $brandModels->c_all(['is_active' => '1']);
        $data['product'] = $productModels->c_all('', '', '', '');
        $data['user'] = $user->c_all(['ut.type' => 'frontend']);
        $data['userSeller'] = $user->c_all(['ut.type' => 'frontend', 'users.is_sellers' => '1']);
        $data['attribute'] = $attributeModels->c_all(["a.attribute_group_id" => '9', 'a.is_active' => '2']);

        $data['coupon'] = $couponModels->c_one(['id' => $id]);
        $data['categorArray'] = explode(',', $data['coupon']->category_id);
        $data['brandArray'] = explode(',', $data['coupon']->brand_id);
        $data['productArray'] = explode(',', $data['coupon']->product_id);
        $data['userArray'] = explode(',', $data['coupon']->user_id);
        $data['sellerArray'] = explode(',', $data['coupon']->seller_id);
        $data['attributeArray'] = explode(',', $data['coupon']->attribute_id);
       
        $data['seasons'] = $productModels->c_season(['is_active' => '1', 'season !=' => ""]);
        return view ("admin/coupon/coupon-edit",  $data);
    }

    public function list()
    {
        $data['sidebarActive'] = 'coupon';
        $db = db_connect();
        $couponModels = new CouponModels($db);
        $data['coupons'] = $couponModels->c_all();
        return view ("admin/coupon/coupon-list", $data);
    } 

    public function insert()
    {
        $validation =  \Config\Services::validation();
        $title = $this->request->getPost('title');
        $code = $this->request->getPost('code');
        $slug = sef_link($this->request->getPost('slug'));
        if (!$slug) {
            $slug = sef_link($title);
        }else {
            $slug = sef_link($slug);
        }
        $coupon_type = $this->request->getPost('coupon_type');
        $discount_type = $this->request->getPost('discount_type');
        $discount = $this->request->getPost('discount');
        $users_id = $this->request->getPost('user_id');
        $products_id = $this->request->getPost('product_id');
        $categories_id = $this->request->getPost('category_id');
        $brands_id = $this->request->getPost('brand_id');
        $sellers_id = $this->request->getPost('seller_id');
        $attributes_id = $this->request->getPost('attribute_id');
        $piece = $this->request->getPost('piece');
        $discount_min = $this->request->getPost('discount_min');
        $is_active = $this->request->getPost('is_active');
        $is_news = $this->request->getPost('is_news');
        $end_at = $this->request->getPost('end_at');
    
        foreach ($categories_id as $row) {
            $category_id .= $row.',';
        }
        foreach ($brands_id as $row) {
            $brand_id .= $row.',';
        }
        foreach ($users_id as $row) {
            $user_id .= $row.',';
        }
        foreach ($products_id as $row) {
            $product_id .= $row.',';
        }
        foreach ($sellers_id as $row) {
            $seller_id .= $row.',';
        }
        foreach ($attributes_id as $row) {
            $attribute_id .= $row.',';
        }
        $category_id = rtrim($category_id, ',');
        $brand_id = rtrim($brand_id, ',');
        $user_id = rtrim($user_id, ',');
        $product_id = rtrim($product_id, ',');
        $db = db_connect();
        $couponModels = new CouponModels($db);
        $productModels = new ProductModels($db);
        if (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen Kupon için bir başlık giriniz.';
        }elseif (!$validation->check($code, 'required')) {
            $data['error'] =  'Lütfen kupon kodunu giriniz.';
        }elseif (!$validation->check($discount, 'required')) {
            $data['error'] =  'Lütfen kuponun indirim miktarını giriniz.';
        }else{
          
            $dataInsert = [
                "user_id" => $user_id,
                "product_id" => $product_id,
                "category_id" => $category_id,
                "brand_id" => $brand_id,
                "seller_id" => $seller_id,
                "attribute_id" => $attribute_id,
                "title" => $title,
                "code" => $code,
                "slug" => $slug,
                "piece" => $piece,
                "coupon_type" => $coupon_type,
                "discount_type" => $discount_type,
                "discount" => $discount,
                "discount_min" => $discount_min,
                "end_at" => $end_at,
                "is_active" => $is_active,
                "is_news" => $is_news,
                "created_at" => created_at(),
            ];
            $insertData = $couponModels->add($dataInsert);
            if ($insertData) {
                $data['success'] = "Kupon Kodu ekleme işlemi başarılı bir şekilde yapıldı.";
            }else{
                $data['error'] = "Beklenmeyen bir hata oluştu.";
            }
        }
        return json_encode($data);
    }

    public function update()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('id');
        $title = $this->request->getPost('title');
        $code = $this->request->getPost('code');
        $slug = sef_link($this->request->getPost('slug'));
        if (!$slug) {
            $slug = sef_link($title);
        }else {
            $slug = sef_link($slug);
        }
        $coupon_type = $this->request->getPost('coupon_type');
        $discount_type = $this->request->getPost('discount_type');
        $discount = $this->request->getPost('discount');
        $discount_min = $this->request->getPost('discount_min');
        $users_id = $this->request->getPost('user_id');
        $products_id = $this->request->getPost('product_id');
        $categories_id = $this->request->getPost('category_id');
        $brands_id = $this->request->getPost('brand_id');
        $sellers_id = $this->request->getPost('seller_id');
        $attributes_id = $this->request->getPost('attribute_id');
        $piece = $this->request->getPost('piece');
        $is_active = $this->request->getPost('is_active');
        $is_news = $this->request->getPost('is_news');
        $end_at = $this->request->getPost('end_at');
    
        foreach ($categories_id as $row) {
            $category_id .= $row.',';
        }
        foreach ($brands_id as $row) {
            $brand_id .= $row.',';
        }
        foreach ($users_id as $row) {
            $user_id .= $row.',';
        }
        foreach ($products_id as $row) {
            $product_id .= $row.',';
        }
        foreach ($sellers_id as $row) {
            $seller_id .= $row.',';
        }
        foreach ($attributes_id as $row) {
            $attribute_id .= $row.',';
        }
        $category_id = rtrim($category_id, ',');
        $brand_id = rtrim($brand_id, ',');
        $user_id = rtrim($user_id, ',');
        $product_id = rtrim($product_id, ',');
        $seller_id = rtrim($seller_id, ',');
        $attribute_id = rtrim($attribute_id, ',');
        $db = db_connect();
        $couponModels = new CouponModels($db);
        $productModels = new ProductModels($db);
        $campaignFind  = $couponModels->c_one(['id' => $id]);
        if (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen Kupon için bir başlık giriniz.';
        }elseif (!$validation->check($code, 'required')) {
            $data['error'] =  'Lütfen kupon kodunu giriniz.';
        }elseif (!$validation->check($discount, 'required')) {
            $data['error'] =  'Lütfen kuponun indirim miktarını giriniz.';
        }elseif (!$campaignFind) {
            $data['error'] =  'Düzenlemek istediğiniz kupon bulunamadı.';
        }else{
            if (!$end_at || $end_at == 'Invalid date') {
                $end_at = NULL;
            }
            if ($campaignFind->is_news == '0' && $is_news == '1') {
                $dataInsert = [
                    "user_id" => $user_id,
                    "product_id" => $product_id,
                    "category_id" => $category_id,
                    "brand_id" => $brand_id,
                    "seller_id" => $seller_id,
                    "attribute_id" => $attribute_id,
                    "title" => $title,
                    "code" => $code,
                    "slug" => $slug,
                    "piece" => $piece,
                    "coupon_type" => $coupon_type,
                    "discount_type" => $discount_type,
                    "discount" => $discount,
                    "discount_min" => $discount_min,
                    "end_at" => $end_at,
                    "is_news" => $is_news,
                    "created_at" => created_at(),
                ];
            }else{
                $dataInsert = [
                    "user_id" => $user_id,
                    "product_id" => $product_id,
                    "category_id" => $category_id,
                    "brand_id" => $brand_id,
                    "seller_id" => $seller_id,
                    "attribute_id" => $attribute_id,
                    "title" => $title,
                    "code" => $code,
                    "slug" => $slug,
                    "piece" => $piece,
                    "coupon_type" => $coupon_type,
                    "discount_type" => $discount_type,
                    "discount" => $discount,
                    "discount_min" => $discount_min,
                    "end_at" => $end_at,
                    "is_news" => $is_news,
                    "updated_at" => created_at(),
                ];
            }
            
            $insertData = $couponModels->edit($id, $dataInsert);
            if ($insertData) {
                $data['success'] = "İndirim Kuponu düzenleme işlemi başarılı bir şekilde yapıldı.";
            }else{
                $data['error'] = "Beklenmeyen bir hata oluştu.";
            }
        }
        return json_encode($data);
    }

    public function delete()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('value');
        $db = db_connect();
        $couponModels = new CouponModels($db);
        if (!$validation->check($id, 'required')) {
            $data['error'] =  'Silmek istediğiniz veri bulunamadı';
        }else{
            $categoryCheack = $couponModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Silmeye çaliştiğiniz indirim kodu mevcut değil lütfen daha sonra tekrar deneyiniz..";
            }else{
                $deleteCategory = $couponModels->delete($id);
                if ($deleteCategory) {
                    $data['success'] = "İndirim Kopu başarılı bir şekilde silindi.";
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
        $couponModels = new CouponModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $categoryCheack = $couponModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "is_active" => $value,
                    "updated_at" => created_at(),
                ];
                $updateCategory = $couponModels->edit($id, $dataInsert);
                if ($updateCategory) {
                    $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        return json_encode($data);
    }

    public function rank()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('id');
        $value = $this->request->getPost('value');
        $db = db_connect();
        $couponModels = new CouponModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $attributeCheack = $couponModels->c_one([
                "id" => $id
            ]);
            if (!$attributeCheack) {
                $data['error'] = "Sira değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "rank" => $value,
                    "updated_at" => created_at(),
                ];
                $updateAttribute = $couponModels->edit($id, $dataInsert);
                if ($updateAttribute) {
                    $data['success'] = "Sıralama değiştirme işlemi başarılı bir şekilde yapıldı.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        return json_encode($data);
    }

}