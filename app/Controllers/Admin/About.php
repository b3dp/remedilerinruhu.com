<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\AboutModels;

    class About extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index()
        {
            $data['sidebarAltActive'] = 'about/list';
            $data['sidebarActive'] = 'about';
            $db = db_connect();
            $aboutModels = new AboutModels($db);
            $data['aboutList'] = $aboutModels->c_all();
            $data['aboutCount'] = $aboutModels->count();

            return view ("admin/about/about-list", $data);
        }

        public function add()
        {
            $data['sidebarAltActive'] = 'about/list';
            $data['sidebarActive'] = 'about';
            $data["title"] = "Kategoriler Ekle";
            $data = [
                'about_w' => getenv('picture.about_w'),
                'about_h' => getenv('picture.about_h'),
                'about_cover_w' => getenv('picture.about_cover_w'),
                'about_cover_h' => getenv('picture.about_cover_h'),
            ];
            $db = db_connect();
            return view ("admin/about/about-add", $data);
        }

        public function edit($id)
        {
            $data['sidebarAltActive'] = 'about/list';
            $data['sidebarActive'] = 'about';
            $data = [
                'about_w' => getenv('picture.about_w'),
                'about_h' => getenv('picture.about_h'),
                'about_cover_w' => getenv('picture.about_cover_w'),
                'about_cover_h' => getenv('picture.about_cover_h'),
            ];
            $db = db_connect(); 
            $aboutModels = new AboutModels($db);
            $data['about'] = $aboutModels->c_one(['id' => $id]);
            return view ("admin/about/about-edit", $data);
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
            $is_active = $this->request->getPost('is_active');
            if ($this->validate(['picture' => 'uploaded[picture]'])) {
                $picture = $this->request->getFile('picture');
            }
            $description = $this->request->getPost('description');
            $db = db_connect();
            $aboutModels = new AboutModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen yazi için bir başlık giriniz.';
            }else{
                helper('text');
    
                if ($picture) {
                    $imageName = $picture->getRandomName();
                    $imageExt = $picture->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('picture'))
                    ->fit(getenv('picture.about_w'), getenv('picture.about_h'), 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/about/".$imageName);
                }
    
                $dataInsert = [
                    "title" => $title,
                    "slug" => $slug,
                    "picture_cover" => $imageNameCover,
                    "picture" => $imageName,
                    "description" => $description,
                    "is_active" => $is_active,
                    "created_at" => created_at(),
                ];
                $insertData = $aboutModels->add($dataInsert);
                if ($insertData) {
                    $data['success'] = "Hakkımızda yazısı ekleme işlemi başarılı bir şekilde yapıldı.";
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
            $is_active = $this->request->getPost('is_active');
            if ($this->validate(['picture' => 'uploaded[picture]'])) {
                $picture = $this->request->getFile('picture');
            }
            $description = $this->request->getPost('description');
            $db = db_connect();
            $aboutModels = new AboutModels($db);

            $aboutFind = $aboutModels->c_one([
                "id =" => $id
            ]);
            
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen yazi için bir başlık giriniz.';
            }elseif(!$aboutFind) {
                $data['error'] =  'Düzenlemek istediğiniz about bulunamadı.';
            }else{

                helper('text');
                if ($picture) {
                    $imageName = $picture->getRandomName();
                    $imageExt = $picture->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('picture'))
                    ->fit(getenv('picture.about_w'), getenv('picture.about_h'), 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/about/".$imageName);
                }else{
                    $imageName = $aboutFind->picture;
                }
    
                $dataInsert = [
                    "title" => $title,
                    "slug" => $slug,
                    "picture_cover" => $imageNameCover,
                    "picture" => $imageName,
                    "description" => $description,
                    "is_active" => $is_active,
                    "updated_at" => created_at(),
                ];
                $insertData = $aboutModels->edit($id, $dataInsert);
                if ($insertData) {
                    $data['success'] = "Hakkımızda yazısı düzenleme işlemi başarılı bir şekilde yapıldı.";
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
            $aboutModels = new AboutModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $aboutModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $aboutModels->edit($id, $dataInsert);
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
            $aboutModels = new AboutModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $aboutModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Resim silmek istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "picture" => '',
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $aboutModels->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Resim silme işlemi başarılı bir şekilde yapıldı.";
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
            $aboutModels = new AboutModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $categoryCheack = $aboutModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz yazı mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteCategory = $aboutModels->delete($id);
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
            $aboutModels = new AboutModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $attributeCheack = $aboutModels->c_one([
                    "id" => $id
                ]);
                if (!$attributeCheack) {
                    $data['error'] = "Sira değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "rank" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateAttribute = $aboutModels->edit($id, $dataInsert);
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
