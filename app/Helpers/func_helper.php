<?php 
    
    function created_at()
    {
        return date('Y-m-d H:i:s');
    }

    function nowDate()
    {
        return date('Y-m-d');
    }

    function nowTime()
    {
        return date('H:i:s');
    }

    function getClientIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //Checking IP From Shared Internet
        {
          $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //To Check IP is Pass From Proxy
        {
          $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip = $_SERVER['REMOTE_ADDR'];
        }
  
        return $ip;
    }

    function namedSettings($settings){
        $named_settings = [];
        foreach ($settings as $setting) {
            $named_settings[$setting->name] = $setting;
        }
        return $named_settings;
    }
    
    function GetProductsNebim(){
        $client = \Config\Services::curlrequest();
        $response = $client->request('GET', 'http://193.111.73.193:88/IntegratorService/RunProc/7E299A342CAD4C448BD8E6EBEAFF3F14?{"ProcName":"sp_ENT_BILT","Parameters":[]}', ['connect_timeout' => 0]);
        return $response->getBody();
    }

    function sef_link($title) {
        $find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '-', '\'');
        $do = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', ' ', '-');
        $perma = strtolower(str_replace($find, $do, $title));
        $perma = preg_replace("@[^A-Za-z0-9\-_]@i", ' ', $perma);
        $perma = trim(preg_replace('/\s+/',' ', $perma));
        $perma = str_replace(' ', '-', $perma);
        return $perma;
    }

    function categoriesAddView($items, $parent = 0, $par = 0)
    {   
        if (!isset($selectOption)) {
            $selectOption = '' ;
        }
        $val = $par;
        foreach ($items as $item)
        {
            if ($item['parent_id'] == '0') {
                $parent_id = '#';
            }else{
                $parent_id = $item['parent_id'];
            }
            if ($par == $item['id']) {
                $opened = 'false';
                $selected = 'true';
            }else{
                $opened = 'false';
                $selected = 'false';
            }
            $selectOption .= '{ "id": "'. $item['id'] .'", "text": "'. $item['title'] .'", "parent" : "'. $parent_id .'", "state": { "opened": '.$opened.', "selected": '.$selected.' }},';

            if (sizeof($item['cocuk']))
            {   $parent = $item['parent_id'];
                $selectOption .= categoriesAddView($item['cocuk'], $par, $val);
            }
        }
        return $selectOption;
    }

    function categoriesListView($items, $parent = 0, $par = 0, $lastTitle = '', $strinText = '')
    {   
        if (!isset($option)) {
            $option = '';
        }
        if (!isset($strinVal)) {
            $strinVal = '';
        }
        $strinText .= $lastTitle;
        foreach ($items as $item)
        {
            $i = 1 ;
            $val = $par;
            for ($i = 1; $i <= $val; ++$i) {
                $strinVal .= $strinText .' -> ';
            }
            $option .= '<option value="'.$item['id'].'"> '.$strinVal.' '.$item['title'];

            if (sizeof($item['cocuk']))
            {
                ++$val;
                $i == 1;
                $lastTitle .= $item['title'];
                $option .= categoriesListView($item['cocuk'], $parent, $val, $lastTitle, $strinText);
            }
            $val = 0;
            $i == 1;
            $option .= '</option>';
        }
        return $option;
    }

    function categoriesAddViewProduct($items, $parent = 0, $par = 0)
    {   
        if (!isset($selectOption)) {
            $selectOption = '' ;
        }
        $val = $par;
        foreach ($items as $item)
        {
            if ($item['parent_id'] == '0') {
                $parent_id = '#';
            }else{
                $parent_id = $item['parent_id'];
            }
            if (is_array($par)) {

            }else{
                if ($par == $item['id']) {
                    $opened = 'false';
                    $selected = 'true';
                }else{
                    $opened = 'false';
                    $selected = 'false';
                }
            }

            if (sizeof($item['cocuk']))
            {   $parent = $item['parent_id'];
                if (is_array($par)) {
                    if (in_array($item['id'], $par)) {
                        $opened = 'false';
                        $selected = 'false';
                    }else{
                        $opened = 'false';
                        $selected = 'false';
                    }
                }
                $selectOption .= '{ "id": "'. $item['id'] .'", "text": "'. $item['title'] .'", "parent" : "'. $parent_id .'", "state": { "opened": '.$opened.', "selected": '.$selected.', "disabled" : "true" }},';
                $selectOption .= categoriesAddViewProduct($item['cocuk'], $par, $val);
            }else{
                if (is_array($par)) {
                    if (in_array($item['id'], $par)) {
                        $opened = 'false';
                        $selected = 'true';
                    }else{
                        $opened = 'false';
                        $selected = 'false';
                    }
                }
                $selectOption .= '{ "id": "'. $item['id'] .'", "text": "'. $item['title'] .'", "parent" : "'. $parent_id .'", "state": { "opened": '.$opened.', "selected": '.$selected.' }},';
            }
        }
        return $selectOption;
    }

    function array_flatten($array) { 
        if (!is_array($array)) { 
        return FALSE; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
        if (is_array($value)) { 
            $result = array_merge($result, array_flatten($value)); 
        } 
        else { 
            $result[$key] = $value; 
        } 
        } 
        return $result; 
    } 
    
    function monthArray(Type $var = null)
    {
        $months = array(
            1=>"Ocak",
            2=>"Şubat",
            3=>"Mart",
            4=>"Nisan",
            5=>"Mayıs",
            6=>"Haziran",
            7=>"Temmuz",
            8=>"Ağustos",
            9=>"Eylül",
            10=>"Ekim",
            11=>"Kasım",
            12=>"Aralık"
    
        );
        return $months;
    }

    function discountRateFind($priceOne, $priceTwo){
        $discountRate = 100 - ($priceTwo * 100 / $priceOne);
        return $discountRate;
    }

    function clearHTML($par, $st = false){
        if ($st){
            if ($par) {
                return htmlspecialchars(addslashes(trim($par)));
            }
        }else {
            if ($par) {
                return strip_tags((trim($par)));
            }
        }
    }

    function shorten($kelime, $str = 40)
    {
        if (strlen($kelime) > $str)
        {

            if (function_exists("mb_substr")) $kelime = mb_substr($kelime, 0, $str, "UTF-8").'..';
            else $kelime = substr($kelime, 0, $str).'..';

        }
        return $kelime;
    }
    
    function priceCalculation($value, $rate){
        $result = ($value / 100) * $rate;
        $lastResult = $value - $result;
        return $lastResult;
    }

	function vat_add($price, $rate){
		$vat = $price -($price / (1 + ($rate/100)));
		return $vat;
	}

	function vat_deducted($price, $rate){
		$vat = $price / (1 + ($rate/100));
		return $vat;
	}

    function fiyatHesaplamaNot($deger,$yuzde){
        $sonuc = ($deger / 100) * $yuzde;
        $sonsonuc = $sonuc;
        return $sonsonuc;
    }

    function fiyatHesaplamaPlus($deger,$yuzde){
        $sonuc = ($deger * 100) / (100 - $yuzde);
        $sonsonuc = $sonuc;
        return $sonsonuc;
    }

    function fiyatHesaplama($deger,$yuzde){
        $sonuc = ($deger / 100) * $yuzde;
        $sonsonuc = $deger - $sonuc;
        return $sonsonuc;
    }

    function priceAreaFunction ($sale_price = '', $discount_price = '', $basket_price = '', $campaign_discount) {
        $discountRate = ''; 
        $totalPrice = ''; 
        $discountPrice = ''; 
        $discountBool = FALSE; 
       
        $basketBool = FALSE; 
        $basketPrice = ''; 
        $basketRate = ''; 

        if ($campaign_discount) {
            $discountBool = TRUE;
            $discountRate = $campaign_discount;
            $totalPrice = $sale_price;
            $discountPrice =  priceCalculation($sale_price, $campaign_discount);
        }else{
            if ($discount_price && ($discount_price < $sale_price)) {
                $discountBool = TRUE;
                $discountRate = discountRateFind($sale_price, $discount_price);
                $totalPrice = $sale_price;
                $discountPrice = $discount_price;
            }else{
                $discountBool = FALSE;
                $totalPrice = $sale_price;
                $discountRate = '';
                $discountPrice = $sale_price;
            }
        }
        if ($basket_price) {
            $basketBool = TRUE; 
            $basketRate = $basket_price;
            $basketPrice = priceCalculation($discountPrice, $basketRate);
        }

        return [
            'discountBool' => $discountBool,
            'discountRate' => $discountRate,
            'totalPrice' => $totalPrice,
            'discountPrice' => $discountPrice,
            'basketBool' => $basketBool,
            'basketRate' => $basketRate,
            'basketPrice' => $basketPrice,
        ];
    }


    function paginate($link, $page = '1', $totalPage, $filter = NULL){
        $paginate = '';
        if ($totalPage != 1) {
            $sol = $page - 5 ;
            $sag = $page + 5;
            if ($page <= 3) {
                $sag = 5;
            }
            if ($sag > $totalPage - 1) {

                $sol = $totalPage -5 ;
            }

            $paginate .= '
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
            ';

            if ($page != 1 ){
                $back = $page ;
                $paginate .= '
                    <li class="page-item">
                        <a class="align-items-center d-flex justify-content-center page-link page-link-arrow" href="'.$link.'/'.--$back.''.$filter.'"><i class="material-icons md-chevron_left"></i></a>
                    </li>
                ';
            }
            for ($i = $sol; $i <= $sag; $i++) {
                if ($i > 0 && $i <= $totalPage) {
                    $active =  $page == $i ? 'active' : '';
                    if ($active == 'active') {
                        $paginate .= '
                            <li class="page-item active">
                                <span class="page-link">
                                    '.$i.'
                                </span>
                            </li>
                        ';
                    }else{
                        $paginate .= '
                            <li class="page-item '.$active.'"><a href="'.$link.'/'.$i.''.$filter.'" class="page-link">'.$i.'</a></li>
                        ';
                    }

                }
            }

            if ($totalPage != $page) {
                $next = $page ;
                $paginate .= '
                    <li class="page-item">
                        <a class="align-items-center d-flex justify-content-center page-link page-link-arrow" href="'.$link.'/'.++$next.''.$filter.'"><i class="material-icons md-chevron_right"></i></a>
                    </li>
                ';
            }

            $paginate .= '
                    </ul>
                </nav>
            ';
        }
        return $paginate;
    }

    function urlutf_8($par){
        $urlutf_8 = array(
            'ç' =>  '%C3%A7', 'ı' =>  '%C4%B1', 'ü' =>  '%C3%BC', 'ğ' =>  '%C4%9F', 'ö' =>  '%C3%B6', 'ş' =>  '%C5%9F', 'İ' =>  '%C4%B0', 'Ğ' =>  '%C4%9E', 'Ü' =>  '%C3%9C', 'Ö' =>  '%C3%96', 'Ş' =>  '%C5%9E',
            'Ç' => '%C3%87', ' ' => '%20', '\'' => '%27', '>' => '%3E', '<' => '%3C', '€' => '%E2%82%AC', "\"" => '%22', "ä" => '%C3%A4', "ß" => '%C3%9F',
        );
        foreach ($urlutf_8 as $key => $urlutf) {
            if (strpos($par, $urlutf) !== FALSE) {
                    $par = str_replace($urlutf, "$key", $par);
            }
        }
        return $par;
    }

    function case_converter( $keyword, $transform='lowercase' ){

		$low = array('a','b','c','ç','d','e','f','g','ğ','h','ı','i','j','k','l','m','n','o','ö','p','r','s','ş','t','u','ü','v','y','z','q','w','x');
		$upp = array('A','B','C','Ç','D','E','F','G','Ğ','H','I','İ','J','K','L','M','N','O','Ö','P','R','S','Ş','T','U','Ü','V','Y','Z','Q','W','X');

		if( $transform=='uppercase' OR $transform=='u' )
		{
			$keyword = str_replace( $low, $upp, $keyword );
			$keyword = function_exists( 'mb_strtoupper' ) ? mb_strtoupper( $keyword ) : $keyword;

		}elseif( $transform=='lowercase' OR $transform=='l' ) {
			
			$keyword = str_replace( $upp, $low, $keyword );
			$keyword = function_exists( 'mb_strtolower' ) ? mb_strtolower( $keyword ) : $keyword;

		}

		return $keyword;

	}
    
    //////// Time Tr Fonksiyonları /////////////////////////

    function timeTR($par)
    {
        $explode = explode(" ", $par);
        $explode2 = explode("-", $explode[0]);
        $zaman = substr($explode[1], 0, 5);
        if ($explode2[1] == "1") $ay = "Ocak";
         elseif ($explode2[1] == "2") $ay = "Şubat";
         elseif ($explode2[1] == "3") $ay = "Mart";
         elseif ($explode2[1] == "4") $ay = "Nisan";
         elseif ($explode2[1] == "5") $ay = "Mayıs";
         elseif ($explode2[1] == "6") $ay = "Haziran";
         elseif ($explode2[1] == "7") $ay = "Temmuz";
         elseif ($explode2[1] == "8") $ay = "Ağustos";
         elseif ($explode2[1] == "9") $ay = "Eylül";
         elseif ($explode2[1] == "10") $ay = "Ekim";
         elseif ($explode2[1] == "11") $ay = "Kasım";
         elseif ($explode2[1] == "12") $ay = "Aralık";
        return $explode2[2]." ".$ay." ".$explode2[0] . " " . $zaman;
    }

    function timeNoTimeTR($par)
    {
        $explode = explode(" ", $par);
        $explode2 = explode("-", $explode[0]);
        $zaman = substr($explode[1], 0, 5);
        if ($explode2[1] == "1") $ay = "Ocak";
         elseif ($explode2[1] == "2") $ay = "Şubat";
         elseif ($explode2[1] == "3") $ay = "Mart";
         elseif ($explode2[1] == "4") $ay = "Nisan";
         elseif ($explode2[1] == "5") $ay = "Mayıs";
         elseif ($explode2[1] == "6") $ay = "Haziran";
         elseif ($explode2[1] == "7") $ay = "Temmuz";
         elseif ($explode2[1] == "8") $ay = "Ağustos";
         elseif ($explode2[1] == "9") $ay = "Eylül";
         elseif ($explode2[1] == "10") $ay = "Ekim";
         elseif ($explode2[1] == "11") $ay = "Kasım";
         elseif ($explode2[1] == "12") $ay = "Aralık";
        return $explode2[2]." ".$ay." ".$explode2[0];
    }

    function managersRole($par){

		if($par == 5){
			return '
                <span class="badge text-uppercase badge-soft-success"> Admin</span>
            ';
		}elseif ($par == 4) {
            return '
                <span class="badge text-uppercase badge-soft-info"> Mağaza Yöneticisi</span>
            ';
        }
    }

    function managersRoleText($par){

		if($par == 5){
			return '
                Admin
            ';
		}elseif ($par == 4) {
            return '
                Mağaza Yöneticisi
            ';
        }
    }

    /////////////// Order Fonksiyonları ///////////////////////



    function orderStatusView($par){

		if($par == 1){
			return '
                <p class="mb-0 font-size-sm font-weight-bold text-secondary">
                    Sipariş Alındı
                </p>
            ';
		}elseif($par == 2){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-info">
                    Sipariş Hazırlanıyor
                </p>
            ';
		}elseif($par == 3){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-info">
                    Sipariş Kargoda
                </p>
            ';
		}elseif($par == 4){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-success">
                    Sipariş Teslim Edildi.
                </p>
            ';
		}elseif($par == 5){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-primary">
                    Sipariş İptal Edildi.
                </p>
            ';
		}elseif($par == 6){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-danger">
                    Sipariş iptal/iade Edildi
                </p>
            ';
		}

    }
    
    function orderStatusPanelView($par){

		if($par == 1){
			return '
                <span class="badge badge-pill badge-soft-dark">Sipariş Alındı</span>
            ';
		}elseif($par == 2){
            return '
                <span class="badge badge-pill badge-soft-info">Sipariş Hazırlanıyor</span>
            ';
		}elseif($par == 3){
            return '
                <span class="badge badge-pill badge-soft-info">Sipariş Kargoda</span>
            ';
		}elseif($par == 4){
            return '
                <span class="badge badge-pill badge-soft-success">Sipariş Teslim Edildi.</span>
            ';
		}elseif($par == 5){
            return '
                <span class="badge badge-pill badge-soft-danger">Sipariş İptal Edildi.</span>
            ';
		}elseif($par == 6){
            return '
                <span class="badge badge-pill badge-soft-danger">Sipariş iptal/iade Edildi</span>
            ';
		}

    }
    
    function orderProductStatusView($par){

		if ($par == 1) {
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-secondary">
                    Sipariş Alindi
                </p>
            ';
        }elseif($par == 2){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-info">
                    Ürün Hazırlanıyor
                </p>
            ';
		}elseif($par == 3){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-info">
                    Ürün Kargoda
                </p>
            ';
		}elseif($par == 4){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-success">
                    Teslim Edildi
                </p>
            ';
		}elseif($par == 5){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-danger">
                    Ürün İptal Edildi
                </p>
            ';
		}elseif($par == 6){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-warning">
                    İade talebi oluşturuldu
                </p>
            ';
		}elseif($par == 7){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-success">
                    İptal Talebiniz Reddedildi.
                </p>
            ';
		}elseif($par == 9){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-danger">
                    Ürün İade Edildi
                </p>
            ';
		}

    }

    function orderProductStatusPanelView($par){

		if ($par == 1) {
            return '
                <p class="badge badge-soft-secondary mb-0 font-size-lg font-weight-bold">
                    Sipariş Alindi
                </p>
            ';
        }elseif($par == 2){
            return '
                <p class="badge badge-soft-info p-4 mb-0 font-size-lg font-weight-bold">
                    Ürün Hazırlanıyor
                </p>
            ';
		}elseif($par == 3){
            return '
                <p class="badge badge-soft-info p-4 mb-0 font-size-lg font-weight-bold">
                    Ürün Kargoda
                </p>
            ';
		}elseif($par == 4){
            return '
                <p class="badge badge-soft-success p-4 mb-0 font-size-lg font-weight-bold">
                    Teslim Edildi
                </p>
            ';
		}elseif($par == 5){
            return '
                <p class="badge badge-soft-danger p-4 mb-0 font-size-lg font-weight-bold">
                    Ürün İptal Edildi
                </p>
            ';
		}elseif($par == 6){
            return '
                <p class="badge badge-soft-info p-4 mb-0 font-size-lg font-weight-bold">
                    İade talebi oluşturuldu
                </p>
            ';
		}elseif($par == 7){
            return '
                <p class="badge badge-soft-success p-4 mb-0 font-size-lg font-weight-bold">
                    İptal Talebi Reddedildi.
                </p>
            ';
		}elseif($par == 9){
            return '
                <p class="badge badge-soft-danger p-4 mb-0 font-size-lg font-weight-bold">
                    Ürün İade Edildi
                </p>
            ';
		}

    }

    function orderReturnStatusPanel($par){

		if($par == 1){
			return '
                <p class="badge badge-secondary p-4 mb-0 font-size-lg font-weight-bold">
                    İade İsteği Gönderildi
                </p>
            ';
		}elseif($par == 2){
            return '
                <p class="mb-0 font-size-sm font-weight-bold badge badge-info p-4">
                    Kargo Numarası Gönderildi
                </p>
            ';
		}elseif($par == 3){
            return '
                <p class="mb-0 font-size-sm font-weight-bold badge badge-info p-4">
                    İade Talebi İnceleniyor
                </p>
            ';
		}elseif($par == 4){
            return '
                <p class="mb-0 font-size-sm font-weight-bold badge badge-success p-4">
                    İade Talebi Onaylandı
                </p>
            ';
		}elseif($par == 5){
            return '
                <p class="mb-0 font-size-sm font-weight-bold badge badge-danger p-4">
                    İade Talebi Reddedildi
                </p>
            ';
		}elseif($par == 6){
            return '
                <p class="mb-0 font-size-sm font-weight-bold badge badge-success p-4">
                    İade Talebi Tammalandı.
                </p>
            ';
		}
    }

    function orderReturnStatusView($par){

		if($par == 1){
			return '
                <p class="mb-0 font-size-sm font-weight-bold text-secondary">
                    İade İsteği Gönderildi
                </p>
            ';
		}elseif($par == 2){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-info ">
                    Kargo Numarası Gönderildi
                </p>
            ';
		}elseif($par == 3){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-info ">
                    İade Talebi İnceleniyor
                </p>
            ';
		}elseif($par == 4){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-success ">
                    İade Talebi Onaylandı
                </p>
            ';
		}elseif($par == 5){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-danger ">
                    İade Talebi Reddedildi
                </p>
            ';
		}elseif($par == 6){
            return '
                <p class="mb-0 font-size-sm font-weight-bold text-success ">
                    İade Talebi Tammalandı.
                </p>
            ';
		}
    }
    
    function getLogDate ($user_id, $activity_id, $item_id = '', $item_type = '', $item_description = '') {
        $detect = systemInfo();
        $browser = browser();
        if (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $real_ip_adress = $_SERVER['HTTP_CLIENT_IP'];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $real_ip_adress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $real_ip_adress = $_SERVER['REMOTE_ADDR'];
        }

        $cip = $real_ip_adress;
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $cip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            $output = array(
                "city"           => @$ipdat->geoplugin_city,
                "city_code"   => @$ipdat->geoplugin_regionCode,
                "country"        => @$ipdat->geoplugin_countryName,
                "country_code"   => @$ipdat->geoplugin_countryCode,
                "latitude" => @$ipdat->geoplugin_latitude,
                "longitude" => @$ipdat->geoplugin_longitude,
            );
        }

        $url = base_url('/insertLogData');
        $fields = array(
            'user_id' => $user_id,
            'device' => $detect['device'],
            'user_agent' => $browser,
            'ip_address' => $cip,
            'location' => $output['city'],
            'activity_id' => $activity_id,
            'item_id' => $item_id,
            'item_type' => $item_type,
            'item_description' => $item_description
        );
        $fields_string = http_build_query($fields);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt ($curl, CURLOPT_POST, TRUE);
        curl_setopt ($curl, CURLOPT_POSTFIELDS, $fields_string);

        curl_setopt($curl, CURLOPT_USERAGENT, 'api');

        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 500);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($curl, CURLOPT_DNS_CACHE_TIMEOUT, 10);

        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

        curl_exec($curl);

        curl_close($curl);
    }

    function systemInfo(){
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform    = "Unknown OS Platform";
        $os_array       = array('/windows nt 10/i'      =>  'Windows 10',
                                '/windows phone 8/i'    =>  'Windows Phone 8',
                                '/windows phone os 7/i' =>  'Windows Phone 7',
                                '/windows nt 6.3/i'     =>  'Windows 8.1',
                                '/windows nt 6.2/i'     =>  'Windows 8',
                                '/windows nt 6.1/i'     =>  'Windows 7',
                                '/windows nt 6.0/i'     =>  'Windows Vista',
                                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                '/windows nt 5.1/i'     =>  'Windows XP',
                                '/windows xp/i'         =>  'Windows XP',
                                '/windows nt 5.0/i'     =>  'Windows 2000',
                                '/windows me/i'         =>  'Windows ME',
                                '/win98/i'              =>  'Windows 98',
                                '/win95/i'              =>  'Windows 95',
                                '/win16/i'              =>  'Windows 3.11',
                                '/macintosh|mac os x/i' =>  'Mac OS X',
                                '/mac_powerpc/i'        =>  'Mac OS 9',
                                '/linux/i'              =>  'Linux',
                                '/ubuntu/i'             =>  'Ubuntu',
                                '/iphone/i'             =>  'iPhone',
                                '/ipod/i'               =>  'iPod',
                                '/ipad/i'               =>  'iPad',
                                '/android/i'            =>  'Android',
                                '/blackberry/i'         =>  'BlackBerry',
                                '/webos/i'              =>  'Mobile');
        $found = false;
        $device = '';
        foreach ($os_array as $regex => $value)
        {
            if($found)
            break;
            else if (preg_match($regex, $user_agent))
            {
                $os_platform    =   $value;
                $device = !preg_match('/(windows|mac|linux|ubuntu)/i',$os_platform)
                        ?'Mobile':(preg_match('/phone/i', $os_platform)?'Mobile':'Desktop');
            }
        }
        $device = !$device? 'Desktop':$device;
        return array('os'=>$os_platform,'device'=>$device);
    }

    function browser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $browser        =   "Unknown Browser";

        $browser_array  = array('/msie/i'       =>  'Internet Explorer',
                                '/firefox/i'    =>  'Firefox',
                                '/safari/i'     =>  'Safari',
                                '/chrome/i'     =>  'Chrome',
                                '/opera/i'      =>  'Opera',
                                '/netscape/i'   =>  'Netscape',
                                '/maxthon/i'    =>  'Maxthon',
                                '/konqueror/i'  =>  'Konqueror',
                                '/mobile/i'     =>  'Handheld Browser');

        foreach ($browser_array as $regex => $value)
        {
            if($found)
            break;
            else if (preg_match($regex, $user_agent,$result))
            {
                $browser    =   $value;
            }
        }
        return $browser;
    }
?>