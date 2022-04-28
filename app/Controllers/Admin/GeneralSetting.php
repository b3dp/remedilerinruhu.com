<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModels;
use App\Models\ProductModels;

class GeneralSetting extends BaseController
{
    public function _construct()
    {
        
    }

    public function generalList()
    {
        $data['sidebarActive'] = 'setting';
        $data['sidebarAltActive'] = 'setting/general-setting';
        $db = db_connect();
        $settingModels = new SettingModels($db);
        $productModels = new ProductModels($db);
        $data['seasons'] = $productModels->c_season(['is_active' => '1', 'season !=' => ""]);
        $data['generalSettings'] = $settingModels->c_all(['type' => 'general']);
        return view ("admin/generalSetting/general-setting", $data);
    }
    public function socialList()
    {
        $data['sidebarActive'] = 'setting';
        $data['sidebarAltActive'] = 'setting/social-setting';
        $db = db_connect();
        $settingModels = new SettingModels($db);
        $data['generalSettings'] = $settingModels->c_all(['type' => 'social']);
        return view ("admin/generalSetting/social-setting", $data);
    }
    public function contactList()
    {
        $data['sidebarActive'] = 'setting';
        $data['sidebarAltActive'] = 'setting/contact-setting';
        $db = db_connect();
        $settingModels = new SettingModels($db);
        $data['generalSettings'] = $settingModels->c_all(['type' => 'contact']);
        return view ("admin/generalSetting/contact-setting", $data);
    }
    
    public function update()
    {
        $validation =  \Config\Services::validation();
        $db = db_connect();
        $settingModels = new SettingModels($db);

        foreach ($_POST as $type => $grup){
            foreach ($grup as $key => $veri){
                $updateData = [
                    'value' => $veri
                ];
                $whereData = [
                    'type' => $type,
                    'name' => $key
                ];
                $update = $settingModels->edit($whereData, $updateData);
            }
        }
        if ($update){
            $data["success"] = 'Başarılı bir şekilde güncellendi';
        }else{
            $data["error"] = 'Beklenmeyen bir hata oluştu.';
        }

        return json_encode($data);
    }


}