<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\AttributeModels;
    use App\Models\AttributeGroupModels;
    use App\Models\ProductModels;
    use App\Models\ProductFeatureModels;

    class Attribute extends BaseController
    { 
        public function _construct()
        {
        }

        
        public function index($group_id, $page = '1')
        {
            $data['sidebarAltActive'] = 'attribute/group-list';
            $data['sidebarActive'] = 'attribute';
            $data["title"] = "Nitelikler";
            $db = db_connect();
            $attributeModels = new AttributeModels($db);
            $attributeGroupModels = new AttributeGroupModels($db);
            $data['attributeCount'] = $attributeModels->count(["a.attribute_group_id" => $group_id, 'a.is_active' => '2']);

            $item = 9;
            if (!$page) {
                $page = 1;
            }
            $data['page'] = $page;
            $totalItem = $data['attributeCount'];
            $totalPage = ceil($totalItem / $item);
            if ($totalPage < $page) {
                $page = 1;
                $whereStart = ($page * $item) - $item;
            }else{
                $whereStart = ($page * $item) - $item;
            }
            $data['totalPage'] = $totalPage;

            $data['attributeList'] = $attributeModels->c_all(["a.attribute_group_id" => $group_id, 'a.is_active' => '2'], '', '', ['whereStart' => $whereStart, 'item' => $item]);
            $data['attributeGroup'] = $attributeGroupModels->c_one(["id" => $group_id]);
            $data['group_id'] = $group_id;

            return view ("admin/attribute/attribute-list", $data);
        }

        public function add($group_id)
        {
            $data['sidebarAltActive'] = 'attribute/group-list';
            $data['sidebarActive'] = 'attribute';
            $db = db_connect();
            $attributeGroupModels = new AttributeGroupModels($db);
            $attributeModels = new AttributeModels($db);
            $data['attributeGroup'] = $attributeGroupModels->c_one(["id" => $group_id]);
            $data['categoriesList'] = categoriesAddViewProduct($attributeModels->c_all_list());
            return view ("admin/attribute/attribute-add", $data);
        }

        public function edit($group_id, $id)
        {
            $data['sidebarAltActive'] = 'attribute/group-list';
            $data['sidebarActive'] = 'attribute';
            $db = db_connect();
            $attributeModels = new AttributeModels($db);
            $data['attributeFind'] = $attributeModels->c_one([
                "a.id" => $id
            ]);
            $attributeGroupModels = new AttributeGroupModels($db);
            $data['attributeGroup'] = $attributeGroupModels->c_one(["id" => $group_id]);
            $categoryArray = explode(',', $data['attributeFind']->category_id);
            $data['categoriesList'] = categoriesAddViewProduct($attributeModels->c_all_list(), '' , $categoryArray);
            return view ("admin/attribute/attribute-edit", $data);
        }
        
        public function changeView()
        {
            $data["title"] = "Nitelikler";
            $db = db_connect();
            $attributeModels = new AttributeModels($db);
            $attributeGroupModels = new AttributeGroupModels($db);
            $data['attributeList'] = $attributeModels->c_all(["a.attribute_group_id" => '5', "a.is_active" => '1' ]);
            $data['attributeListTwo'] = $attributeModels->c_all(["a.attribute_group_id" => '5', "a.is_active" => '2']);

            return view ("admin/attribute/attribute-change", $data);
        }

        public function insert()
        {
            $validation =  \Config\Services::validation();
            $group_id = $this->request->getPost('group_id');
            $parent_id = $this->request->getPost('parent_id');
            $title = $this->request->getPost('title');
            $slug = sef_link($title);
            $color = $this->request->getPost('color');
            $is_active = $this->request->getPost('is_active');
            $db = db_connect();
            $attributeModels = new AttributeModels($db);
            $attributeGroupModels = new AttributeGroupModels($db);

            $attributeGroupCheack = $attributeGroupModels->c_one([
                "id" => $group_id
            ]);

            if (!$attributeGroupCheack) {
                $data['error'] =  'Seçmiş oldugunuz nitelik grubu bulunamadı.';
            }elseif (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen nitelik için bir başlık giriniz.';
            }elseif (!$validation->check($color, 'required') && $attributeGroupCheack->is_color == '1') {
                $data['error'] =  'Lütfern renk paletinden eklemek istediğniiz rengi seçiniz.';
            }
                /*
                elseif ($attributeGroupCheack->is_category == '1' && !$validation->check($parent_id, 'required')) {
                    $data['error'] =  'Lütfern eklemek istediğiniz kategorileri seçiniz.';
                }
                */
            else{
                $attributeCheack = $attributeModels->c_one([
                    "a.title " => $title, 
                    "a.is_active" => '2'
                ]);
                if ($attributeCheack) {
                    $data['error'] = "Girmek istediğiniz nitelik değeri mevcut lütfen kontrol ederek tekrar deneyiniz.";
                }else{
                    $attributeLastRank = $attributeModels->lastRank([
                        "a.attribute_group_id" => $group_id
                    ]);
                    $lastRank = $attributeLastRank->rank + 1;
                    $dataInsert = [
                        "attribute_group_id" => $group_id,
                        "category_id" => rtrim($parent_id, ','),
                        "title" => $title,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "color" => $color,
                        "rank" => $lastRank,
                        "created_at" => created_at(),
                    ];
                    $insertAttribute = $attributeModels->add($dataInsert);
                    $attributeID = $db->insertID();
                    if ($insertAttribute) {
                        if ($attributeGroupCheack->is_category == '1') {
                            $categoryArray = explode(",", rtrim($parent_id, ','));
                            foreach ($categoryArray as $item) {
                                $dataInsertCategory = [
                                    "attribute_group_id" => $group_id,
                                    "attribute_id" => $attributeID,
                                    "category_id" => $item,
                                    "is_active" => "1",
                                    "created_at" => created_at()
                                ];
                                $insertAttribute = $attributeModels->addCategory($dataInsertCategory);
                            }
                        }
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
            $group_id = $this->request->getPost('group_id');
            $parent_id = $this->request->getPost('parent_id');
            $title = $this->request->getPost('title');
            $slug = sef_link($title);
            $color = $this->request->getPost('color');
            $is_active = $this->request->getPost('is_active');
            $db = db_connect();
            $attributeModels = new AttributeModels($db);
            $attributeGroupModels = new AttributeGroupModels($db);

            $attributeGroupCheack = $attributeGroupModels->c_one([
                "id" => $group_id
            ]);

            if (!$attributeGroupCheack) {
                $data['error'] =  'Seçmiş oldugunuz nitelik grubu bulunamadı.';
            }elseif (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen nitelik için bir başlık giriniz.';
            }elseif (!$validation->check($color, 'required') && $attributeGroupCheack->is_color == '1') {
                $data['error'] =  'Lütfern renk paletinden eklemek istediğniiz rengi seçiniz.';
            }elseif ($attributeGroupCheack->is_category == '1' && !$validation->check($parent_id, 'required')) {
                $data['error'] =  'Lütfern eklemek istediğiniz kategorileri seçiniz.';
            }else{
                $attributeCheack = $attributeModels->c_one([
                    "a.id " => $id
                ]);
                if (!$attributeCheack) {
                    $data['error'] = "Düzenlemek istediğiniz nitelik mevcut değil.";
                }else{
                    $dataInsert = [
                        "attribute_group_id" => $group_id,
                        "category_id" => rtrim($parent_id, ','),
                        "title" => $title,
                        "slug" => $slug,
                        "is_active" => $is_active,
                        "color" => $color,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $attributeModels->edit($id,$dataInsert);
                    if ($updateCategory) {
                        if ($attributeGroupCheack->is_category == '1') {
                            $deleteAttrCategory = $attributeModels->deleteAttrCategory($id);
                            $categoryArray = explode(",", rtrim($parent_id, ','));
                            foreach ($categoryArray as $item) {
                                $dataInsertCategory = [
                                    "attribute_group_id" => $group_id,
                                    "attribute_id" => $id,
                                    "category_id" => $item,
                                    "is_active" => "1",
                                    "created_at" => created_at()
                                ];
                                $insertAttribute = $attributeModels->addCategory($dataInsertCategory);
                            }
                        }
                        $data['success'] = "Nitelik düzenleme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function change()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $selectAttr = $this->request->getPost('selectAttr');
            $thisAtrr = $this->request->getPost('thisAtrr');
            $db = db_connect();
            $attributeModels = new AttributeModels($db);
            $attributeGroupModels = new AttributeGroupModels($db);
            $productModels = new ProductModels($db);
            $productFeatureModels = new ProductFeatureModels($db);

            $attributeCheackOne = $attributeModels->c_one([
                "a.id " => $thisAtrr
            ]);
            if ($attributeCheackOne) {
                foreach ($selectAttr as $row) {
                    $attributeCheack = $attributeModels->c_one([
                        "a.id " => $row
                    ]);
                    if ($attributeCheack) {
                        $productModels->productattributecombinationUpdate(['attribute_id' => $row], ['attribute_id' => $thisAtrr]);
                        $productFeatureModels->editAttr(['attribute_id' => $row], ['attribute_id' => $thisAtrr]);
                        $attributeModels->edit($row, ['is_active' => '99']);
                        $attribute_change = $attributeModels->attribute_change_c_one(['nebim_id' => $row, 'biltstore_id' => $thisAtrr]);
                        if (!$attribute_change) {
                            $attribute_change_add = $attributeModels->attribute_change_add([
                                'nebim_id' => $row,
                                'biltstore_id' => $thisAtrr,
                                'created_at' => created_at(),
                            ]);
                        }
                    }
                }
            }else{
                $data['error'] = 'Seçmiş olduğunuz özellik hatalıdır.';
            }
            $data['success'] = 'Başarılı bir şekilde değiştirildi.';
            return json_encode($data);
        }
        
        public function status()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $value = $this->request->getPost('value');
            $db = db_connect();
            $attributeModels = new AttributeModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $attributeCheack = $attributeModels->c_one([
                    "a.id" => $id
                ]);
                if (!$attributeCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateAttribute = $attributeModels->edit($id, $dataInsert);
                    if ($updateAttribute) {
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
            $attributeModels = new AttributeModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $attributeCheack = $attributeModels->c_one([
                    "a.id" => $id
                ]);
                if (!$attributeCheack) {
                    $data['error'] = "Sira değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "rank" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateAttribute = $attributeModels->edit($id, $dataInsert);
                    if ($updateAttribute) {
                        $data['success'] = "Sıralama değiştirme işlemi başarılı bir şekilde yapıldı.";
                        if ($attributeCheack->rank > $value) {
                            $status = '1';
                        }else{
                            $status = '2';
                        }

                        $updateAttribute = $attributeModels->rank($id, $value, $attributeCheack->rank, $attributeCheack->attribute_group_id, $status);
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
            $attributeModels = new AttributeModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $attributeCheack = $attributeModels->c_one([
                    "a.id" => $id
                ]);
                if (!$attributeCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz nitelik mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteAttribute = $attributeModels->delete($id);
                    if ($deleteAttribute) {
                        $data['success'] = "Nitelik değeri başarılı bir şekilde silindi.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

    }
