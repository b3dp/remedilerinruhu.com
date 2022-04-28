<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\BlogModels;

    class Blog extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index()
        {
            $db = db_connect();
            $blogModels = new BlogModels($db);
            $data['blogList'] = $blogModels->c_all();
            $data['blogCount'] = $blogModels->count();

            return view ("admin/blog/blog-list", $data);
        }

        public function add()
        {
            $data["title"] = "Kategoriler Ekle";
            $data = [
                'blog_w' => getenv('picture.blog_w'),
                'blog_h' => getenv('picture.blog_h'),
                'blog_cover_w' => getenv('picture.blog_cover_w'),
                'blog_cover_h' => getenv('picture.blog_cover_h'),
            ];
            $db = db_connect();
            return view ("admin/blog/blog-add", $data);
        }

        public function edit($id)
        {
            $data = [
                'blog_w' => getenv('picture.blog_w'),
                'blog_h' => getenv('picture.blog_h'),
                'blog_cover_w' => getenv('picture.blog_cover_w'),
                'blog_cover_h' => getenv('picture.blog_cover_h'),
            ];
            $db = db_connect(); 
            $blogModels = new BlogModels($db);
            $data['blog'] = $blogModels->c_one(['id' => $id]);
            return view ("admin/blog/blog-edit", $data);
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
            if ($this->validate(['pictureCover' => 'uploaded[pictureCover]'])) {
                $pictureCover = $this->request->getFile('pictureCover');
            }
            if ($this->validate(['picture' => 'uploaded[picture]'])) {
                $picture = $this->request->getFile('picture');
            }
            $tags = json_decode($this->request->getPost('tags'), FALSE);
            foreach ($tags as $row) {
                $tag .=  $row->value.',';
                $tag_slug .=  sef_link($row->value).',';
            }
            $tag = rtrim($tag, ',');
            $tag_slug = rtrim($tag_slug, ',');
            $short_description = $this->request->getPost('short_description');
            $description = $this->request->getPost('description');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $db = db_connect();
            $blogModels = new BlogModels($db);
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen blog için bir başlık giriniz.';
            }elseif (!$validation->check($pictureCover, 'required')) {
                $data['error'] =  'Lütfen blog için kapak resmi seçiniz.';
            }elseif (!$validation->check($picture, 'required')) {
                $data['error'] =  'Lütfen blog resmi seçiniz.';
            }else{
                helper('text');

                if ($pictureCover) {
                    $imageNameCover = $pictureCover->getRandomName();
                    $imageExt = $pictureCover->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('pictureCover'))
                    ->fit(getenv('picture.blog_cover_w'), getenv('picture.blog_cover_h'), 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/blog/cover/".$imageNameCover);
                }
    
                if ($picture) {
                    $imageName = $picture->getRandomName();
                    $imageExt = $picture->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('picture'))
                    ->fit(getenv('picture.blog_w'), getenv('picture.blog_h'), 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/blog/".$imageName);
                }
    
                $dataInsert = [
                    "title" => $title,
                    "slug" => $slug,
                    "picture_cover" => $imageNameCover,
                    "picture" => $imageName,
                    "tag" => $tag,
                    "tag_slug" => $tag_slug,
                    "short_description" => $short_description,
                    "description" => $description,
                    "seo_title" => $seo_title,
                    "seo_description" => $seo_description,
                    "is_active" => $is_active,
                    "created_at" => created_at(),
                ];
                $insertData = $blogModels->add($dataInsert);
                if ($insertData) {
                    $data['success'] = "Blog ekleme işlemi başarılı bir şekilde yapıldı.";
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
            $tags = json_decode($this->request->getPost('tags'), FALSE);
            foreach ($tags as $row) {
                $tag .=  $row->value.',';
                $tag_slug .=  sef_link($row->value).',';
            }
            $tag = rtrim($tag, ',');
            $tag_slug = rtrim($tag_slug, ',');
            $short_description = $this->request->getPost('short_description');
            $description = $this->request->getPost('description');
            $seo_title = $this->request->getPost('seo_title');
            $seo_description = $this->request->getPost('seo_description');
            $db = db_connect();
            $blogModels = new BlogModels($db);

            $blogFind = $blogModels->c_one([
                "id =" => $id
            ]);
            
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen blog için bir başlık giriniz.';
            }elseif(!$blogFind) {
                $data['error'] =  'Düzenlemek istediğiniz blog bulunamadı.';
            }else{

                helper('text');
                if ( $this->validate(['pictureCover' => 'uploaded[pictureCover]']) ) {
                    $pictureCover = $this->request->getFile('pictureCover');
                    $imageNameCover = $pictureCover->getRandomName();
                    $imageExt = $pictureCover->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('pictureCover'))
                    ->fit(getenv('picture.blog_cover_w'), getenv('picture.blog_cover_h'), 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/blog/cover/".$imageNameCover);
                }else{
                    $imageNameCover = $blogFind->picture_cover;
                }
    
                if ($this->validate(['picture' => 'uploaded[picture]'])) {
                    $picture = $this->request->getFile('picture');
                    $imageName = $picture->getRandomName();
                    $imageExt = $picture->getClientExtension();
        
                    $image = \Config\Services::image()
                    ->withFile($this->request->getFile('picture'))
                    ->fit(getenv('picture.blog_w'), getenv('picture.blog_h'), 'center')
                    ->convert(IMAGETYPE_WEBP)
                    ->save("./uploads/blog/".$imageName);
                }else{
                    $imageName = $blogFind->picture;
                }
    
                $dataInsert = [
                    "title" => $title,
                    "slug" => $slug,
                    "picture_cover" => $imageNameCover,
                    "picture" => $imageName,
                    "tag" => $tag,
                    "tag_slug" => $tag_slug,
                    "short_description" => $short_description,
                    "description" => $description,
                    "seo_title" => $seo_title,
                    "seo_description" => $seo_description,
                    "is_active" => $is_active,
                    "updated_at" => created_at(),
                ];
                $insertData = $blogModels->edit($id, $dataInsert);
                if ($insertData) {
                    $data['success'] = "Blog düzenleme işlemi başarılı bir şekilde yapıldı.";
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
            $blogModels = new BlogModels($db);
            if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $blogModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "is_active" => $value,
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $blogModels->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }

        public function coverDeleteImg()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('value');
            $db = db_connect();
            $blogModels = new BlogModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $blogModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Resim silmek istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "picture_cover" => '',
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $blogModels->edit($id, $dataInsert);
                    if ($updateCategory) {
                        $data['success'] = "Resim silme işlemi başarılı bir şekilde yapıldı.";
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
            $blogModels = new BlogModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Şeçili bir değer bulunamadı.';
            }else{
                $categoryCheack = $blogModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Resim silmek istediğiniz veri bulunamadı.";
                }else{
                    $dataInsert = [
                        "picture" => '',
                        "updated_at" => created_at(),
                    ];
                    $updateCategory = $blogModels->edit($id, $dataInsert);
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
            $blogModels = new BlogModels($db);
            if (!$validation->check($id, 'required')) {
                $data['error'] =  'Silmek istediğiniz veri bulunamadı';
            }else{
                $categoryCheack = $blogModels->c_one([
                    "id" => $id
                ]);
                if (!$categoryCheack) {
                    $data['error'] = "Silmeye çaliştiğiniz yazı mevcut değil lütfen daha sonra tekrar deneyiniz..";
                }else{
                    $deleteCategory = $blogModels->delete($id);
                    if ($deleteCategory) {
                        $data['success'] = "Yazı başarılı bir şekilde silindi.";
                    }else{
                        $data['error'] = "Beklenmeyen bir hata oluştu.";
                    }
                }
            }
            return json_encode($data);
        }
    }
