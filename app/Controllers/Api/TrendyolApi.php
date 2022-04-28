<?php 

    namespace App\Controllers\Api;
    
    use App\Controllers\BaseController;
    use App\Libraries\Trendyol;

    class TrendyolApi extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function getBrand (){
            $trendyol = new Trendyol();
            print_r($trendyol->getBrands());
        }

        public function getCategories (){
            $trendyol = new Trendyol();
            print_r($trendyol->getCategories());
        }
          
    }
