<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\OrderModels;

    class cargoSetting extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index()
        {
            $data['sidebarAltActive'] = 'cargoSetting/list';
            $data['sidebarActive'] = 'setting';
            $db = db_connect();
            $orderModels = new OrderModels($db);
            $data['delivery_options'] = $orderModels->delivery_c_all();
            return view ("admin/cargoSetting/cargoSetting-list", $data);
        }

        public function add()
        {
            $data['sidebarAltActive'] = 'cargoSetting/list';
            $data['sidebarActive'] = 'setting';
            $db = db_connect();
            return view ("admin/cargoSetting/cargoSetting-add", $data);
        }

        public function edit($id)
        {
            $data['sidebarAltActive'] = 'cargoSetting/list';
            $data['sidebarActive'] = 'setting';
            $db = db_connect(); 
            $orderModels = new OrderModels($db);
            $data['delivery_options'] = $orderModels->delivery_c_one(['id' => $id]);
            return view ("admin/cargoSetting/cargoSetting-edit", $data);
        }
        

        public function update()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $title = $this->request->getPost('title');
            $shipping_time = $this->request->getPost('shipping_time');
            $free_shipping_price = $this->request->getPost('free_shipping_price');
            $shipping_price = $this->request->getPost('shipping_price');
            $db = db_connect();
            $orderModels = new OrderModels($db);

            $aboutFind = $orderModels->delivery_c_one([
                "id =" => $id
            ]);
            
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen kargo için bir başlık giriniz.';
            }elseif (!$validation->check($shipping_time, 'required')) {
                $data['error'] =  'Lütfen tahmini teslimat süresini giriniz.';
            }elseif (!$validation->check($free_shipping_price, 'required')) {
                $data['error'] =  'Lütfen ücretsiz kargo için gerekli alişveriş tutarını giriniz.';
            }elseif (!$validation->check($shipping_price, 'required')) {
                $data['error'] =  'Lütfen kargo ücretini giriniz.';
            }elseif (!$aboutFind) {
                $data['error'] =  'Düzenlemek istediğiniz kargo ayarları bulunamadı.';
            }else{

                $dataInsert = [
                    "title" => $title,
                    "shipping_time" => $shipping_time,
                    "free_shipping_price" => $free_shipping_price,
                    "shipping_price" => $shipping_price,
                    "updated_at" => created_at(),
                ];
                $insertData = $orderModels->delivery_edit(['id' => $id], $dataInsert);
                if ($insertData) {
                    $data['success'] = "Kargo başarılı bir şekilde düzenlendi.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
            return json_encode($data);
        }

    }
