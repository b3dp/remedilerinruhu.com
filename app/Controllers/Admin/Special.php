<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SpecialModels;

class Special extends BaseController
{
    public function _construct()
    {
        
    }

    public function add()
    {
        return view ("admin/special/special-add");
    }
    
    public function edit($id)
    { 
        $db = db_connect();
        $specialModels = new SpecialModels($db);
        $data['special'] = $specialModels->c_one(['id' => $id]);
        $data['specialImage'] = $specialModels->c_all_image(['special_page_id' => $id]);
        if (!$data['special']) {
            return redirect()->route('special/list');
        }
        return view ("admin/special/special-edit", $data);
    }

    public function list()
    {
        $db = db_connect();
        $specialModels = new SpecialModels($db);

        $data['specials'] = $specialModels->c_all();
        return view ("admin/special/special-list", $data);
    }

    public function insert()
    {
        $validation =  \Config\Services::validation();
        $title = $this->request->getPost('title');
        $seo_title = $this->request->getPost('seo_title');
        $seo_description = $this->request->getPost('seo_description');
        $slug = $this->request->getPost('slug');
        $is_active = $this->request->getPost('is_active');
        if (!$slug) {
            $slug = sef_link($title);
        }else{
            $slug = sef_link($slug);
        }
        $db = db_connect();
        $specialModels = new SpecialModels($db);

        if (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen Özel Sayfa için bir başlık giriniz.';
        }else{
            $dataInsert = [
                "title" => $title,
                "slug" => $slug,
                "seo_title" => $seo_title,
                "seo_description" => $seo_description,
                "is_active" => $is_active,
                "rank" => '1',
                "created_at" => created_at()
            ];
            $insertSpecial = $specialModels->add($dataInsert);
            if ($insertSpecial) {
                $specialID =  $db->insertID();;
                helper('text');
                if ($this->request->getFileMultiple('picture')) {
                    foreach($this->request->getFileMultiple('picture') as $key => $file)
                    {   
                        $imageNameDesktop = $file->getRandomName();
                        $imageExt = $file->getClientExtension();
                        $image = \Config\Services::image()
                        ->withFile($file)
                        ->save("./uploads/special/".$imageNameDesktop);

                        $insertSpecialImageDate = [
                            'special_page_id' => $specialID,
                            'link' => $_POST['link'][$key],
                            'grid_id' => $_POST['grid'][$key],
                            'image' => $imageNameDesktop,
                            'rank' => $key,
                            'created_at' => created_at(),
                        ];
                        $insertSpecialImage = $specialModels->addImage($insertSpecialImageDate);
                    }
                }
                $data['success'] = "Özel Sayfa ekleme işlemi başarılı bir şekilde yapıldı.";
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
        $seo_title = $this->request->getPost('seo_title');
        $seo_description = $this->request->getPost('seo_description');
        $slug = $this->request->getPost('slug');
        $is_active = $this->request->getPost('is_active');
        if (!$slug) {
            $slug = sef_link($title);
        }else{
            $slug = sef_link($slug);
        }
        $db = db_connect();
        $specialModels = new SpecialModels($db);
        
        $thisFind = $specialModels->c_one([
            "id" => $id
        ]);
        if(!$thisFind){
            $data['error'] = 'Düzenlemek istediğiniz sözleşme bulunamadı.';
        }elseif (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen Özel Sayfa için bir başlık giriniz.';
        }else{
            $dataInsert = [
                "title" => $title,
                "slug" => $slug,
                "seo_title" => $seo_title,
                "seo_description" => $seo_description,
                "is_active" => $is_active,
                "updated_at" => created_at()
            ];
            $insertSpecial = $specialModels->edit($id, $dataInsert);
            if ($insertSpecial) {
                $specialID =  $id;
                helper('text');
                $deleteSpecialImage = $specialModels->deleteImage(['special_page_id' => $specialID]);
                foreach($this->request->getFileMultiple('picture') as $key => $file)
                {   
                    if ($file->getName()) {
                        $imageNameDesktop = $file->getRandomName();
                        $imageExt = $file->getClientExtension();
            
                        $image = \Config\Services::image()
                        ->withFile($file)
                        ->save("./uploads/special/".$imageNameDesktop);

                        $insertSpecialImageDate = [
                            'special_page_id' => $specialID,
                            'link' => $_POST['link'][$key],
                            'grid_id' => $_POST['grid'][$key],
                            'image' => $imageNameDesktop,
                            'rank' => $key,
                            'created_at' => created_at(),
                        ];
                        $insertSpecialImage = $specialModels->addImage($insertSpecialImageDate);
                    }else{
                        $insertSpecialImageDate = [
                            'special_page_id' => $specialID,
                            'link' => $_POST['link'][$key],
                            'grid_id' => $_POST['grid'][$key],
                            'image' => $_POST['fileClone'][$key],
                            'rank' => $key,
                            'created_at' => created_at(),
                        ];
                        $insertSpecialImage = $specialModels->addImage($insertSpecialImageDate);
                    }
                   
                }
                $data['success'] = "Sözleşme Düzenleme işlemi başarılı bir şekilde yapıldı.";
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
        $specialModels = new SpecialModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $attributeCheack = $specialModels->c_one([
                "id" => $id
            ]);
            if (!$attributeCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "is_active" => $value,
                    "updated_at" => created_at(),
                ];
                $updateAttribute = $specialModels->edit($id, $dataInsert);
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
        $specialModels = new SpecialModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $attributeCheack = $specialModels->c_one([
                "id" => $id
            ]);
            if (!$attributeCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "rank" => $value,
                    "updated_at" => created_at(),
                ];
                $updateAttribute = $specialModels->edit($id, $dataInsert);
                if ($updateAttribute) {
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
        $specialModels = new SpecialModels($db);
        if (!$validation->check($id, 'required')) {
            $data['error'] =  'Silmek istediğiniz veri bulunamadı';
        }else{
            $categoryCheack = $specialModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Silmeye çaliştiğiniz yazı mevcut değil lütfen daha sonra tekrar deneyiniz..";
            }else{
                $deleteCategory = $specialModels->delete($id);
                if ($deleteCategory) {
                    $data['success'] = "Yazı başarılı bir şekilde silindi.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        return json_encode($data);
    }

    public function spacialPictureRankChange()
    {
        helper('text');
        $pictureRank = $this->request->getPost('deleteItemArea');
        $db = db_connect();
        $specialModels = new SpecialModels($db);
        foreach ($pictureRank as $key => $row) {
            $updateDate = [
                'rank' => $key
            ];
            $pictureEdit = $specialModels->pictureRankEdit($row, $updateDate);
        }
        $data['success'] = 'Resim Sıralaması başarılı bir şekilde düzenlendi.';
        return json_encode($data);
    }
}