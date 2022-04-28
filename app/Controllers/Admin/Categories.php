<?php 

    namespace App\Controllers\Admin;

    use App\Libraries\Trendyol;
    use App\Controllers\BaseController;
    use App\Models\Category;
  

    class Categories extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index($type = 'product')
        {
            $data["title"] = "Kategoriler";
            $data["type"] = $type;
            $data['sidebarActive'] = 'categories';
            if ($type != 'product') {
                $data["typeLink"] = '/'.$type;
                $data['sidebarActive'] = 'blog';
                $data['sidebarAltActive'] = 'blog/list';
            }
            $db = db_connect();
            $category = new Category($db);
            $data['categoriesList'] = $category->c_all(['type' => $type]);
            $data['categoriesListView'] = categoriesListView($category->c_all_list(['type' => $type]));
            $data['categoriesCount'] = $category->count();
            $data['category'] = $category;

            return view ("admin/categories/categories-list", $data);
        }

        public function add($type = 'product')
        {
            $data["title"] = "Kategoriler Ekle";
            $data["type"] = $type;
            $data['sidebarActive'] = 'categories';
            if ($type != 'product') {
                $data["typeLink"] = '/'.$type;
                $data['sidebarActive'] = 'blog';
                $data['sidebarAltActive'] = 'blog/list';
            }
            $db = db_connect();
            $category = new Category($db);
            $data['categoriesList'] = categoriesAddView($category->c_all_list(0, 0, $type));
            print_r($data['categoriesList']);
            return view ("admin/categories/categories-add", $data);
        }

        public function edit($id, $type = 'product')
        {
            $data["title"] = "Kategori Düzenle";
            $data["type"] = $type;
            $data['sidebarActive'] = 'categories';
            if ($type != 'product') {
                $data["typeLink"] = '/'.$type;
                $data['sidebarActive'] = 'blog';
                $data['sidebarAltActive'] = 'blog/list';
            }
            $db = db_connect();
            $category = new Category($db);
            $data['categoriesFind'] = $category->c_one([
                "id" => $id
            ]);
            $data['categoriesList'] = categoriesAddView($category->c_all_list(0, 0, $type), '' , $data['categoriesFind']->parent_id);
            return view ("admin/categories/categories-edit", $data);
        }
        
        public function insert()
        {
            $validation =  \Config\Services::validation();
            $title = $this->request->getPost('title');
            $parent_id = $this->request->getPost('parent_id');
            $description = $this->request->getPost('description');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $slug  = $this->request->getPost('slug ');
            $commission_rate  = $this->request->getPost('commission_rate ');
            $is_active  = $this->request->getPost('is_active ');
            $is_cimri  = $this->request->getPost('is_cimri ');
            $is_akakce  = $this->request->getPost('is_akakce ');
            $is_mubiko  = $this->request->getPost('is_mubiko ');
            $type  = $this->request->getPost('type');
            if (!$slug) {
                $slug = sef_link($title);
            }else {
                $slug = sef_link($slug);
            }
            $db = db_connect();
            $category = new Category($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen kategori için bir başlık giriniz.';
            }elseif (!$validation->check($parent_id, 'required')) {
                $data['error'] =  'Lütfen kategorinin üst kategorisini belirleyiniz.';
            }else{
                $categoryCheack = $category->c_one([
                    "slug" => $slug,
                    "parent_id" => $parent_id,
                ]);
                if ($categoryCheack) {
                    $data['error'] = "Girmek istediğiniz kategori mevcut lütfen kontrol ederek tekrar deneyiniz.";
                }else{

                    helper('text');
                    $menuPicture = $this->request->getFile('menuPicture');
                    if ($menuPicture->getClientExtension()) {
                        $imageName = $menuPicture->getRandomName();
                        $imageExt = $menuPicture->getClientExtension();
            
                        $image = \Config\Services::image()
                        ->withFile($this->request->getFile('menuPicture'))
                        ->fit(500, 500, 'center')
                        ->convert(IMAGETYPE_WEBP)
                        ->save("./uploads/category/menuPicture/".$imageName);
                    }
                    $dataInsert = [
                        "title" => $title,
                        "parent_id" => $parent_id,
                        "description" => $description,
                        "menuPicture" => $imageName,
                        "seo_title" => $seo_title,
                        "seo_description" => $seo_description,
                        "commission_rate" => $commission_rate,
                        "type" => $type,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "is_cimri" => $is_cimri,
                        "is_akakce" => $is_akakce,
                        "is_mubiko" => $is_mubiko,
                        "rank" => '1',
                        "created_at" => created_at(),
                    ];
                    $insertCategory = $category->add($dataInsert);
                    if ($insertCategory) {
                        $data['success'] = "Kategori ekleme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function update()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $title = $this->request->getPost('title');
            $parent_id = $this->request->getPost('parent_id');
            $description = $this->request->getPost('description');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $slug  = $this->request->getPost('slug');
            $is_active  = $this->request->getPost('is_active');
            $commission_rate  = $this->request->getPost('commission_rate');
            $type  = $this->request->getPost('type');
            if (!$slug) {
                $slug = sef_link($title);
            }else {
                $slug = sef_link($slug);
            }
            $db = db_connect();
            $category = new Category($db);
            $categoryFind = $category->c_one([
                "id" => $id
            ]);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen kategori için bir başlık giriniz.';
            }elseif (!$validation->check($parent_id, 'required')) {
                $data['error'] =  'Lütfen kategorinin üst kategorisini belirleyiniz.';
            }else{

                helper('text');
                $menuPicture = $this->request->getFile('menuPicture');
                if ($menuPicture->getClientExtension()) {
                    $imageName = $menuPicture->getRandomName();
                    $imageExt = $menuPicture->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('menuPicture'))
                    ->fit(350, 350, 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/category/menuPicture/".$imageName);
                }else{
                    $imageName = $categoryFind->menuPicture;
                }

                $categoryCheack = $category->c_one([
                    "slug" => $slug,
                    "parent_id" => $parent_id,
                    "id !=" => $id
                ]);
                if ($categoryCheack) {
                    $data['error'] = "Girmek istediğiniz kategori mevcut lütfen kontrol ederek tekrar deneyiniz.";
                }else{
                    $dataInsert = [
                        "title" => $title,
                        "parent_id" => $parent_id,
                        "description" => $description,
                        "menuPicture" => $imageName,
                        "seo_title" => $seo_title,
                        "seo_description" => $seo_description,
                        "commission_rate" => $commission_rate,
                        "type" => $type,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "rank" => '1',
                        "created_at" => created_at(),
                    ];
                    $updateCategory = $category->edit($id,$dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Kategori düzenleme işlemi başarılı bir şekilde yapıldı.";
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
            $category = new Category($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $category->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $category->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function statusCimri()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $value = $this->request->getPost('value');
            $db = db_connect();
            $category = new Category($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $category->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_cimri" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $category->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function statusAkakce()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $value = $this->request->getPost('value');
            $db = db_connect();
            $category = new Category($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $category->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_akakce" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $category->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function statusMubiko()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $value = $this->request->getPost('value');
            $db = db_connect();
            $category = new Category($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $category->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_mubiko" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $category->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
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
            $category = new Category($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $categoryCheack = $category->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz kategoru mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteCategory = $category->delete($id);
                    if ($deleteCategory) {
                        $data['success'] = "Kategori başarılı bir şekilde silindi.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function deleteImage()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('value');
            $db = db_connect();
            $category = new Category($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $categoryCheack = $category->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Resim silmek istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "menuPicture" => '',
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $category->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Resim silme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

    }
