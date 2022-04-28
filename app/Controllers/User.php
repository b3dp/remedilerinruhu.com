<?php

namespace App\Controllers;

use App\Libraries\LoadView;
use App\Controllers\Basket;
use CodeIgniter\I18n\Time;
use App\Models\UserModels;
use App\Models\ContractsModels;
use App\Models\SettingModels;
use App\Models\BasketModels;

class User extends LoadView
{
	public function loginView()
	{
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $arr = parse_url($url);
        $filter = $arr['query'];
        $return = ltrim(strstr($filter, '='), '=') ;
        $data = $this->data;
        $db = db_connect();
        if ($_GET['info']) {
            $data['info'] = $_GET['info'];
            $return = '';
        }
        $contractsModels = new ContractsModels($db);
        $settingModels = new SettingModels($db);
        $data['aydinlatmaMetni'] = $contractsModels->c_one(['id' => 3]);
        $data['uyelikSozlesmesi'] = $contractsModels->c_one(['id' => 4]);
        $data['rizaMetni'] = $contractsModels->c_one(['id' => 5]);
        $data['iletisim_onayi'] = $contractsModels->c_one(['id' => 8]);
        $contactSetting = $settingModels->c_all(['type' => 'contact']);
        $data['contact'] = namedSettings($contactSetting);
        $data['retun'] = $return;
		for ($i = 1 ; $i <= 31 ; $i++ ) {
            $data['days'][] = $i;
        }
        
        $data['month'] = monthArray();
        $date = new Time();
        $year = $date->getYear(); 
        for ($i = $year ; $i >= $year - 100 ; $i-- ) {
            $data['years'][] = $i;
        }
        $this->viewLoad('login', $data);
	}
    
    public function registerView()
	{
        $data = $this->data;
        $db = db_connect();
        $contractsModels = new ContractsModels($db);
        $settingModels = new SettingModels($db);
        $userModels = new UserModels($db);
        $data['aydinlatmaMetni'] = $contractsModels->c_one(['id' => 3]);
        $data['uyelikSozlesmesi'] = $contractsModels->c_one(['id' => 4]);
        $data['rizaMetni'] = $contractsModels->c_one(['id' => 5]);
        $data['iletisim_onayi'] = $contractsModels->c_one(['id' => 8]);
        $contactSetting = $settingModels->c_all(['type' => 'contact']);
        $data['contact'] = namedSettings($contactSetting);
        $data['user_type'] = $userModels->c_all_type(['type' => 'frontend']);
        for ($i = 1 ; $i <= 31 ; $i++ ) {
            $data['days'][] = $i;
        }
        $data['month'] = monthArray();
        $date = new Time();
        $year = $date->getYear(); 
        for ($i = $year ; $i >= $year - 100 ; $i-- ) {
            $data['years'][] = $i;
        }
        $this->viewLoad('register', $data);
	}

