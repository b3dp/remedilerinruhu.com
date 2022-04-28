<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\fixedFieldsModels;

    class fixedFields extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function index()
        {
            $data['sidebarActive'] = 'setting';
            $data['sidebarAltActive'] = 'fixedFields/list';
            $db = db_connect();
            $fixedFieldsModels = new fixedFieldsModels($db);
            $data['fixed_fields'] = $fixedFieldsModels->c_all();
            return view ("admin/fixedFields/fixedFields-list", $data);
        }

        public function edit($id)
        {
            $data['sidebarActive'] = 'setting';
            $data['sidebarAltActive'] = 'fixedFields/list';
            $db = db_connect(); 
            $fixedFieldsModels = new fixedFieldsModels($db);
            $data['fixed_fields'] = $fixedFieldsModels->c_one(['id' => $id]);
            return view ("admin/fixedFields/fixedFields-edit", $data);
        }
        

        public function update()
        {
            $validation =  \Config\Services::validation();
            $id = $this->request->getPost('id');
            $title = $this->request->getPost('title');
            $content = $this->request->getPost('content');
            $icon = $this->request->getPost('icon');
            $db = db_connect();
            $fixedFieldsModels = new fixedFieldsModels($db);

            $aboutFind = $fixedFieldsModels->c_one([
                "id =" => $id
            ]);
            
            if (!$validation->check($title, 'required')) {
                $data['error'] =  'Lütfen İceriğin başlığını giriniz.';
            }elseif (!$validation->check($content, 'required')) {
                $data['error'] =  'Lütfen icerik giriniz.';
            }else{

                $dataInsert = [
                    "title" => $title,
                    "content" => $content,
                    "icon" => $icon,
                    "updated_at" => created_at(),
                ];
                $insertData = $fixedFieldsModels->edit(['id' => $id], $dataInsert);
                if ($insertData) {
                    $data['success'] = "Sabit Alan başarılı bir şekilde düzenlendi.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
            return json_encode($data);
        }

    }
