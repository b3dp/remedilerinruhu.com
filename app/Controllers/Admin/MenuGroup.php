<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\MenuGroupModels;

    class MenuGroup extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index()
        {
            $db = db_connect();
            $menuGroupModels = new MenuGroupModels($db);
            $data['menuList'] = $menuGroupModels->c_all();
            $data['menuCount'] = $menuGroupModels->count();

            return view ("admin/menu/menu-group-list", $data);
        }

        public function add()
        {
            $data["title"] = "Kategoriler Ekle";
            return view ("admin/menu/menu-group-add", $data);
        }

        public function edit($id)
        {
            $db = db_connect();
            $menuGroupModels = new MenuGroupModels($db);
            $data['menuGroupFind'] = $menuGroupModels->c_one([
                "id" => $id
            ]);
            return view ("admin/menu/menu-group-edit", $data);
        }
        
        public function insert()
        {
            $validation =  \Config\Services::validation();
            $title = $this->request->getPost('title');
            $slug = sef_link($title);
            $description = $this->request->getPost('description');
            $is_active = $this->request->getPost('is_active');
         
            $db = db_connect();
            $menuGroupModels = new MenuGroupModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen kategori için bir başlık giriniz.';
            }else{
                $attributeGroupCheack = $menuGroupModels->c_one([
                    "title" => $title
                ]);
                if ($attributeGroupCheack) {
                    $data['error'] = "Girmek istediğiniz kategori mevcut lütfen kontrol ederek tekrar deneyiniz.";
                }else{
                    $dataInsert = [
                        "title" => $title,
                        "slug" => $slug,
                        "description" => $description,
                        "is_active" => $is_active,
                        "created_at" => created_at(),
                    ];
                    $insertAttributeGroup = $menuGroupModels->add($dataInsert);
                    if ($insertAttributeGroup) {
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
            $slug = sef_link($title);
            $description = $this->request->getPost('description');
            $is_active = $this->request->getPost('is_active');
            $db = db_connect();
            $menuGroupModels = new MenuGroupModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen kategori için bir başlık giriniz.';
            }else{
                $attributeGroupCheack = $menuGroupModels->c_one([
                    "id " => $id
                ]);
                if (!$attributeGroupCheack) {
                    $data['error'] = "Düzenlemek istediğiniz kategori mevcut değil.";
                }else{
                    $dataInsert = [
                        "title" => $title,
                        "slug" => $slug,
                        "description" => $description,
                        "is_active" => $is_active,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $menuGroupModels->edit($id,$dataInsert);
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
            $menuGroupModels = new MenuGroupModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $attributeGroupCheack = $menuGroupModels->c_one([
                    "id" => $id
                ]);
                if (!$attributeGroupCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateAttributeGroup = $menuGroupModels->edit($id, $dataInsert);
                    if ($updateAttributeGroup) {
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
            $menuGroupModels = new MenuGroupModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $attributeGroupCheack = $menuGroupModels->c_one([
                    "id" => $id
                ]);
                if (!$attributeGroupCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "rank" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateAttributeGroup = $menuGroupModels->edit($id, $dataInsert);
                    if ($updateAttributeGroup) {
                        $data['success'] = "Sıralama değiştirme işlemi başarılı bir şekilde yapıldı.";
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
            $menuGroupModels = new MenuGroupModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $attributeGroupCheack = $menuGroupModels->c_one([
                    "id" => $id
                ]);
                if (!$attributeGroupCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz nitelik mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteAttributeGroup = $menuGroupModels->delete($id);
                    if ($deleteAttributeGroup) {
                        $data['success'] = "Nitelik başarılı bir şekilde silindi.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }
    }
