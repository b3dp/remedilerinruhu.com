<?php

namespace App\Libraries;

use App\Controllers\BaseController;
use App\Libraries\Seo;
use App\Models\Category;
use App\Models\UserModels;
use App\Models\OrderModels;
use App\Models\fixedFieldsModels;
use App\Models\MenuGroupModels;
use App\Models\MenuModels;
use App\Models\SettingModels;

class LoadView extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function viewLoad($viewName, $data)
    {
        $db = db_connect();
        $category = new Category($db);
        $userModels = new UserModels($db);
        $orderModels = new OrderModels($db);
        $fixedFieldsModels = new fixedFieldsModels($db);
        $settingModels = new SettingModels($db);
        $menuGroupModels = new MenuGroupModels($db);
        $menuModels = new MenuModels($db);
        if(isset($_COOKIE["__remember"]) && !isset($_SESSION["user"])) {
            $login = (array) json_decode($_COOKIE["__remember"]);
            $email = $login["email"];
            $sifre = $login["g_s"];
    
            $findUser = $userModels->c_one(['email' => $email, 'remember_token' => $sifre]);
            if($findUser) {
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
                $this->session = \Config\Services::session();
                if ($this->session->get('user')) {
                    $this->data['user_id'] = $this->session->get('user')['id'];
                    $this->data['user_email'] = $this->session->get('user')['email'];
                    $this->data['user_role'] = $this->session->get('user')['role'];
                    $this->data['user_name'] = $this->session->get('user')['name'];
                    $this->data['user_surname'] = $this->session->get('user')['surname'];
                    $this->data['user_fullName'] = $this->session->get('user')['fullName'];
                    $this->data['basket_count'] = count($this->session->get('order.product'));
                    $this->data['order'] = $this->session->get('order');
                    $this->data['order_product'] = $this->session->get('order.product');
                }
            }
        }
        $data['headerCategory'] = $category->c_all_list();
        $data['category'] = $category;
        $data['delivery_options_first'] = $orderModels->delivery_c_one(['is_default' => '1', 'is_active' => '1']);
        
        $data['menu_group'] = $menuGroupModels->c_all(['is_active' => '1']);
        $data['menuModels'] = $menuModels;
        $data['fixedInfo'] = $fixedFieldsModels->c_all(['is_active' => '1', 'group_id' => '1']);
        $data['socialMedia'] = $settingModels->c_all(['type' => 'social']);

        $generalSetting = $settingModels->c_all(['type' => 'general']);
        $general = namedSettings($generalSetting);

        $contactSetting = $settingModels->c_all(['type' => 'contact']);
        $data['contact'] = namedSettings($contactSetting);

        $site_title = $general['site_title']->value;
        $site_keyword = $general['site_keyword']->value;
        $site_desc = $general['site_desc']->value;
        $home_seo_title = $general['home_seo_title']->value;
        $home_seo_desc = $general['home_seo_desc']->value;
        $site_img = 'assets/img/tedarek_share_image_1.jpg';
        $site_url =  base_url().'/';
        $seo = new Seo($site_title, $site_desc, $site_img , $site_url, $site_keyword);
        $data['home_seo_title'] = $home_seo_title;
        $data['home_seo_desc'] = $home_seo_desc;
        $data['PageSeo'] = $seo;
        echo view($theme.$viewName, $data);
    }
    
}