    public function register()
	{
        $validation =  \Config\Services::validation();
        $name = $this->request->getPost('name');
        $surname = $this->request->getPost('surname');
        $full_name = $name . ' ' . $surname;
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('passwordConfirm');
        $phone = $this->request->getPost('phone');
        $gender = $this->request->getPost('gender');
        $days = $this->request->getPost('days');
        $month = $this->request->getPost('month');
        $year = $this->request->getPost('year');
        $contract = $this->request->getPost('contrat');
        $electronic_message = $this->request->getPost('newsletter');
        $retun = $this->request->getPost('retun');
        if ($days < 10) {
            $days = '0'.$days;
        }
        $birthday = $year . '-' . $month . '-' . $days;
        $date = new Time('-11 year');
        $thisYear = $date->getYear(); 
        $db = db_connect();
        $user = new UserModels($db);
        $settingModels = new SettingModels($db);
        if (!$validation->check($name, 'required')) {
            $data['error'] =  'Lütfen adınızı giriniz.';
        }elseif (!$validation->check($surname, 'required')) {
            $data['error'] =  'Lütfen soyadınızı giriniz.';
        }elseif (!$validation->check($email, 'required')) {
            $data['error'] =  'Lütfen email adresinizi giriniz.';
        }elseif (!$validation->check($email, 'valid_email')) {
            $data['error'] =  'Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz.';
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
        }elseif (!$validation->check($passwordConfirm, 'required')) {
            $data['error'] =  'Lütfen şifrenizi tekrardan giriniz.';
        }elseif (!$validation->check($phone, 'required')) {
            $data['error'] =  'Lütfen telefon numaranızı giriniz.';
        }elseif (!$validation->check($days, 'required') || !$validation->check($month, 'required') || !$validation->check($year, 'required')) {
            $data['error'] =  'Lüften doğum tarihinizi giriniz.';
        }elseif ($birthday > $thisYear) {
            $data['error'] =  'Üye olabilmeniz için 12 yaşından büyük olmanız gerekmektedir.';
        }else{
            $user_password = password_hash($password, PASSWORD_DEFAULT);
            $verify_code = rand(111111,999999);
            $emailCheack = $user->c_one(['email' => $email, 'is_guest !=' => '1']);
            if ($emailCheack) {
                $data['error'] =  'Bu e-posta adresi ile bir müşteri zaten kayıtlı.';
            }else{
                $userInsertDate = [
                    'full_name' => $full_name,
                    'name' => $name,
                    'surname' => $surname,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $user_password,
                    'gender' => $gender,
                    'birthday' => $birthday,
                    'role' => '1',
                    'verify_code' => $verify_code,
                    'is_active' => '1',
                    'is_verified' => '0',
                    'contract' => $contract,
                    'electronic_message' => $electronic_message,
                    'created_at' => created_at()
                ];
                $userInsert = $user->add($userInsertDate);
                if ($userInsert) {
                    $userID = $db->insertID();
                    $sendMail = new SendMail();
                    if ($retun) {
                        $url = base_url('aktivasyon/'. $email .'/'. $verify_code .'?return=siparis');
                    }else{
                        $url = base_url('aktivasyon/'. $email .'/'. $verify_code .'');
                    }
                    
                    $contactSetting = $settingModels->c_all(['type' => 'contact']);
                    $contact = namedSettings($contactSetting);

                    $mailContent = '
                                <table class="body"
                                style="border-collapse: collapse; border-spacing: 0; height: 100% !important; width: 100% !important">
                                <tr>
                                    <td
                                        style="">
                                        <table class="header row"
                                            style="border-collapse: collapse; border-spacing: 0; margin: 40px 0 20px; width: 100%">
                                            <tr>
                                                <td class="header__cell"
                                                    style="">
                                                    <center>
                                                        <table class="container"
                                                            style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                            <tr>
                                                                <td
                                                                    style="">
                                                                    <table class="row"
                                                                        style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                        <tr>
                                                                            <td class="shop-name__cell"
                                                                                style="">
                                                                                <img src="'. base_url('public/frontend/assets/img/bilt/bilt_logo.png').'" width="180">
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </center>
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="row content" style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                            <tr>
                                                <td class="content__cell"
                                                    style="padding-bottom: 40px; ">
                                                    <center>
                                                        <table class="container"
                                                            style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                            <tr>
                                                                <td
                                                                    style="">
                                                                    <h2 style="font-size: 24px; font-weight: normal; margin: 0 0 10px">Biltstore Üyelik Aktivasyon Maili</h2>

                                                                    <p style="color: #777; font-size: 16px; line-height: 150%; margin: 0">Üyeliğinizi aktive edebilmek için lütfen Aktivasyon Yap butonuna basınız.</p>
                                                                    <table class="row actions"
                                                                        style="border-collapse: collapse; border-spacing: 0; margin-top: 20px; width: 100%">
                                                                        <tr>
                                                                            <td class="actions__cell"
                                                                                style="">
                                                                                <table class="button main-action-cell"
                                                                                    style="border-collapse: collapse; border-spacing: 0; float: left; margin-right: 15px">
                                                                                    <tr>
                                                                                        <td class="button__cell"
                                                                                            style="background: #1f1f1f; border-radius: 4px; padding: 20px 25px; text-align: center; "
                                                                                            align="center" bgcolor="#1f1f1f">
                                                                                            <a href="'. $url .'" class="button__text"
                                                                                                style="color: #fff; font-size: 16px; text-decoration: none">Aktivasyon Yap</a>
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </center>
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="row footer"
                                            style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                            <tr>
                                                <td class="footer__cell"
                                                    style=" padding: 35px 0; ">
                                                    <center>
                                                        <table class="container"
                                                            style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                            <tr>
                                                                <td
                                                                    style="">
                                                                    <p class="disclaimer__subtext"
                                                                        style="color: #999; font-size: 14px; line-height: 150%; margin: 0">Herhangi bir sorunuz varsa, bu adresten bize ulaşın:
                                                                        <a href="mailto:'. $contact['biltstore_info_email_general']->value .'"
                                                                            style="color: #1f1f1f; font-size: 14px; text-decoration: none">'. $contact['biltstore_info_email_general']->value .'</a>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </center>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </body>
                        </html>
                        </body>
                        </html>
                    ';
                    $registerMail = $sendMail->SendMail($email, "", $contact['biltstore_info_email_general']->value, "Üyelik Aktivasyon Maili", $mailContent);
                    if ($registerMail) {
                        $data['success'] =  'Kayıt işlemi başarılı bir şekilde tamamlandı.';
                        getLogDate($userID, '3', $userID, 'Kullanıcı ID', '');
                        unset($_SESSION['user']);
                    }else{
                        $data['error'] =  'Üyelik aktivasyon maili yollanamadı lütfen daha sonra tekrardan deneyiniz.';
                        getLogDate($userID, '2', $userID, 'Kullanıcı ID', '');
                    }
                }else{
                    $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
                    getLogDate($userID, '2', $userID, 'Kullanıcı ID', '');
                }
            }
        }
        return json_encode($data);
	}

