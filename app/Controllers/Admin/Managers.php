<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModels;
use App\Models\OrderModels;
use App\Models\AddressModels;
use CodeIgniter\I18n\Time;

class Managers extends BaseController
{
    public function _construct()
    {
        
    }

    public function list()
    {
        $data['sidebarActive'] = 'managers';
        $data['sidebarAltActive'] = 'managers/list';
        $db = db_connect();
        $user = new UserModels($db);
        $data['users'] = $user->c_all(['ut.type' => 'backend']);
        $data['user'] = $user;
        return view ("admin/managers/managers-list", $data);
    }

    public function add()
    {
        $data['sidebarActive'] = 'managers';
        $data['sidebarAltActive'] = 'managers/list';
        $db = db_connect();
        $user = new UserModels($db);
        $data['users'] = $user->c_all(['ut.type' => 'backend']);
        $data['users_type'] = $user->c_all_type(['type' => 'backend']);
        return view ("admin/managers/managers-add", $data);
    }

    public function detail($id)
    {
        $data['sidebarActive'] = 'managers';
        $data['sidebarAltActive'] = 'managers/list';
        $db = db_connect();
        $user = new UserModels($db);
        $data['user'] = $user->c_one(['users.id' => $id, 'ut.type' => 'backend']);
        $data['users_type'] = $user->c_all_type(['type' => 'backend']);
        return view ("admin/managers/managers-edit", $data);
    }

