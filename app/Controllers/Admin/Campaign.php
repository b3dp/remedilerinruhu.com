<?php 


namespace App\Controllers\Admin;
use App\Controllers\Api\SearchApi;
use App\Controllers\BaseController;
use App\Models\Category;
use App\Models\BrandModels;
use App\Models\CampaignModels;
use App\Models\ProductModels;

class Campaign extends BaseController
{ 
    public function _construct()
    {
       
    } 
    public function add() 
    {   
        $data['sidebarActive'] = 'campaign';
        $db = db_connect();
        $category = new Category($db);
        $brandModels = new BrandModels($db);
        $productModels = new ProductModels($db);
        $data = [
            'campaign_w' => getenv('picture.campaign_w'),
            'campaign_h' => getenv('picture.campaign_h'),
        ];
        
        $data['categoriesList'] = $category->c_all();
        $data['categoriesCount'] = $category->count();
        $data['category'] = $category;
        $data['brands'] = $brandModels->c_all(['is_active' => '1']);
        $data['seasons'] = $productModels->c_season(['is_active' => '1', 'season !=' => ""]);
        return view ("admin/campaign/campaign-add", $data);
    }
    public function edit($id)
    {
        $data['sidebarActive'] = 'campaign';
        $db = db_connect();
        $campaignModels = new CampaignModels($db);
        $category = new Category($db);
        $brandModels = new BrandModels($db);
        $productModels = new ProductModels($db);
        $data = [
            'campaign_w' => getenv('picture.campaign_w'),
            'campaign_h' => getenv('picture.campaign_h'),
        ];
        
        $data['categoriesList'] = $category->c_all();
        $data['categoriesCount'] = $category->count();
        $data['category'] = $category;
        $data['brands'] = $brandModels->c_all(['is_active' => '1']);

        $data['campaign'] = $campaignModels->c_one(['id' => $id]);
        $data['categorArray'] = explode(',', $data['campaign']->category_id);
        $data['brandArray'] = explode(',', $data['campaign']->brand_id);
        $data['seasonsArray'] = explode(',', $data['campaign']->season);
        $data['seasons'] = $productModels->c_season(['is_active' => '1', 'season !=' => ""]);
        return view ("admin/campaign/campaign-edit",  $data);
    }
    public function list()
    {
        $data['sidebarActive'] = 'campaign';
        $db = db_connect();
        $campaignModels = new CampaignModels($db);
        $data['campaigns'] = $campaignModels->c_all();
        return view ("admin/campaign/campaign-list", $data);
    } 