    public function login()
	{
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $basket = new Basket($db);
        $user = new UserModels($db);
        $basketModels = new BasketModels($db);
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $recaptcha = $this->request->getPost('g-recaptcha-response');
        $remember = $this->request->getPost('remember');
        $findUser = $user->c_one(['email' => $email]);
        $data['activationArea'] = TRUE;
        if (!$validation->check($email, 'required')) {
            $data['error'] =  'Lütfen email adresinizi giriniz.';
        }elseif (!$validation->check($email, 'valid_email')) {
            $data['error'] =  'Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz.';
        }elseif (!$validation->check($password, 'required')) {
            $data['error'] =  'Lütfen şifrenizi giriniz.';
        }elseif (!$validation->check($recaptcha, 'required')) {
            $data['error'] =  'Lütfen robot olmadığınızı doğrulayınız.';
        }elseif (!$findUser) {
            $data['error'] =  'Girilen email adresine ait bir hesap bulunamadı.';
            getLogDate('', '21', '', '', '');
        }elseif (!password_verify($password, $findUser->password)) {
            $data['error'] =  'Girilen şifre eksik yada hatalıdır.';
        }elseif ($findUser->is_verified == 0 ) {
            $data['error'] =  'Lütfen aktivasyon işleminiz için mail adresinizi kontrol ediniz.';
            getLogDate($findUser->id, '22', $findUser->id, 'Kullanıcı ID', '');
        }else{
            if ($remember == '1') {
                $sifrele = sha1(base64_encode(md5(base64_encode($simdikiZaman))));
                $token = substr($sifrele, 1, 16);
                $loginArray = array(
                    "email" => $email,
                    "g_s" => $token
                );
                $login = json_encode($loginArray);
                setcookie("__remember", $login, time() + (10 * 365 * 24 * 60 * 60));
            }

            $sessionUser = [
                "id" => $findUser->id,
                'name' => $findUser->name,
                'surname' => $findUser->surname,
                'fullName' => $findUser->name . ' ' .$findUser->surname,
                'email' => $findUser->email,
                'role' => $findUser->role,
                'logged_in' => TRUE
            ];
            $session = session();
            $session->set('user', $sessionUser);
            $updateUserData = [
                "remember_token" => $token,
                "last_login" => created_at(),
                "updated_at" => created_at(),
            ];
            $updateUser = $user->edit(['id' => $findUser->id], $updateUserData);
            getLogDate($findUser->id, '5', $findUser->id, 'Üye ID', '');
            $basketSession = $basketModels->c_all(['user_id' => $findUser->id]);
            foreach ($basketSession as $row) {
                $basket->productUserLoginBasketAdd($row->color_id, $row->size_id, $row->product_id, $row->variant_id, $row->piece, $findUser->id);
            }

            foreach ($_SESSION['order']['product'] as $row) {
                $headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
                $basketTotalPrice += $row['last_price'] * $row['piece'];
    
                $basketDatebaseData = [
                    'user_id' => $findUser->id,
                    'product_id' => $row['id'],
                    'variant_id' => $row['variant_id'],
                    'color_id' => $row['color_id'],
                    'size_id' => $row['size_id'],
                    'piece' => $row['piece'],
                ];
                $basketFind = $basketModels->c_one(['user_id' => $findUser->id, 'product_id' => $row['id'], 'variant_id' => $row['variant_id']]);
                if (!$basketFind) {
                    $basketModels->add($basketDatebaseData);
                }
            }
        }
        return json_encode($data);
	}

