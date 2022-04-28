<?php 

    namespace App\Controllers\Admin;
    
    use App\Controllers\BaseController;
    use App\Models\UserModels;

    class Login extends BaseController
    { 
        public function _construct()
        {

        }

        public function index()
        {
            $data["title"] = "Giriş Yap";
            return view ("admin/login", $data);
        }

        public function LoginCheack()
        {
            $validation =  \Config\Services::validation();
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $recaptcha = $this->request->getPost('g-recaptcha-response');
            $db = db_connect();
            $user = new UserModels($db);
            $userCheack = $user->c_one([
                "users.email" => $email,
                "users.role !=" => '1',
            ]);
            if (!$validation->check($email, 'required')) {
                $data['error'] =  'Lütfen email adresinizi doldurunuz.';
            }elseif (!$validation->check($email, 'valid_email')) {
                $data['error'] =  'Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz.';
            }elseif (!$validation->check($password, 'required')) {
                $data['error'] =  'Lütfen şifrenizi giriniz.';
            }elseif (!password_verify($password, $userCheack->password)) {
                $data['error'] =  'Girilen şifre eksik yada hatalıdır.';
            }else{
                if ($userCheack) {
                    $data['success'] = "Giriş başarılı.";
                    $userArray = [
                        "id" => $userCheack->id,
                        "fullname" => $userCheack->full_name,
                        "name" => $userCheack->name,
                        "surname" => $userCheack->surname,
                        "email" => $userCheack->email,
                        "role" => $userCheack->role,
                        "store_id" => $userCheack->store_id,
                        'logged_in' => TRUE
                    ];
                    $session = session();
                    $session->set('admin', $userArray);
                }else{
                    $data['error'] = "Girilen bilgilere ait bir hesap bulunamadı.";
                }
            }
            return json_encode($data);
        }

        public function logout()
        {
            $session =  session();
            $session->removeTempdata("admin"); // destroy session variables and values
            return redirect()->to("panel/login");
        }

    }