    public function insert()
    {
        $validation =  \Config\Services::validation();
        $title = $this->request->getPost('title');
        $slug = sef_link($this->request->getPost('slug'));
        if (!$slug) {
            $slug = sef_link($title);
        }else {
            $slug = sef_link($slug);
        }
        $discount = $this->request->getPost('discount');
        $categories_id = $this->request->getPost('category_id');
        $brands_id = $this->request->getPost('brand_id');
        $seo_title = $this->request->getPost('seo_title');
        $seo_description = $this->request->getPost('seo_description');
        $is_active = $this->request->getPost('is_active');
        $is_visible = $this->request->getPost('is_visible');
        $start_at = $this->request->getPost('start_at');
        $end_at = $this->request->getPost('end_at');
        $seasons = $this->request->getPost('season');
        if ($this->validate(['image' => 'uploaded[image]'])) {
            $picture = $this->request->getFile('image');
        }
        foreach ($categories_id as $row) {
            $category_id .= $row.',';
        }
        foreach ($brands_id as $row) {
            $brand_id .= $row.',';
        }
        foreach ($seasons as $row) {
            $season .= $row.',';
        }
        $category_id = rtrim($category_id, ',');
        $brand_id = rtrim($brand_id, ',');
        $season = rtrim($season, ',');
        $db = db_connect();
        $campaignModels = new CampaignModels($db);
        $productModels = new ProductModels($db);
        if (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen kampanya için bir başlık giriniz.';
        }elseif (!$validation->check($category_id, 'required') && !$validation->check($brand_id, 'required')) {
            $data['error'] =  'Lütfen kampanya için katagori yada marka seçiniz.';
        }elseif ($validation->check($start_at, 'required') && $validation->check($end_at, 'required') && $start_at > $end_at) {
            $data['error'] =  'Başlangıç tarihi bitiş tarihinden küçük olamaz.';
        }else{
            helper('text');
            if ($picture) {
                $imageName = $picture->getRandomName();
                $imageExt = $picture->getClientExtension();
    
                $image = \Config\Services::image()
                ->withFile($this->request->getFile('image'))
                ->fit(getenv('picture.campaign_w'), getenv('picture.campaign_h'), 'center')
                ->convert(IMAGETYPE_WEBP)
                ->save("./uploads/campaigns/".$imageName);
            }
          
            $dataInsert = [
                "category_id" => $category_id,
                "brand_id" => $brand_id,
                "season" => $season,
                "title" => $title,
                "slug" => $slug,
                "discount" => $discount,
                "seo_title" => $seo_title,
                "seo_description" => $seo_description,
                "image" => $imageName,
                "start_at" => $start_at,
                "end_at" => $end_at,
                "is_active" => $is_active,
                "is_visible" => $is_visible,
                "created_at" => created_at(),
            ];
            $insertData = $campaignModels->add($dataInsert);
            if ($insertData) {
                $campaign_id = $db->insertID();
                if ($is_active == '1') {
                    $dataProduct = [
                        "campaign_id" => $campaign_id,
                        "created_at" => created_at(),
                    ];
                }else{
                    $dataProduct = [
                        "campaign_id" => '',
                        "created_at" => created_at(),
                    ];
                }
                $updateProduct = $productModels->editCampaign($categories_id, $brands_id, $seasons, $dataProduct);
                $data['success'] = "Kampanya ekleme işlemi başarılı bir şekilde yapıldı.";

                $searchApi = new SearchApi();
                $searchApi->searchProductInsert();

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
        $slug = sef_link($this->request->getPost('slug'));
        if (!$slug) {
            $slug = sef_link($title);
        }else {
            $slug = sef_link($slug);
        }
        $discount = $this->request->getPost('discount');
        $categories_id = $this->request->getPost('category_id');
        $brands_id = $this->request->getPost('brand_id');
        $seasons = $this->request->getPost('season');
        $seo_title = $this->request->getPost('seo_title');
        $seo_description = $this->request->getPost('seo_description');
        $is_active = $this->request->getPost('is_active');
        $is_visible = $this->request->getPost('is_visible');
        $start_at = $this->request->getPost('start_at');
        $end_at = $this->request->getPost('end_at');
        if ($this->validate(['image' => 'uploaded[image]'])) {
            $picture = $this->request->getFile('image');
        }
        foreach ($categories_id as $row) {
            $category_id .= $row.',';
        }
        foreach ($brands_id as $row) {
            $brand_id .= $row.',';
        }
        foreach ($seasons as $row) {
            $season .= $row.',';
        }
        $category_id = rtrim($category_id, ',');
        $brand_id = rtrim($brand_id, ',');
        $season = rtrim($season, ',');
        $db = db_connect();
        $campaignModels = new CampaignModels($db);
        $productModels = new ProductModels($db);
        $campaignFind  = $campaignModels->c_one(['id' => $id]);
        if (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen kampanya için bir başlık giriniz.';
        }elseif (!$campaignFind) {
            $data['error'] =  'Düzenlemek istediğiniz kampanya bulunamadı.';
        }elseif (!$validation->check($category_id, 'required') && !$validation->check($brand_id, 'required')) {
            $data['error'] =  'Lütfen kampanya için katagori yada marka seçiniz.';
        }elseif ($validation->check($start_at, 'required') && $validation->check($end_at, 'required') && $start_at > $end_at) {
            $data['error'] =  'Başlangıç tarihi bitiş tarihinden küçük olamaz.';
        }else{
            helper('text');
            if ($picture) {
                $imageName = $picture->getRandomName();
                $imageExt = $picture->getClientExtension();
    
                $image = \Config\Services::image()
                ->withFile($this->request->getFile('image'))
                ->fit(getenv('picture.campaign_w'), getenv('picture.campaign_h'), 'center')
                ->convert(IMAGETYPE_WEBP)
                ->save("./uploads/campaigns/".$imageName);
            }else{
                $imageName = $campaignFind->image;
            }
          
            $dataInsert = [
                "category_id" => $category_id,
                "brand_id" => $brand_id,
                "season" => $season,
                "title" => $title,
                "slug" => $slug,
                "discount" => $discount,
                "seo_title" => $seo_title,
                "seo_description" => $seo_description,
                "image" => $imageName,
                "start_at" => $start_at ? $start_at : NULL,
                "end_at" => $end_at ? $end_at : NULL,
                "is_active" => $is_active,
                "is_visible" => $is_visible,
                "updated_at" => created_at(),
            ];
            $insertData = $campaignModels->edit($id, $dataInsert);
            if ($insertData) {
                $campaign_id = $id;

                if ($is_active == '1') {
                    $dataProduct = [
                        "campaign_id" => $campaign_id,
                        "updated_at" => created_at(),
                    ];
                }else{
                    $dataProduct = [
                        "campaign_id" => '',
                        "updated_at" => created_at(),
                    ];
                }
                $dataProductEditArray = [
                    "campaign_id" => '',
                    "updated_at" => created_at(),
                ];
                $productModels->edit_array(['campaign_id' =>$campaign_id ], $dataProductEditArray);
                $updateProduct = $productModels->editCampaign($categories_id, $brands_id, $seasons, $dataProduct);
                $data['success'] = "Kampanya düzenleme işlemi başarılı bir şekilde yapıldı.";

                $searchApi = new SearchApi();
                $searchApi->searchProductInsert();

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
        $campaignModels = new CampaignModels($db);
        $productModels = new ProductModels($db);
        if (!$validation->check($id, 'required')) {
            $data['error'] =  'Silmek istediğiniz veri bulunamadı';
        }else{
            $categoryCheack = $campaignModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Silmeye çaliştiğiniz kampanya mevcut değil lütfen daha sonra tekrar deneyiniz..";
            }else{
                $deleteCategory = $campaignModels->delete($id);
                if ($deleteCategory) {
                    $data['success'] = "Kampanya başarılı bir şekilde silindi.";

                    $dataProduct = [
                        "campaign_id" => '',
                        "updated_at" => created_at(),
                    ];
                    $categories_id = explode(',', $categoryCheack->category_id);
                    $brands_id = explode(',', $categoryCheack->brand_id);
                    $seasons = explode(',', $categoryCheack->season);
                    $updateProduct = $productModels->editCampaign($categories_id, $brands_id, $seasons, $dataProduct);
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        $searchApi = new SearchApi();
        $searchApi->searchProductInsert();
        return json_encode($data);
    }

    public function status()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('id');
        $value = $this->request->getPost('value');
        $db = db_connect();
        $campaignModels = new CampaignModels($db);
        $productModels = new ProductModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $categoryCheack = $campaignModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "is_active" => $value,
                    "updated_at" => created_at(),
                ];
                $updateCategory = $campaignModels->edit($id, $dataInsert);
                if ($updateCategory) {
                    $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";

                    if ($value == '1') {
                        $dataProduct = [
                            "campaign_id" => $id,
                            "updated_at" => created_at(),
                        ];
                    }else{
                        $dataProduct = [
                            "campaign_id" => '',
                            "updated_at" => created_at(),
                        ];
                    }
                    if ($categoryCheack->category_id) {
                        $categories_id = explode(',', $categoryCheack->category_id);
                    }
                    if ($categoryCheack->brand_id) {
                        $brands_id = explode(',', $categoryCheack->brand_id);
                    }
                    if ($categoryCheack->season) {
                        $seasons = explode(',', $categoryCheack->season);
                    }
                    $updateProduct = $productModels->editCampaign($categories_id, $brands_id, $seasons, $dataProduct);

                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        $searchApi = new SearchApi();
        $searchApi->searchProductInsert();
        return json_encode($data);
    }
        
    public function deleteImg()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('value');
        $db = db_connect();
        $campaignModels = new CampaignModels($db);
        $productModels = new ProductModels($db);
        if (!$validation->check($id, 'required')) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $categoryCheack = $campaignModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "image" => '',
                    "updated_at" => created_at(),
                ];
                $updateCategory = $campaignModels->edit($id, $dataInsert);
                if ($updateCategory) {
                    $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";

                    if ($value == '1') {
                        $dataProduct = [
                            "campaign_id" => $id,
                            "updated_at" => created_at(),
                        ];
                    }else{
                        $dataProduct = [
                            "campaign_id" => '',
                            "updated_at" => created_at(),
                        ];
                    }
                    if ($categoryCheack->category_id) {
                        $categories_id = explode(',', $categoryCheack->category_id);
                    }
                    if ($categoryCheack->brand_id) {
                        $brands_id = explode(',', $categoryCheack->brand_id);
                    }
                    if ($categoryCheack->season) {
                        $seasons = explode(',', $categoryCheack->season);
                    }
                    $updateProduct = $productModels->editCampaign($categories_id, $brands_id, $seasons, $dataProduct);

                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        $searchApi = new SearchApi();
        $searchApi->searchProductInsert();
        return json_encode($data);
    }

    public function rank()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('id');
        $value = $this->request->getPost('value');
        $db = db_connect();
        $campaignModels = new CampaignModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $attributeCheack = $campaignModels->c_one([
                "id" => $id
            ]);
            if (!$attributeCheack) {
                $data['error'] = "Sira değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "rank" => $value,
                    "updated_at" => created_at(),
                ];
                $updateAttribute = $campaignModels->edit($id, $dataInsert);
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