    public function logout()
	{
        $session =  session();
        helper('cookie');
        getLogDate(session()->get('user')['id'], '18', session()->get('user')['id'], 'Kullanıcı ID', '');
        $session->removeTempdata("user"); // destroy session variables and values
        setcookie('__remember', null); 
        return redirect()->to(base_url("/"));
	}

    public function activation($email, $activationCode)
	{
        $data = $this->data;
        $db = db_connect();
        $url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $arr = parse_url($url);
        $filter = $arr['query'];
        $return = ltrim(strstr($filter, '='), '=') ;
        $user = new UserModels($db);
        $findUser = $user->c_one(['email' => $email, 'verify_code' => $activationCode]);
        $data['activationArea'] = TRUE;
        if ($findUser) {
            if (!$findUser->is_verified) {
                $updateSetData = [
                    'is_verified' => '1', 
                    'email_verified_at' => created_at(), 
                    'updated_at' => created_at() 
                ];
                $updateUser = $user->edit(['email' => $email, 'verify_code' => $activationCode], $updateSetData);
                if ($updateUser) {
                    /*
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => base_url().'/api/nebimUserInsert/'.$findUser->id.'?apiKey=953E1C7C06494141B8DF4BBBDE76ED5E',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Cookie: ci_session=k0oeq5nshfc34taql9mu9n5o8usk07ip'
                        ),
                        ));
                        $response = curl_exec($curl);
                        curl_close($curl);
                    */
                    $data['message'] = "Aktivasyon işlemi başarılı bir şekilde yapıldı. Yönlendiriliyorsunuz...";
                    $data['errorType'] = "success";
                    $data['return'] = $return;
                    
                    $sessionUser = [
                        "id" => $findUser->id,
                        'name' => $findUser->name,
                        'surname' => $findUser->surname,
                        'fullName' => $findUser->name . ' ' .$findUser->surname,
                        'email' => $findUser->email,
                        'role' => $findUser->role,
                        'logged_in' => TRUE
                    ];
                    $session = session();
                    $session->set('user', $sessionUser);
                    $updateUserData = [
                        "remember_token" => $token,
                        "last_login" => created_at(),
                        "updated_at" => created_at(),
                    ];
                    $updateUser = $user->edit(['id' => $findUser->id], $updateUserData);
                    getLogDate($findUser->id, '23', $findUser->id, 'Kullanıcı ID', '');
                    header("refresh:3; url=". base_url($return) ."");
                }else{
                    $data['message'] = "Aktivasyon işlemi gerçekleştirilemedi lütfen daha sonra tekrar deneyiniz.";
                    $data['errorType'] = "error";
                }
            }else{
                $data['message'] = "Bu hesapta daha önce aktivasyon işlemi yapılmıştır.";
                getLogDate($findUser->id, '24', $findUser->id, 'Kullanıcı ID', '');
                $data['errorType'] = "warning";
            }
        }else{
            $data['message'] = "Aktivasyon işlemi yapılacak bir kullanıcı bulunamadı.";
            getLogDate($findUser->id, '25', $findUser->id, 'Kullanıcı ID', '');
            $data['errorType'] = "error";
        }
        $this->viewLoad('login', $data);
	}

