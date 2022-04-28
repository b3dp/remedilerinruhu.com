<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContractsModels;

class Contracts extends BaseController
{
    public function _construct()
    {
        
    }

    public function add()
    {
        $data['sidebarActive'] = 'contracts';
        $data['sidebarAltActive'] = 'contracts/list';
        return view ("admin/contract/contract-add", $data);
    }
    
    public function edit($id)
    { 
        $data['sidebarActive'] = 'contracts';
        $data['sidebarAltActive'] = 'contracts/list';
        $db = db_connect();
        $contractsModels = new ContractsModels($db);
        $data['contract'] = $contractsModels->c_one(['id' => $id]);
        if (!$data['contract']) {
            return redirect()->route('contracts/list');
        }
        return view ("admin/contract/contract-edit", $data);
    }

    public function list()
    {
        $data['sidebarActive'] = 'contracts';
        $data['sidebarAltActive'] = 'contracts/list';
        $db = db_connect();
        $contractsModels = new ContractsModels($db);

        $data['contracts'] = $contractsModels->c_all();
        return view ("admin/contract/contract-list", $data);
    }

    public function insert()
    {
        $validation =  \Config\Services::validation();
        $title = $this->request->getPost('title');
        $short_description = $this->request->getPost('short_description');
        $description = $this->request->getPost('description');
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
        $contractsModels = new ContractsModels($db);

        if (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen sözleşme için bir başlık giriniz.';
        }elseif (!$validation->check($description, 'required')) {
            $data['error'] =  'Lütfen bir açıklama yazısı yazınız.';
        }else{
            $attributeCheack = $contractsModels->c_one([
                "title " => $title
            ]);
            if ($attributeCheack) {
                $data['error'] = "Girmek istediğiniz sözleşme mevcut lütfen kontrol ederek tekrar deneyiniz.";
            }else{
                $dataInsert = [
                    "title" => $title,
                    "slug" => $slug,
                    "short_description" => $short_description,
                    "description" => $description,
                    "seo_title" => $seo_title,
                    "seo_description" => $seo_description,
                    "is_active" => $is_active,
                    "rank" => '1',
                    "created_at" => created_at()
                ];
                $insertContract = $contractsModels->add($dataInsert);
                if ($insertContract) {
                    $data['success'] = "Sözleşme ekleme işlemi başarılı bir şekilde yapıldı.";
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
        $short_description = $this->request->getPost('short_description');
        $description = $this->request->getPost('description');
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
        $contractsModels = new ContractsModels($db);
        
        $thisFind = $contractsModels->c_one([
            "id" => $id
        ]);

        if(!$thisFind){
            $data['error'] = 'Düzenlemek istediğiniz sözleşme bulunamadı.';
        }elseif (!$validation->check($title, 'required')) {
            $data['error'] = 'Lütfen sözleşme için bir başlık giriniz.';
        }elseif (!$validation->check($description, 'required')) {
            $data['error'] = 'Lütfen bir açıklama yazısı yazınız.';
        }else{
            $dataInsert = [
                "title" => $title,
                "slug" => $slug,
                "short_description" => $short_description,
                "description" => $description,
                "seo_title" => $seo_title,
                "seo_description" => $seo_description,
                "is_active" => $is_active,
                "updated_at" => created_at()
            ];
            $insertContract = $contractsModels->edit($id, $dataInsert);
            if ($insertContract) {
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
        $contractsModels = new ContractsModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $attributeCheack = $contractsModels->c_one([
                "id" => $id
            ]);
            if (!$attributeCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "is_active" => $value,
                    "updated_at" => created_at(),
                ];
                $updateAttribute = $contractsModels->edit($id, $dataInsert);
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
        $contractsModels = new ContractsModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $attributeCheack = $contractsModels->c_one([
                "id" => $id
            ]);
            if (!$attributeCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "rank" => $value,
                    "updated_at" => created_at(),
                ];
                $updateAttribute = $contractsModels->edit($id, $dataInsert);
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
        $contractsModels = new ContractsModels($db);
        if (!$validation->check($id, 'required')) {
            $data['error'] =  'Silmek istediğiniz veri bulunamadı';
        }else{
            $categoryCheack = $contractsModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Silmeye çaliştiğiniz yazı mevcut değil lütfen daha sonra tekrar deneyiniz..";
            }else{
                $deleteCategory = $contractsModels->delete($id);
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