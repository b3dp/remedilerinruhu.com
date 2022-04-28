<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SliderModels;

class Slider extends BaseController
{
    public function _construct()
    {
        
    }
    public function add()
    {
        $data['sidebarActive'] = 'slider';
        $data['sidebarAltActive'] = 'slider/list';
        $data = [
            'slider_mobile_w' => getenv('picture.slider_mobile_w'),
            'slider_mobile_h' => getenv('picture.slider_mobile_h'),
            'slider_desktop_w' => getenv('picture.slider_desktop_w'),
            'slider_desktop_h' => getenv('picture.slider_desktop_h'),
        ];
        return view ("admin/slider/slider-add", $data);
    }
    public function edit($id)
    {
        $data['sidebarActive'] = 'slider';
        $data['sidebarAltActive'] = 'slider/list';
        $db = db_connect();
        $sliderModels = new SliderModels($db);
        $data = [
            'slider_mobile_w' => getenv('picture.slider_mobile_w'),
            'slider_mobile_h' => getenv('picture.slider_mobile_h'),
            'slider_desktop_w' => getenv('picture.slider_desktop_w'),
            'slider_desktop_h' => getenv('picture.slider_desktop_h'),
        ];
        $data['slider'] = $sliderModels->c_one(['id' => $id]);
        
        return view ("admin/slider/slider-edit", $data);
    }
    public function list()
    {
        $data['sidebarActive'] = 'slider';
        $data['sidebarAltActive'] = 'slider/list';
        $db = db_connect();
        $sliderModels = new SliderModels($db);
        $data['sliders'] = $sliderModels->c_all();
        return view ("admin/slider/slider-list", $data);
    }

