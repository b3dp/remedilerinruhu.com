<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\FaqModels;
    use App\Models\FaqGroupModels;
    class Faq extends BaseController
    { 
        public function _construct()
        {
        }

        
        public function index($group_id, $page = '1')
        {
            $data['sidebarActive'] = 'faq';
            $data['sidebarAltActive'] = 'faq/group-list';
            $db = db_connect();
            $faqModels = new FaqModels($db);
            $faqGroupModels = new FaqGroupModels($db);
            $data['faqCount'] = $faqModels->count(["f.faq_group_id" => $group_id]);

            $item = 9;
            if (!$page) {
                $page = 1;
            }
            $data['page'] = $page;
            $totalItem = $data['faqCount'];
            $totalPage = ceil($totalItem / $item);
            if ($totalPage < $page) {
                $page = 1;
                $whereStart = ($page * $item) - $item;
            }else{
                $whereStart = ($page * $item) - $item;
            }
            $data['totalPage'] = $totalPage;

            $data['faqList'] = $faqModels->c_all(["f.faq_group_id" => $group_id], '', '', ['whereStart' => $whereStart, 'item' => $item]);
            $data['faqGroup'] = $faqGroupModels->c_one(["id" => $group_id]);
            $data['group_id'] = $group_id;

            return view ("admin/faq/faq-list", $data);
        }

        public function add($group_id)
        {
            $data['sidebarActive'] = 'faq';
            $data['sidebarAltActive'] = 'faq/group-list';
            $db = db_connect();
            $faqGroupModels = new FaqGroupModels($db);
            $faqModels = new FaqModels($db);
            $data['faqGroup'] = $faqGroupModels->c_one(["id" => $group_id]);
            return view ("admin/faq/faq-add", $data);
        }

        public function edit($group_id, $id)
        {
            $data['sidebarActive'] = 'faq';
            $data['sidebarAltActive'] = 'faq/group-list';
            $db = db_connect();
            $faqModels = new FaqModels($db);
            $data['faqFind'] = $faqModels->c_one([
                "f.id" => $id
            ]);
            $faqGroupModels = new FaqGroupModels($db);
            $data['faqGroup'] = $faqGroupModels->c_one(["id" => $group_id]);
            return view ("admin/faq/faq-edit", $data);
        }

        public function insert()
        {
            $validation =  \Config\Services::validation();
            $group_id = $this->request->getPost('group_id');
            $parent_id = $this->request->getPost('parent_id');
            $title = $this->request->getPost('title');
            $slug = sef_link($title);
            $description = $this->request->getPost('description');
            $is_active = $this->request->getPost('is_active');
            $db = db_connect();
            $faqModels = new FaqModels($db);
            $faqGroupModels = new FaqGroupModels($db);

            $faqGroupCheack = $faqGroupModels->c_one([
                "id" => $group_id
            ]);

            if (!$faqGroupCheack) {
                $data['error'] =  'Seçmiş oldugunuz kategori grubu bulunamadı.';
            }elseif (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen soru için bir başlık giriniz.';
            }else{
                $dataInsert = [
                    "faq_group_id" => $group_id,
                    "title" => $title,
                    "slug" => $slug,
                    "description" => $description,
                    "is_active" => $is_active,
                    "rank" => '1',
                    "created_at" => created_at(),
                ];
                $insertFaq = $faqModels->add($dataInsert);
                $faqID = $db->insertID();
                if ($insertFaq) {
                    $data['success'] = "Soru ekleme işlemi başarılı bir şekilde yapıldı.";
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
            $group_id = $this->request->getPost('group_id');
            $title = $this->request->getPost('title');
            $slug = sef_link($title);
            $description = $this->request->getPost('description');
            $is_active = $this->request->getPost('is_active');
            $db = db_connect();
            $faqModels = new FaqModels($db);
            $faqGroupModels = new FaqGroupModels($db);

            $faqGroupCheack = $faqGroupModels->c_one([
                "id" => $group_id
            ]);

            if (!$faqGroupCheack) {
                $data['error'] =  'Seçmiş oldugunuz SSS Kategorisi bulunamadı.';
            }elseif (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen nitelik için bir başlık giriniz.';
            }else{
                $faqCheack = $faqModels->c_one([
                    "f.id " => $id
                ]);
                if (!$faqCheack) {
                    $data['error'] = "Düzenlemek istediğiniz soru mevcut değil.";
                }else{
                    $dataInsert = [
                        "title" => $title,
                        "faq_group_id" => $group_id,
                        "slug" => $slug,
                        "description" => $description,
                        "is_active" => $is_active,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $faqModels->edit($id,$dataInsert);
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
            $faqModels = new FaqModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $faqCheack = $faqModels->c_one([
                    "f.id" => $id
                ]);
                if (!$faqCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateFaq = $faqModels->edit($id, $dataInsert);
                    if ($updateFaq) {
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
            $faqModels = new FaqModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $faqCheack = $faqModels->c_one([
                    "f.id" => $id
                ]);
                if (!$faqCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "rank" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateFaq = $faqModels->edit($id, $dataInsert);
                    if ($updateFaq) {
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
            $faqModels = new FaqModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $faqCheack = $faqModels->c_one([
                    "f.id" => $id
                ]);
                if (!$faqCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz soru mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteFaq = $faqModels->delete($id);
                    if ($deleteFaq) {
                        $data['success'] = "Soru başarılı bir şekilde silindi.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }
    }
