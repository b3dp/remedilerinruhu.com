<?php 

    namespace App\Controllers\Api;
    
    use App\Controllers\BaseController;
    use App\Models\ProductModels;
    use App\Models\Category;
    use App\Models\AttributeModels;
    use App\Models\ProductFeatureModels;
    use App\Models\AttributeGroupModels;
    use App\Models\BrandModels;
    use App\Models\UserModels;
    use App\Models\AddressModels;
    use App\Models\OrderModels;
    use App\Libraries\YurticiKargo;

    class Cargo extends BaseController
    { 
        public function _construct()
        {
            
        }

        public function orderCreateCargo($order_id, $cargoCount, $invonceNo)
        {
            ini_set('max_execution_time', 1200);
            $db = db_connect();
            $yurtici = new YurticiKargo(getenv('yurtici.username'), getenv('yurtici.password'), getenv('yurtici.language'));
            $productFeatureModels = new ProductFeatureModels($db);
            $userModels = new UserModels($db);
            $orderModels = new OrderModels($db);
            $addressModels = new AddressModels($db);

            $orderFind = $orderModels->c_one(['id' => $order_id]);
            $orderDetailFind = $orderModels->orderDetailAll(['order_id' => $order_id]);
            $userFind = $userModels->c_one(['id' => $orderFind->user_id]);
            $addressFind = $addressModels->c_one('order_address_clone', ['id' => $orderFind->shipping_address]);
            
            $town = $addressModels->c_one('town', ['TownID' => $addressFind->user_town], 'TownName ASC');
            $city = $addressModels->c_one('city', ['CityID' => $addressFind->user_city], 'CityName ASC');
            $neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $addressFind->user_neighborhood], 'NeighborhoodName ASC');
            $address = $addressFind->address . ' ' . $neighborhood->NeighborhoodName . ' ' . $town->TownName . '/' . $city->CityName;

            $yurtici->shippingOrderVoNormal($orderFind->order_no, $invonceNo, $cargoCount, $userFind->name, $address, $town->TownName, $city->CityName,  $addressFind->email, $addressFind->phone);
            $returnDate = $yurtici->createShipment();
            $newXML = strstr($returnDate, '<ShippingOrderResultVO>', false);
            $jsonXML = strstr($newXML, '</ShippingOrderResultVO>', true). '</ShippingOrderResultVO>';
            $xml = simplexml_load_string($jsonXML, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            return $json;
        }

        public function cargoTracking($cargoNumber)
        {
            $yurticiparams = [
                "wsUserName" => getenv('yurtici.username'),
                "wsPassword" => getenv('yurtici.password'),
                "userLanguage" => getenv('yurtici.language'),
            ];
                
            $yurtici = new YurticiKargo(getenv('yurtici.username'), getenv('yurtici.password'), getenv('yurtici.language'));
            return view ("admin/product/nebim");
        }
          
    }
