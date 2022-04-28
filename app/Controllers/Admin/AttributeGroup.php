<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\AttributeGroupModels;

    class AttributeGroup extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index()
        {
            $data["title"] = "Nitelikler";
            $db = db_connect();
            $attributeGroupModels = new AttributeGroupModels($db);
            $data['attributeList'] = $attributeGroupModels->c_all();
            $data['attributeCount'] = $attributeGroupModels->count();

            return view ("admin/attribute/attribute-group-list", $data);
        }

        public function add()
        {
            $data["title"] = "Kategoriler Ekle";
            return view ("admin/attribute/attribute-group-add", $data);
        }

        public function edit($id)
        {
            $db = db_connect();
            $attributeGroupModels = new AttributeGroupModels($db);
            $data['attributeGroupFind'] = $attributeGroupModels->c_one([
                "id" => $id
            ]);
            return view ("admin/attribute/attribute-group-edit", $data);
        }
        
        public function insert()
        {
            $validation =  \Config\Services::validation();
            $title = $this->request->getPost('title');
            $slug = sef_link($title);
            $description = $this->request->getPost('description');
            $is_category = $this->request->getPost('is_category');
            $is_active = $this->request->getPost('is_active');
            $group_type = $this->request->getPost('group_type');
            if ($group_type == 'color') {
                $is_color = '1';
            }else{
                $is_color = '0';
            }
            $db = db_connect();
            $attributeGroupModels = new AttributeGroupModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen nitelik için bir başlık giriniz.';
            }elseif (!$validation->check($group_type, 'required')) {
                $data['error'] =  'Lütfen Niteliğin türünü belirleyiniz.';
            }else{
                $attributeGroupCheack = $attributeGroupModels->c_one([
                    "title" => $title
                ]);
                if ($attributeGroupCheack) {
                    $data['error'] = "Girmek istediğiniz nitelik mevcut lütfen kontrol ederek tekrar deneyiniz.";
                }else{
                    $dataInsert = [
                        "title" => $title,
                        "slug" => $slug,
                        "description" => $description,
                        "is_category" => $is_category,
                        "is_active" => $is_active,
                        "is_color" => $is_color,
                        "group_type" => $group_type,
                        "rank" => '1',
                        "created_at" => created_at(),
                    ];
                    $insertAttributeGroup = $attributeGroupModels->add($dataInsert);
                    if ($insertAttributeGroup) {
                        $data['success'] = "Nitelik ekleme işlemi başarılı bir şekilde yapıldı.";
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
            $is_category = $this->request->getPost('is_category');
            $is_active = $this->request->getPost('is_active');
            $group_type = $this->request->getPost('group_type');
            if ($group_type == 'color') {
                $is_color = '1';
            }else{
                $is_color = '0';
            }
            $db = db_connect();
            $attributeGroupModels = new AttributeGroupModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen nitelik için bir başlık giriniz.';
            }elseif (!$validation->check($group_type, 'required')) {
                $data['error'] =  'Lütfen Niteliğin türünü belirleyiniz.';
            }else{
                $attributeGroupCheack = $attributeGroupModels->c_one([
                    "id " => $id
                ]);
                if (!$attributeGroupCheack) {
                    $data['error'] = "Düzenlemek istediğiniz nitelik mevcut değil.";
                }else{
                    $dataInsert = [
                        "title" => $title,
                        "slug" => $slug,
                        "description" => $description,
                        "is_category" => $is_category,
                        "is_active" => $is_active,
                        "is_color" => $is_color,
                        "group_type" => $group_type,
                        "rank" => '1',
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $attributeGroupModels->edit($id,$dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Nitelik düzenleme işlemi başarılı bir şekilde yapıldı.";
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
            $attributeGroupModels = new AttributeGroupModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $attributeGroupCheack = $attributeGroupModels->c_one([
                    "id" => $id
                ]);
                if (!$attributeGroupCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateAttributeGroup = $attributeGroupModels->edit($id, $dataInsert);
                    if ($updateAttributeGroup) {
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
            $attributeGroupModels = new AttributeGroupModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $attributeGroupCheack = $attributeGroupModels->c_one([
                    "id" => $id
                ]);
                if (!$attributeGroupCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz nitelik mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteAttributeGroup = $attributeGroupModels->delete($id);
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