    public function forgotPassword()
	{
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $user = new UserModels($db);
        $settingModels = new SettingModels($db);
        $email = $this->request->getPost('email');
        $findUser = $user->c_one(['email' => $email]);
        if (!$validation->check($email, 'required')) {
            $data['error'] =  'Lütfen email adresinizi giriniz.';
        }elseif (!$validation->check($email, 'valid_email')) {
            $data['error'] =  'Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz.';
        }elseif (!$findUser) {
            $data['error'] =  'Girilen email adresine ait bir hesap bulunamadı.';
        }else{

            $contactSetting = $settingModels->c_all(['type' => 'contact']);
			$contact = namedSettings($contactSetting);

            $reset_code = rand(111111,999999);
            $updateSetData = [
                'reset_code' => $reset_code
            ];
            $updateUser = $user->edit(['email' => $email], $updateSetData);
            if ($updateUser) {
                $sendMail = new SendMail();
                $mailContent = '
                        <table class="body"
                            style="border-collapse: collapse; border-spacing: 0; height: 100% !important; width: 100% !important">
                            <tr>
                                <td
                                    style="">
                                    <table class="header row"
                                        style="border-collapse: collapse; border-spacing: 0; margin: 40px 0 20px; width: 100%">
                                        <tr>
                                            <td class="header__cell"
                                                style="">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <table class="row"
                                                                    style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                                                    <tr>
                                                                        <td class="shop-name__cell"
                                                                            style="">
                                                                            <img src="'. base_url('public/frontend/assets/img/bilt/bilt_logo.png').'" width="180">
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row content" style="border-collapse: collapse; border-spacing: 0; width: 100%">
                                        <tr>
                                            <td class="content__cell"
                                                style="padding-bottom: 40px; ">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <h2 style="font-size: 24px; font-weight: normal; margin: 0 0 10px">Biltstore Şifremi Unuttum</h2>

                                                                <p style="color: #777; font-size: 16px; line-height: 150%; margin: 0">Şifrenizi sıfırlamak için bağlantıyı kullanabilirsiniz.</p>
                                                                <table class="row actions"
                                                                    style="border-collapse: collapse; border-spacing: 0; margin-top: 20px; width: 100%">
                                                                    <tr>
                                                                        <td class="actions__cell"
                                                                            style="">
                                                                            <table class="button main-action-cell"
                                                                                style="border-collapse: collapse; border-spacing: 0; float: left; margin-right: 15px">
                                                                                <tr>
                                                                                    <td class="button__cell"
                                                                                        style="background: #1f1f1f; border-radius: 4px; padding: 20px 25px; text-align: center; "
                                                                                        align="center" bgcolor="#1f1f1f">
                                                                                        <a href="'. base_url('sifremi-sifirla/'. $email .'/'. $reset_code .'') .'" class="button__text"
                                                                                            style="color: #fff; font-size: 16px; text-decoration: none">Şifremi Sıfırla</a>
                                                                                    </td>
                                                                                </tr>
                                                                            </table>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="row footer"
                                        style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
                                        <tr>
                                            <td class="footer__cell"
                                                style=" padding: 35px 0; ">
                                                <center>
                                                    <table class="container"
                                                        style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
                                                        <tr>
                                                            <td
                                                                style="">
                                                                <p class="disclaimer__subtext"
                                                                    style="color: #999; font-size: 14px; line-height: 150%; margin: 0">Herhangi bir sorunuz varsa, bu adresten bize ulaşın:
                                                                    <a href="mailto:'. $contact['biltstore_info_email_general']->value .'"
                                                                        style="color: #1f1f1f; font-size: 14px; text-decoration: none">'. $contact['biltstore_info_email_general']->value .'</a>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </center>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                    </body>
                    </html>
                ';
                $registerMail = $sendMail->SendMail($email, "", $contact['biltstore_info_email_general']->value, "Şifremi Unuttum", $mailContent);
                if ($registerMail) {
                    $data['success'] =  'Girdiğiniz email adresine bağlantı linki gönderilmiştir. Linke giderek şifrenizi sıfırlaya bilirsiniz.';
                    getLogDate($findUser->id, '7', $findUser->id, 'Kullanıcı ID', '');
                }else{
                    $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
                }
            }else{
                $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
	}

    public function resetPasswordView($email, $reset_code)
	{
        $data = $this->data;
        $data['email'] = $email;
        $data['reset_code'] = $reset_code;
        
		$this->viewLoad('resetPassword', $data);
	}
    
    public function resetPassword()
	{
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $user = new UserModels($db);

        $email = $this->request->getPost('email');
        $reset_code = $this->request->getPost('reset_code');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('passwordConfirm');
        $findUser = $user->c_one(['email' => $email, 'reset_code' => $reset_code]);
        if (!$validation->check($password, 'required') || !$validation->check($password, 'required')){
            $data['error'] =  'Email adresiniz yada şifre sıfılama kodunuz bulunamadı. Lütfen tekrar deneyiniz.';
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
                $data['success'] =  'Şifreniz başarılı bir şekilde değiştirildi yeni şifreniz ile giriş yapabilirsiniz.';
                getLogDate($findUser->id, '8', $findUser->id, 'Kullanıcı ID', '');
            }else{
                $data['error'] =  'Beklenmeyen bir hata oluştu lütfen daha sonra tekrar deneyiniz.';
            }
        }
        return json_encode($data);
	}
}
    
    