    public function insert()
    {
        $validation =  \Config\Services::validation();
        $title = $this->request->getPost('title');
        $link = $this->request->getPost('link');
        $is_active = $this->request->getPost('is_active');
        if ($this->validate(['pictureDesktop' => 'uploaded[pictureDesktop]'])) {
            $pictureDesktop = $this->request->getFile('pictureDesktop');
        }
        if ($this->validate(['pictureMobile' => 'uploaded[pictureMobile]'])) {
            $pictureMobile = $this->request->getFile('pictureMobile');
        }
        $db = db_connect();
        $sliderModels = new SliderModels($db);
        if (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen slider için bir başlık giriniz.';
        }elseif (!$validation->check($pictureDesktop, 'required')) {
            $data['error'] =  'Lütfen slider için masaüstü resmi seçiniz.';
        }elseif (!$validation->check($pictureMobile, 'required')) {
            $data['error'] =  'Lütfen slider için mobile resmi seçiniz.';
        }else{
            helper('text');
            if ($pictureDesktop) {
                $imageNameDesktop = $pictureDesktop->getRandomName();
                $imageExt = $pictureDesktop->getClientExtension();
    
                $image = \Config\Services::image()
                ->withFile($this->request->getFile('pictureDesktop'))
                ->fit(getenv('picture.slider_desktop_w'), getenv('picture.slider_desktop_h'), 'center')
                ->convert(IMAGETYPE_WEBP)
                ->save("./uploads/sliders/".$imageNameDesktop);
            }

            if ($pictureMobile) {
                $imageNameMobile = $pictureMobile->getRandomName();
                $imageExt = $pictureMobile->getClientExtension();
    
                $image = \Config\Services::image()
                ->withFile($this->request->getFile('pictureMobile'))
                ->fit(getenv('picture.slider_mobile_w'), getenv('picture.slider_mobile_h'), 'center')
                ->convert(IMAGETYPE_WEBP)
                ->save("./uploads/sliders/mobile/".$imageNameMobile);
            }

            $dataInsert = [
                "title" => $title,
                "link" => $link,
                "pictureDesktop" => $imageNameDesktop,
                "pictureMobile" => $imageNameMobile,
                "is_active" => $is_active,
                "created_at" => created_at(),
            ];
            $insertData = $sliderModels->add($dataInsert);
            if ($insertData) {
                $data['success'] = "Slider ekleme işlemi başarılı bir şekilde yapıldı.";
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
        $link = $this->request->getPost('link');
        $is_active = $this->request->getPost('is_active');

        if ($this->validate(['pictureDesktop' => 'uploaded[pictureDesktop]'])) {
            $pictureDesktop = $this->request->getFile('pictureDesktop');
        }
        if ($this->validate(['pictureMobile' => 'uploaded[pictureMobile]'])) {
            $pictureMobile = $this->request->getFile('pictureMobile');
        }
        $db = db_connect();
        $sliderModels = new SliderModels($db);
        $slider = $sliderModels->c_one(['id' => $id]);
        if (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen slider için bir başlık giriniz.';
        }else{
            
            helper('text');
            if ($pictureDesktop) {
                $imageNameDesktop = $pictureDesktop->getRandomName();
                $imageExt = $pictureDesktop->getClientExtension();
    
                $image = \Config\Services::image()
                ->withFile($this->request->getFile('pictureDesktop'))
                ->fit(getenv('picture.slider_desktop_w'), getenv('picture.slider_desktop_h'), 'center')
                ->convert(IMAGETYPE_WEBP)
                ->save("./uploads/sliders/".$imageNameDesktop);
            }else{
                $imageNameDesktop = $slider->pictureDesktop;
            }
            if ($pictureMobile) {
                $imageNameMobile = $pictureMobile->getRandomName();
                $imageExt = $pictureMobile->getClientExtension();
    
                $image = \Config\Services::image()
                ->withFile($this->request->getFile('pictureMobile'))
                ->fit(getenv('picture.slider_mobile_w'), getenv('picture.slider_mobile_h'), 'center')
                ->convert(IMAGETYPE_WEBP)
                ->save("./uploads/sliders/mobile/".$imageNameMobile);
            }else{
                $imageNameMobile = $slider->pictureMobile;
            }

            $dataInsert = [
                "title" => $title,
                "link" => $link,
                "pictureDesktop" => $imageNameDesktop,
                "pictureMobile" => $imageNameMobile,
                "is_active" => $is_active,
                "updated_at" => created_at(),
            ];
            $insertData = $sliderModels->edit($id, $dataInsert);
            if ($insertData) {
                $data['success'] = "Slider düzenleme işlemi başarılı bir şekilde yapıldı.";
            }else{
                $data['error'] = "Beklenmeyen bir hata oluştu.";
            }
        }
        return json_encode($data);
    }

    public function delete()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('value');
        $db = db_connect();
        $sliderModels = new SliderModels($db);
        if (!$validation->check($id, 'required')) {
            $data['error'] =  'Silmek istediğiniz veri bulunamadı';
        }else{
            $categoryCheack = $sliderModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Silmeye çaliştiğiniz slider mevcut değil lütfen daha sonra tekrar deneyiniz..";
            }else{
                $deleteCategory = $sliderModels->delete($id);
                if ($deleteCategory) {
                    $data['success'] = "Slider başarılı bir şekilde silindi.";
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
        $sliderModels = new SliderModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $categoryCheack = $sliderModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "is_active" => $value,
                    "updated_at" => created_at(),
                ];
                $updateCategory = $sliderModels->edit($id, $dataInsert);
                if ($updateCategory) {
                    $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        return json_encode($data);
    }

    public function desktopDeleteImg()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('value');
        $db = db_connect();
        $sliderModels = new SliderModels($db);
        if (!$validation->check($id, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $categoryCheack = $sliderModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Resim silmek istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "pictureDesktop" => '',
                    "updated_at" => created_at(),
                ];
                $updateCategory = $sliderModels->edit($id, $dataInsert);
                if ($updateCategory) {
                    $data['success'] = "Resim silme işlemi başarılı bir şekilde yapıldı.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        return json_encode($data);
    }

    public function mobileDeleteImg()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('value');
        $db = db_connect();
        $sliderModels = new SliderModels($db);
        if (!$validation->check($id, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $categoryCheack = $sliderModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Resim silmek istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "pictureMobile" => '',
                    "updated_at" => created_at(),
                ];
                $updateCategory = $sliderModels->edit($id, $dataInsert);
                if ($updateCategory) {
                    $data['success'] = "Resim silme işlemi başarılı bir şekilde yapıldı.";
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
        $sliderModels = new SliderModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $attributeCheack = $sliderModels->c_one([
                "id" => $id
            ]);
            if (!$attributeCheack) {
                $data['error'] = "Sira değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "rank" => $value,
                    "updated_at" => created_at(),
                ];
                $updateAttribute = $sliderModels->edit($id, $dataInsert);
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