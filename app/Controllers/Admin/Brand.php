<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\BrandModels;

    class Brand extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index()
        {
            $data["title"] = "Kategoriler";
            $data['sidebarActive'] = 'brand';
            $db = db_connect();
            $brandModels = new BrandModels($db);
            $data['brandList'] = $brandModels->c_all('1 = 1', 'is_popular DESC, rank ASC, id DESC');
            $data['brandCount'] = $brandModels->count();

            return view ("admin/brand/brand-list", $data);
        }

        public function add()
        {
            $data["title"] = "Kategoriler Ekle";
            $data['sidebarActive'] = 'brand';
            $db = db_connect();
            return view ("admin/brand/brand-add", $data);
        }

        public function edit($id)
        {
            $data["title"] = "Kategori Düzenle";
            $data['sidebarActive'] = 'brand';
            $db = db_connect();
            $brandModels = new BrandModels($db);
            $data['brandFind'] = $brandModels->c_one([
                "id" => $id
            ]);
            return view ("admin/brand/brand-edit", $data);
        }
        
        public function insert()
        {
            $validation =  \Config\Services::validation();
            $title = $this->request->getPost('title');
            $logo = $this->request->getFile('logo');
            $description = $this->request->getPost('description');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $slug  = $this->request->getPost('slug');
            $is_active  = $this->request->getPost('is_active');
            $is_cimri  = $this->request->getPost('is_cimri');
            $is_akakce  = $this->request->getPost('is_akakce');
            $is_mubiko  = $this->request->getPost('is_mubiko');
            if (!$slug) {
                $slug = sef_link($title);
            }else {
                $slug = sef_link($slug);
            }
            $db = db_connect();
            $brandModels = new BrandModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen Marka için bir başlık giriniz.';
            }else{

                helper('text');
                $logo = $this->request->getFile('logo');
                if ($logo) {
                    $imageName = $logo->getRandomName();
                    $imageExt = $logo->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('logo'))
                    ->fit(400, 400, 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/brand/".$imageName);
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('logo'))
                    ->fit(200, 200, 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/brand/min/".$imageName);
                }

                $categoryCheack = $brandModels->c_one([
                    "slug" => $slug,
                ]);
                if ($categoryCheack) {
                    $data['error'] = "Girmek istediğiniz marka mevcut lütfen kontrol ederek tekrar deneyiniz.";
                }else{
                    $dataInsert = [
                        "title" => $title,
                        "image" => $imageName,
                        "description" => $description,
                        "seo_title" => $seo_title,
                        "seo_description" => $seo_description,
                        "slug" => $slug,
                        "is_cimri" => $is_cimri,
                        "is_akakce" => $is_akakce,
                        "is_mubiko" => $is_mubiko,
                        "is_active" => $is_active,
                        "created_at" => created_at(),
                    ];
                    $insertCategory = $brandModels->add($dataInsert);
                    if ($insertCategory) {
                        $data['success'] = "Marka ekleme işlemi başarılı bir şekilde yapıldı.";
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
            $description = $this->request->getPost('description');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $slug  = $this->request->getPost('slug');
            $is_active  = $this->request->getPost('is_active');
            if (!$slug) {
                $slug = sef_link($title);
            }else {
                $slug = sef_link($slug);
            }
            $db = db_connect();
            $brandModels = new BrandModels($db);

            $brandFind = $brandModels->c_one([
                "id =" => $id
            ]);

            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen Marka için bir başlık giriniz.';
            }elseif(!$brandFind) {
                $data['error'] =  'Düzenlemek istediğiniz marka bulunamadı.';
            }else{

                helper('text');
                $logo = $this->request->getFile('logo');
                if ($logo->getClientExtension()) {
                    $imageName = $logo->getRandomName();
                    $imageExt = $logo->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('logo'))
                    ->fit(400, 400, 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/brand/".$imageName);
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('logo'))
                    ->fit(200, 200, 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/brand/min/".$imageName);
                }else{
                    $imageName = $brandFind->image;
                }


                $brandCheack = $brandModels->c_one([
                    "slug" => $slug,
                    "id !=" => $id
                ]);
                if ($brandCheack) {
                    $data['error'] = "Girmek istediğiniz Marka mevcut lütfen kontrol ederek tekrar deneyiniz.";
                }else{
                    $dataInsert = [
                        "title" => $title,
                        "image" => $imageName,
                        "description" => $description,
                        "seo_title" => $seo_title,
                        "seo_description" => $seo_description,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $brandModels->edit($id,$dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Marka düzenleme işlemi başarılı bir şekilde yapıldı.";
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
            $brandModels = new BrandModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $brandModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $brandModels->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function deleteImg()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('value');
            $db = db_connect();
            $brandModels = new BrandModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $brandModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Resmini silmek istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "image" => '',
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $brandModels->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Resim Silme işlemi başarılı bir şekilde yapıldı.";
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
            $brandModels = new BrandModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $brandModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_cimri" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $brandModels->edit($id, $dataInsert);
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
            $brandModels = new BrandModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $brandModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_akakce" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $brandModels->edit($id, $dataInsert);
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
            $brandModels = new BrandModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $brandModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_mubiko" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $brandModels->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function populer()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $value = $this->request->getPost('value');
            $db = db_connect();
            $brandModels = new BrandModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $brandModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Populer marka değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_popular" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $brandModels->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Populer marka değiştirme işlemi başarılı bir şekilde yapıldı.";
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
            $brandModels = new BrandModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $categoryCheack = $brandModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz kategoru mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteCategory = $brandModels->delete($id);
                    if ($deleteCategory) {
                        $data['success'] = "Marka başarılı bir şekilde silindi.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }
    }