    public function insert()
	{
         if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $surname = $this->request->getPost('surname');
        $full_name = $name . ' ' . $surname;
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user_type = $this->request->getPost('user_type');
        $store_id = $this->request->getPost('store_id');
      
        $db = db_connect();
        $user = new UserModels($db);
        $userEmail = $user->c_one(['users.email' => $email]);
        if (!$validation->check($name, 'required')) {
            $data['error'] =  'Lütfen adınızı giriniz.';
        }elseif (!$validation->check($surname, 'required')) {
            $data['error'] =  'Lütfen soyadınızı giriniz.';
        }elseif (!$validation->check($phone, 'required')) { 
            $data['error'] =  'Lütfen telefon numaranızı giriniz.';
        }elseif (!$validation->check($email, 'required')) { 
            $data['error'] =  'Lütfen kullanıcı için email adresinizi doldurunuz.';
        }elseif (!$validation->check($email, 'valid_email')) { 
            $data['error'] =  'Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz.';
        }elseif ($userEmail) { 
            $data['error'] =  'Eklemek istediğiniz e-posta adresi bulunmaktadır.';
        }else{
            if (!$password) {
                $password = $userEmail->password_clear;
            }
            $user_password = password_hash($password, PASSWORD_DEFAULT);
            $updateSetData = [
                'full_name' => $full_name,
                'name' => $name,
                'surname' => $surname,
                'phone' => $phone,
                'email' => $email,
                'password' => $user_password,
                'password_clear' => $password,
                'role' => $user_type,
                'store_id' => $store_id,
                'is_active' => '1',
                'updated_at' => created_at()
            ];
            $updateUser = $user->add($updateSetData);
            if ($updateUser) {
                $data['success'] =  'Yönetici düzenleme işlemi başarılı bir şekilde eklendi.';
            }else{
                $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
	}
    
    public function edit()
	{
         if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $surname = $this->request->getPost('surname');
        $full_name = $name . ' ' . $surname;
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user_type = $this->request->getPost('user_type');
        $store_id = $this->request->getPost('store_id');
      
        $db = db_connect();
        $user = new UserModels($db);
        $userEmail = $user->c_one(['users.id' => $id]);
        if (!$validation->check($name, 'required')) {
            $data['error'] =  'Lütfen adınızı giriniz.';
        }elseif (!$validation->check($surname, 'required')) {
            $data['error'] =  'Lütfen soyadınızı giriniz.';
        }elseif (!$validation->check($phone, 'required')) { 
            $data['error'] =  'Lütfen telefon numaranızı giriniz.';
        }elseif (!$validation->check($email, 'required')) { 
            $data['error'] =  'Lütfen kullanıcı için email adresinizi doldurunuz.';
        }elseif (!$validation->check($email, 'valid_email')) { 
            $data['error'] =  'Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz.';
        }elseif (!$userEmail) { 
            $data['error'] =  'Girilen email adresine ait bir kullanıcı bulunmaktadır.1231231';
        }else{
            $user_password = password_hash($password, PASSWORD_DEFAULT);
            $updateSetData = [
                'full_name' => $full_name,
                'name' => $name,
                'surname' => $surname,
                'phone' => $phone,
                'email' => $email,
                'password' => $user_password,
                'password_clear' => $password,
                'role' => $user_type,
                'store_id' => $store_id,
                'is_active' => '1',
                'updated_at' => created_at()
            ];
            $updateUser = $user->edit(['id' => $id], $updateSetData);
            if ($updateUser) {
                $data['success'] =  'Kullanıcı başarılı bir şekilde düzenlendi.';
            }else{
                $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
	}

    public function userEmailChange()
	{
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('id');
        $email = $this->request->getPost('email');
       
        $db = db_connect();
        $user = new UserModels($db);
        $updateFind = $user->c_one(['users.id' => $id]);
        $updateEmailFind = $user->c_one(['users.email' => $email, 'id !=' => $id]);
        if (!$validation->check($email, 'required')) {
            $data['error'] =  'Lütfen kullanıcı için email adresinizi doldurunuz.';
        }elseif (!$updateFind) {
            $data['error'] =  'Düzenlemek istediğiniz kullanııc bulunamadı';
        }elseif ($updateEmailFind) {
            $data['error'] =  'Düzenlemek istediğiniz eposta adresi başka kullanıcı kullanmaktadır.';
        }else{
            $updateSetData = [
                'email' => $email,
                'updated_at' => created_at()
            ];
            $updateUser = $user->edit(['id' => $id], $updateSetData);
            if ($updateUser) {
                $data['success'] =  'Düzenleme işlemi başarılı bir şekilde tamamlandı.';
                $data['email'] =  $email;
            }else{
                $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
	}

    public function userPasswordChange()
	{
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $user = new UserModels($db);

        $id = $this->request->getPost('id');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('passwordConfirm');
        $findUser = $user->c_one(['users.id' => $id]);
        if (!$validation->check($password, 'required') || !$validation->check($password, 'required')){
            $data['error'] =  'Lütfen şifrenizi giriniz.';
        }elseif (!$findUser) {
            $data['error'] =  'Şifre değiştirmek istediğiniz kullanıcı bulunamadı.';
        }elseif (!$validation->check($password, 'required')) {
            $data['error'] =  'Lütfen şifrenizi giriniz.';
        }elseif (!$validation->check($password, 'min_length[6]')) {
            $data['error'] =  'Girilen şifre 6 karakterden küçük olamaz.';
        }elseif (!$validation->check($password, 'max_length[16]')) {
            $data['error'] =  'Girilen şifre 16 karakterden büyük olamaz.';
        }elseif (!$validation->check($passwordConfirm, 'required')) {
            $data['error'] =  'Lütfen şifrenizi tekrardan giriniz.';
        }elseif ($password != $passwordConfirm) {
            $data['error'] =  'Girilen şifreler birbirleri ile uyuşmuyor';
        }else {
            $user_password = password_hash($password, PASSWORD_DEFAULT);
            $reset_code = rand(111111,999999);
            $updateSetData = [
                'reset_code' => $reset_code,
                'password' => $user_password,
            ];
            $updateUser = $user->edit(['id' => $findUser->id], $updateSetData);
            if ($updateUser) {
                $data['success'] =  'Şifreniz başarılı bir şekilde değiştirildi.';
            }else{
                $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
	}

    public function userAddressDeleteAdmin()
	{
        $validation =  \Config\Services::validation();
        $userAddressID = $this->request->getPost('value');
        $db = db_connect();
        $user = new UserModels($db);
        $addressModels = new AddressModels($db);

        $userAddressFind = $addressModels->c_one('user_address', ['id' => $userAddressID]);
        if (!$userAddressFind) {
            $data['error'] =  'Bu işlemi gerçekleştirme yetkiniz bulunmamaktadır.';
        }else{
            $addressDelete = $addressModels->deleteRow('user_address', ['id' => $userAddressID]);
            if ($addressDelete) {
                $data['success'] =  'Adres başarılı bir şekilde silindi.';
            }else{
                $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
	}

    public function getCheckoutEditAddressFormAdmin()
	{

        $validation =  \Config\Services::validation();
        $userAddressID = $this->request->getPost('id');
        $db = db_connect();
        $user = new UserModels($db);
        $addressModels = new AddressModels($db);

        $userAddressFind = $addressModels->c_one('user_address', ['id' => $userAddressID]);
        $cityAddress = $addressModels->c_all('city', ['CountryID' => '212']);
        $townAddress = $addressModels->c_all('town', ['CityID' => $userAddressFind->user_city]);
        $neighborhoodAddress = $addressModels->neighborhood_all(['d.TownID' => $userAddressFind->user_town]); 
        if (!$userAddressFind) {
            $data['error'] =  'Bu işlemi gerçekleştirme yetkiniz bulunmamaktadır.';
        }else{
            $data['getAddress'] = $userAddressFind;
            $data['city'] = $cityAddress;
            $data['town'] = $townAddress;
            $data['neighborhood'] = $neighborhoodAddress;
        }
        return json_encode($data);
	}

    public function userEditAddress()
	{
        if (isset($this->data['user_id'])) {
            $user_id = $this->data['user_id'];
        }
        $validation =  \Config\Services::validation();
        $userAddressID = $this->request->getPost('userAddressID');
        $title = $this->request->getPost('title');
        $receiver_name = $this->request->getPost('receiver_name');
        $user_city = $this->request->getPost('user_city');
        $user_town = $this->request->getPost('user_town');
        $user_neighborhood = $this->request->getPost('user_neighborhood');
        $post_code = $this->request->getPost('post_code');
        $address = $this->request->getPost('address');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $address_default = $this->request->getPost('address_default');
        $db = db_connect();
        $user = new UserModels($db);
        $addressModels = new AddressModels($db);

        $userAddressFind = $addressModels->c_one('user_address', ['id' => $userAddressID]);
        $userFind = $user->c_one(['users.id' => $user_id]);
        
        $cityCheack = $addressModels->c_one('city', ['CountryID' => '212', 'CityID' => $user_city]);
		$townCheack = $addressModels->c_one('town', ['TownID' => $user_town]);
		$neighborhoodCheack = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $user_neighborhood]);

        if (!$userAddressFind) {
            $data['error'] =  'Bu işlemi gerçekleştirme yetkiniz bulunmamaktadır.';
        }elseif (!$validation->check($title, 'required')) {
            $data['error'] =  'Lütfen adres tanımı giriniz.';
        }elseif (!$validation->check($receiver_name, 'required')) {
            $data['error'] =  'Lütfen Ad, Soyad / Firma giriniz.';
        }elseif (!$validation->check($user_city, 'required')) { 
            $data['error'] =  'Lütfen ilinizi seçin.';
        }elseif (!$validation->check($user_town, 'required') ) {
            $data['error'] =  'Lütfen ilçenizi seçin.';
        }elseif (!$validation->check($user_neighborhood, 'required') ) {
            $data['error'] =  'Lütfen mahallenizi seçin.';
        }elseif (!$validation->check($address, 'required') ) {
            $data['error'] =  'Lütfen adres giriniz.';
        }elseif (!$validation->check($phone, 'required') ) {
            $data['error'] =  'Lütfen telefon numaranızı giriniz.';
        }elseif (!$validation->check($email, 'required') ) {
            $data['error'] =  'Lütfen email adresinizi doldurunuz.';
        }elseif (!$validation->check($email, 'valid_email')) {
            $data['error'] =  'Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz.';
        }elseif (!$cityCheack) {
            $data['error'] =  'Lütfen geçerli bir il seçiniz..';
        }elseif (!$townCheack) {
            $data['error'] =  'Lütfen geçerli bir ilçe seçiniz..';
        }elseif (!$neighborhoodCheack) {
            $data['error'] =  'Lütfen geçerli bir mahalle seçiniz..';
        }else{
            $updateSetData = [
                'title' => $title,
                'receiver_name' => $receiver_name,
                'user_city' => $user_city,
                'user_town' => $user_town,
                'user_neighborhood' => $user_neighborhood,
                'post_code' => $post_code,
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
                'address_default' => $address_default,
                'updated_at' => created_at()
            ];
            $addressUpdate = $addressModels->edit('user_address', ['id' => $userAddressID], $updateSetData);
            $userAddressID = $userAddressID;
            if ($addressUpdate) {
                if ($address_default == '1') {
                    $updateSetData = [
                        'address_default' => 0,
                        'updated_at' => created_at()
                    ];
                    $updateUserAddress = $addressModels->edit('user_address', ['user_id' => $user_id, 'id !=' => $userAddressID ], $updateSetData);
                }
                $data['success'] =  'Adres başarılı bir şekilde düzenlendi.';
            }else{
                $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
	}

    public function userStatus()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('id');
        $value = $this->request->getPost('value');
        $db = db_connect();
        $userModels = new UserModels($db);
        if (!$validation->check($id, 'required') || !$validation->check($value, 'required') ) {
            $data['error'] =  'Şeçili bir değer bulunamadı.';
        }else{
            $attributeCheack = $userModels->c_one([
                "id" => $id
            ]);
            if (!$attributeCheack) {
                $data['error'] = "Durum değişikliği yapmak istediğiniz veri bulunamadı.";
            }else{
                $dataInsert = [
                    "is_active" => $value,
                    "updated_at" => created_at(),
                ];
                $updateAttribute = $userModels->edit(['id' => $id], $dataInsert);
                if ($updateAttribute) {
                    $data['success'] = "Durum değiştirme işlemi başarılı bir şekilde yapıldı.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        return json_encode($data);
    }

    public function userDelete()
    {
        $validation =  \Config\Services::validation();
        $id = $this->request->getPost('value');
        $db = db_connect();
        $userModels = new UserModels($db);
        if (!$validation->check($id, 'required')) {
            $data['error'] =  'Silmek istediğiniz veri bulunamadı';
        }else{
            $categoryCheack = $userModels->c_one([
                "id" => $id
            ]);
            if (!$categoryCheack) {
                $data['error'] = "Silmeye çaliştiğiniz kullanıcı mevcut değil lütfen daha sonra tekrar deneyiniz..";
            }else{
                $deleteCategory = $userModels->delete($id);
                if ($deleteCategory) {
                    $data['success'] = "Kullanıcı başarılı bir şekilde silindi.";
                }else{
                    $data['error'] = "Beklenmeyen bir hata oluştu.";
                }
            }
        }
        return json_encode($data);
    }
    
}