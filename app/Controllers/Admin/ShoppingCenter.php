<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\ShoppingCenterModels;
    use App\Models\AddressModels;

    class ShoppingCenter extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index()
        {
            $db = db_connect();
            $shoppingCenter = new ShoppingCenterModels($db);
            $data['aboutList'] = $shoppingCenter->c_all();
            $data['aboutCount'] = $shoppingCenter->count();

            return view ("admin/shopping-center/shopping-center-list", $data);
        }

        public function add()
        {
            $db = db_connect();
            $addressModels = new AddressModels($db);
            $data["title"] = "Kategoriler Ekle";
            $data["cityFind"] = $addressModels->c_all("city", ['1' => '1']);
            $db = db_connect();
            return view ("admin/shopping-center/shopping-center-add", $data);
        }

        public function edit($id)
        {
            $db = db_connect(); 
            $shoppingCenter = new ShoppingCenterModels($db);
            $addressModels = new AddressModels($db);
            $data['about'] = $shoppingCenter->c_one(['id' => $id]);
            $data["cityFind"] = $addressModels->c_all("city", ['1' => '1']);
            $data["townFind"] = $addressModels->c_all("town", ['CityID' => $data['about']->city]);
            return view ("admin/shopping-center/shopping-center-edit", $data);
        }
        
        public function insert()
        {
            $validation =  \Config\Services::validation();
            $title = $this->request->getPost('title');
            $title_nebim = $this->request->getPost('title_nebim');
            $nebim_code = $this->request->getPost('nebim_code');
            $nebim_slug = $this->request->getPost('nebim_slug');
            $address = $this->request->getPost('address');
            $city = $this->request->getPost('city');
            $town = $this->request->getPost('town');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            $slug_slug = sef_link($title);
            if (!$slug) {
                $slug = sef_link($title);
            }else {
                $slug = sef_link($slug);
            }
            $is_active = $this->request->getPost('is_active');
           
            $db = db_connect();
            $shoppingCenter = new ShoppingCenterModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen Mağaza adını giriniz.';
            }else{
               
                $dataInsert = [
                    "title" => $title,
                    "title_nebim" => $title_nebim,
                    "nebim_code" => $nebim_code,
                    "slug" => $nebim_slug,
                    "address" => $address,
                    "city" => $city,
                    "town" => $town,
                    "phone" => $phone,
                    "email" => $email,
                    "slug_slug" => $slug,
                    "is_active" => $is_active,
                    "created_at" => created_at(),
                ];

                $insertData = $shoppingCenter->add($dataInsert);
                if ($insertData) {
                    $data['success'] = "Mağaza ekleme işlemi başarılı bir şekilde yapıldı.";
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
            $title_nebim = $this->request->getPost('title_nebim');
            $nebim_code = $this->request->getPost('nebim_code');
            $nebim_slug = $this->request->getPost('nebim_slug');
            $address = $this->request->getPost('address');
            $city = $this->request->getPost('city');
            $town = $this->request->getPost('town');
            $phone = $this->request->getPost('phone');
            $email = $this->request->getPost('email');
            $slug_slug = sef_link($title);
            if (!$slug) {
                $slug = sef_link($title);
            }else {
                $slug = sef_link($slug);
            }
            $is_active = $this->request->getPost('is_active');
            $db = db_connect();
            $shoppingCenter = new ShoppingCenterModels($db);

            $aboutFind = $shoppingCenter->c_one([
                "id =" => $id
            ]);
            
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Mağaza adini giriniz.';
            }elseif(!$aboutFind) {
                $data['error'] =  'Düzenlemek istediğiniz about bulunamadı.';
            }else{

                $dataInsert = [
                    "title" => $title,
                    "title_nebim" => $title_nebim,
                    "nebim_code" => $nebim_code,
                    "slug" => $nebim_slug,
                    "address" => $address,
                    "city" => $city,
                    "town" => $town,
                    "phone" => $phone,
                    "email" => $email,
                    "slug_slug" => $slug,
                    "is_active" => $is_active,
                    "updated_at" => created_at(),
                ];
                $insertData = $shoppingCenter->edit($id, $dataInsert);
                if ($insertData) {
                    $data['success'] = "Mağaza düzenleme işlemi başarılı bir şekilde yapıldı.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
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
            $shoppingCenter = new ShoppingCenterModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $shoppingCenter->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $shoppingCenter->edit($id, $dataInsert);
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
            $shoppingCenter = new ShoppingCenterModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $categoryCheack = $shoppingCenter->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz yazı mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteCategory = $shoppingCenter->delete($id);
                    if ($deleteCategory) {
                        $data['success'] = "Yazı başarılı bir şekilde silindi.";
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
            $shoppingCenter = new ShoppingCenterModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $attributeCheack = $shoppingCenter->c_one([
                    "id" => $id
                ]);
                if (!$attributeCheack) {
                    $data['error'] = "Sira değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "rank" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateAttribute = $shoppingCenter->edit($id, $dataInsert);
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
