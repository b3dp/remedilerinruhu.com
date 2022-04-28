<?php

namespace App\Controllers;

use App\Libraries\LoadView;
use App\Libraries\Iyzico;
use App\Models\Category;
use App\Models\ProductFilterModels;
use App\Models\ProductDetailModels;
use App\Models\UserModels;
use App\Models\OrderModels;
use App\Models\AttributeGroupModels;
use App\Models\AddressModels;
use App\Models\CIsessionModels;
use App\Models\ContractsModels;
use App\Models\SettingModels;
use App\Models\CampaignModels;
use App\Models\BasketModels;

class Checkout extends LoadView
{

	public function index () {
		
		$data = $this->data;
		$this->session = \Config\Services::session();
		if (!$this->data['user_id']) {
			return redirect()->to('giris-yap?return=siparis'); 
		}
		$db =  db_connect();
		$orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$order = $this->session->get('order');
		$data['headerBasketDisabled'] = '1';
		$data['basketProduct'] = $this->sessionCheckoutProduct();
		$addressModels = new AddressModels($db);
		if (!$order['order_no'] || !$order['product']) {
			return redirect()->route('sepetim');
		}
		if ($this->data['user_id']) {
			$data['city'] = $addressModels->c_all('city', ['CountryID' => '212'], '');
			$data['delivery_address'] = $addressModels->c_all('user_address', ['user_id' => $this->data['user_id'], 'address_type' => 'delivery'], 'address_default DESC, id DESC');
			$data['billing_address'] = $addressModels->c_all('user_address', ['user_id' => $this->data['user_id'], 'address_type' => 'billing'], 'address_default DESC, id DESC');
			$data['addressModels'] = $addressModels;
        }else{
			$data['city'] = $addressModels->c_all('city', ['CountryID' => '212'], 'CityName ASC');
			$userCheackOrder = $userModels->c_one(['id' => $order['order_user'], 'is_active' => '1', 'is_guest' => '1' ]);
			$thisProductOrderFind = $orderModels->c_one(['user_id' => $userCheackOrder->id, 'order_no' => $order['order_no']]);
			$data['shipping_id'] = $thisProductOrderFind->shipping_id;
			$data['order_note'] = $thisProductOrderFind->order_note;
			$data['delivery_address'] = $addressModels->c_one('user_address', ['id' => $thisProductOrderFind->shipping_address]);
			if ($data['delivery_address']) {
				$data['town'] = $addressModels->c_all('town', ['CityID' => $data['delivery_address']->user_city], 'TownName ASC');
				$data['neighborhood'] = $addressModels->neighborhood_all(['d.TownID' => $data['delivery_address']->user_town]); 
			}
		}
		$data['delivery_options'] = $orderModels->delivery_c_all();
		$data['couponCode'] = $order['coupon_code'];
		getLogDate($this->data['user_id'], '34', $order['order_no'], 'Sipariş Numarası', '');
		$this->viewLoad('checkout', $data);
	}

	public function paymentView () {
		
		$data = $this->data;
		$this->session = \Config\Services::session();
		$db =  db_connect();
		$orderModels = new OrderModels($db);
		$iyzico = new Iyzico();
		$userModels = new UserModels($db);
		$cisessionModels = new CIsessionModels($db);
		$contractsModels = new ContractsModels($db);
		$settingModels = new SettingModels($db);
		$order = $this->session->get('order');
		$orderProduct = $this->session->get('order.product');
		$user = $this->session->get('user');
		$data['headerBasketDisabled'] = '1';
		$data['order_no'] = $order['order_no'];
		if ($this->data['user_id']) {
			$data['basketProduct'] = $this->sessionCheckoutProduct(1);
			$addressModels = new AddressModels($db);
			$data['city'] = $addressModels->c_all('city', ['CountryID' => '212'], 'CityName ASC');
			$userCheackOrder = $userModels->c_one(['id' => $this->data['user_id'], 'is_active' => '1', 'is_guest' => '0' ]);
        }else{
			$data['basketProduct'] = $this->sessionCheckoutProduct(1);
			$addressModels = new AddressModels($db);
			$data['city'] = $addressModels->c_all('city', ['CountryID' => '212'], 'CityName ASC');
			$userCheackOrder = $userModels->c_one(['id' => $order['order_user'], 'is_active' => '1', 'is_guest' => '1' ]);
		}

		if (!$userCheackOrder) {
			return redirect()->route('siparis');
		}

		if (!$orderProduct) {
			return redirect()->route('siparis');
		}
		$thisProductOrderFind = $orderModels->c_one(['user_id' => $userCheackOrder->id, 'order_no' => $order['order_no']]);
		$data['shipping_id'] = $thisProductOrderFind->shipping_id;
		$data['order_note'] = $thisProductOrderFind->order_note;
		$data['delivery_address'] = $addressModels->c_one('order_address_clone', ['id' => $thisProductOrderFind->shipping_address]);
		$billing_address = $addressModels->c_one('order_address_clone', ['id' => $thisProductOrderFind->billing_address]);
		$data['billing_address'] = $billing_address;
		$delivery_options = $orderModels->delivery_c_one(['id' => $thisProductOrderFind->shipping_id]);
		$data['delivery_options'] = $delivery_options;
		
		$data['town'] = $addressModels->c_one('town', ['TownID' => $data['delivery_address']->user_town], 'TownName ASC');
        $data['city']= $addressModels->c_one('city', ['CityID' => $data['delivery_address']->user_city], 'CityName ASC');

		$data['townBilling'] = $addressModels->c_one('town', ['TownID' => $data['billing_address']->user_town], 'TownName ASC');
        $data['cityBilling']= $addressModels->c_one('city', ['CityID' => $data['billing_address']->user_city], 'CityName ASC');
		
		$delivery_address = $data['delivery_address'];
		if ($data['delivery_address']) {
			$city = $addressModels->c_one('city', ['CityID' => $data['delivery_address']->user_city], 'CityName ASC');
			$distanceContract = $contractsModels->c_one(['id' => '1']);
			foreach ($data['basketProduct'] as $row) {
				$productArea .= $row['title'] . ' x '. $row['piece'] . ' ' . number_format(($row['last_price'] * $row['piece']), 2) . ' TL <br><br>';
			}
			$contactSetting = $settingModels->c_all(['type' => 'contact']);
			$contact = namedSettings($contactSetting);

			$distanceContract = $contractsModels->c_one(['id' => '1']);

			$distanceContractText = str_replace('{uye_ad_soyad}', $delivery_address->receiver_name, $distanceContract->description);
			$distanceContractText = str_replace('{uye_adres}', $delivery_address->address, $distanceContractText);
			$distanceContractText = str_replace('{uye_telefon}', $delivery_address->phone, $distanceContractText);
			$distanceContractText = str_replace('{uye_email}', $delivery_address->email, $distanceContractText);
			$distanceContractText = str_replace('{siparis_urunler}', $productArea, $distanceContractText);
			$distanceContractText = str_replace('{siparis_odeme_sekli}', 'Online Kredi/Banka Kartı', $distanceContractText);
			$distanceContractText = str_replace('{siparis_tutar}', number_format($thisProductOrderFind->total_price, 2) . ' TL' , $distanceContractText);
			$distanceContractText = str_replace('{siparis_teslimat_ad_soyad}', $delivery_address->receiver_name , $distanceContractText);
			$distanceContractText = str_replace('{siparis_teslimat_adres}', $delivery_address->address , $distanceContractText);
			$distanceContractText = str_replace('{siparis_teslimat_telefon}', $delivery_address->phone , $distanceContractText);
			$distanceContractText = str_replace('{siparis_teslimat_email}', $delivery_address->email , $distanceContractText);

			$distanceContractText = str_replace('{siparis_fatura_ad_soyad}', $billing_address->receiver_name , $distanceContractText);
			$distanceContractText = str_replace('{siparis_fatura_adres}', $billing_address->address , $distanceContractText);
			$distanceContractText = str_replace('{siparis_fatura_telefon}', $billing_address->phone , $distanceContractText);
			$distanceContractText = str_replace('{siparis_fatura_email}', $billing_address->email , $distanceContractText);
			$distanceContractText = str_replace('{biltstore_tel}', $contact['biltstore_tel']->value , $distanceContractText);
			$distanceContractText = str_replace('{biltstore_fax}', $contact['biltstore_fax']->value , $distanceContractText);
			$distanceContractText = str_replace('{biltstore_email}', $contact['biltstore_email']->value , $distanceContractText);
			$distanceContractText = str_replace('{biltstore_kep}', $contact['biltstore_kep']->value , $distanceContractText);
			$distanceContractText = str_replace('{biltstore_iade_adresi}', $contact['biltstore_iade_adresi']->value , $distanceContractText);
			$distanceContractText = str_replace('{biltstore_kargo_firma_adresi}', $contact['biltstore_kargo_firma_adresi']->value , $distanceContractText);
			$distanceContractText = str_replace('{biltstore_kargo_firma}', $contact['biltstore_kargo_firma']->value , $distanceContractText);

			$data['distanceContract'] = $distanceContractText;

			$orderContract = $contractsModels->c_one(['id' => '6']);
			$orderContractText = str_replace('{uye_ad_soyad}', $delivery_address->receiver_name, $orderContract->description);
			$orderContractText = str_replace('{uye_adres}', $delivery_address->address, $orderContractText);
			$orderContractText = str_replace('{uye_telefon}', $delivery_address->phone, $orderContractText);
			$orderContractText = str_replace('{uye_email}', $delivery_address->email, $orderContractText);
			$orderContractText = str_replace('{siparis_urunler}', $productArea, $orderContractText);
			$orderContractText = str_replace('{siparis_odeme_sekli}', 'Online Kredi/Banka Kartı', $orderContractText);
			$orderContractText = str_replace('{siparis_tutar}', number_format($thisProductOrderFind->total_price, 2) . ' TL' , $orderContractText);
			$orderContractText = str_replace('{siparis_teslimat_ad_soyad}', $delivery_address->receiver_name , $orderContractText);
			$orderContractText = str_replace('{siparis_teslimat_adres}', $delivery_address->address , $orderContractText);
			$orderContractText = str_replace('{siparis_teslimat_telefon}', $delivery_address->phone , $orderContractText);
			$orderContractText = str_replace('{siparis_teslimat_email}', $delivery_address->email , $orderContractText);

			$orderContractText = str_replace('{siparis_fatura_ad_soyad}', $billing_address->receiver_name , $orderContractText);
			$orderContractText = str_replace('{siparis_fatura_adres}', $billing_address->address , $orderContractText);
			$orderContractText = str_replace('{siparis_fatura_telefon}', $billing_address->phone , $orderContractText);
			$orderContractText = str_replace('{siparis_fatura_email}', $billing_address->email , $orderContractText);
			$orderContractText = str_replace('{biltstore_tel}', $contact['biltstore_tel']->value , $orderContractText);
			$orderContractText = str_replace('{biltstore_fax}', $contact['biltstore_fax']->value , $orderContractText);
			$orderContractText = str_replace('{biltstore_email}', $contact['biltstore_email']->value , $orderContractText);
			$orderContractText = str_replace('{biltstore_kep}', $contact['biltstore_kep']->value , $orderContractText);
			$orderContractText = str_replace('{biltstore_iade_adresi}', $contact['biltstore_iade_adresi']->value , $orderContractText);
			$orderContractText = str_replace('{biltstore_kargo_firma_adresi}', $contact['biltstore_kargo_firma_adresi']->value , $orderContractText);
			$orderContractText = str_replace('{biltstore_kargo_firma}', $contact['biltstore_kargo_firma']->value , $orderContractText);

			$data['orderContractText'] = $orderContractText;
		

			$withdrawal = $contractsModels->c_one(['id' => '7']);
			$withdrawalText = str_replace('{uye_ad_soyad}', $delivery_address->receiver_name, $withdrawal->description);
			$withdrawalText = str_replace('{uye_adres}', $delivery_address->address, $withdrawalText);
			$withdrawalText = str_replace('{uye_telefon}', $delivery_address->phone, $withdrawalText);
			$withdrawalText = str_replace('{uye_email}', $delivery_address->email, $withdrawalText);
			$withdrawalText = str_replace('{siparis_urunler}', $productArea, $withdrawalText);
			$withdrawalText = str_replace('{siparis_odeme_sekli}', 'Online Kredi/Banka Kartı', $withdrawalText);
			$withdrawalText = str_replace('{siparis_tutar}', number_format($thisProductOrderFind->total_price, 2) . ' TL' , $withdrawalText);
			$withdrawalText = str_replace('{siparis_teslimat_ad_soyad}', $delivery_address->receiver_name , $withdrawalText);
			$withdrawalText = str_replace('{siparis_teslimat_adres}', $delivery_address->address , $withdrawalText);
			$withdrawalText = str_replace('{siparis_teslimat_telefon}', $delivery_address->phone , $withdrawalText);
			$withdrawalText = str_replace('{siparis_teslimat_email}', $delivery_address->email , $withdrawalText);

			$withdrawalText = str_replace('{siparis_fatura_ad_soyad}', $billing_address->receiver_name , $withdrawalText);
			$withdrawalText = str_replace('{siparis_fatura_adres}', $billing_address->address , $withdrawalText);
			$withdrawalText = str_replace('{siparis_fatura_telefon}', $billing_address->phone , $withdrawalText);
			$withdrawalText = str_replace('{siparis_fatura_email}', $billing_address->email , $withdrawalText);
			$withdrawalText = str_replace('{biltstore_tel}', $contact['biltstore_tel']->value , $withdrawalText);
			$withdrawalText = str_replace('{biltstore_fax}', $contact['biltstore_fax']->value , $withdrawalText);
			$withdrawalText = str_replace('{biltstore_email}', $contact['biltstore_email']->value , $withdrawalText);
			$withdrawalText = str_replace('{biltstore_kep}', $contact['biltstore_kep']->value , $withdrawalText);
			$withdrawalText = str_replace('{biltstore_iade_adresi}', $contact['biltstore_iade_adresi']->value , $withdrawalText);
			$withdrawalText = str_replace('{biltstore_kargo_firma_adresi}', $contact['biltstore_kargo_firma_adresi']->value , $withdrawalText);
			$withdrawalText = str_replace('{biltstore_kargo_firma}', $contact['biltstore_kargo_firma']->value , $withdrawalText);

			$data['withdrawalText'] = $withdrawalText;

			$mpdf = new \Mpdf\Mpdf();
			$mpdf->WriteHTML($distanceContractText);
			if (!file_exists('uploads/orders/'.$order['order_no'].'')) {
				mkdir('uploads/orders/'.$order['order_no'].'');
			}
			$mpdf->Output('uploads/orders/'.$order['order_no'].'/mesafeli_satis_sozlesmesi.pdf','F'); // opens in browser

			$mpdf = new \Mpdf\Mpdf();
			$mpdf->WriteHTML($orderContractText);
			if (!file_exists('uploads/orders/'.$order['order_no'].'')) {
				mkdir('uploads/orders/'.$order['order_no'].'');
			}
			$mpdf->Output('uploads/orders/'.$order['order_no'].'/siparis_on_bilgilendirme.pdf','F'); // opens in browser

			$mpdf = new \Mpdf\Mpdf();
			$mpdf->WriteHTML($withdrawalText);
			if (!file_exists('uploads/orders/'.$order['order_no'].'')) {
				mkdir('uploads/orders/'.$order['order_no'].'');
			}
			$mpdf->Output('uploads/orders/'.$order['order_no'].'/cayma_hakki.pdf','F'); // opens in browser

		}else{
			return redirect()->route('siparis');
		}
		if ($billing_address->identification_number) {
			$identification_number = $billing_address->identification_number;
		}else{
			$identification_number = $data['delivery_address']->identification_number;
		}
		////////////////////// Iyziko Kart Area Data Start ////////////////////////
			if ($thisProductOrderFind->coupon_discount_type == '2') {
				$setPrice = $thisProductOrderFind->total_price - $thisProductOrderFind->shipping_price;
			}else{
				$setPrice = $thisProductOrderFind->total_price - $thisProductOrderFind->shipping_price;
			}
			$payment = $iyzico->setForm([
				'ConversationId' => $order['order_no'],
				'setPrice' => $setPrice,
				'setPaidPrice' => $thisProductOrderFind->total_price,
				'setBasketId' => $order['order_no'],
			])
			->setBuyer([
				'setId' => $userCheackOrder->id,
				'setName' => $userCheackOrder->name,
				'setSurname' => $userCheackOrder->surname,
				'setGsmNumber' => $userCheackOrder->phone,
				'setEmail' => $userCheackOrder->email,
				'setIdentityNumber' => $identification_number,
				'setRegistrationAddress' => $delivery_address->address,
				'setIp' => getClientIpAddress(),
				'setCity' => $city->CityName,
				'setCountry' => 'Türkiye',
			])
			->setShipping([
				'setContactName' => $delivery_address->receiver_name,
				'setCity' => $data['city']->CityName,
				'setCountry' => 'Türkiye',
				'setAddress' => $delivery_address->address
			])
			->setBilling([
				'setContactName' => $billing_address->receiver_name,
				'setCity' => $data['cityBilling']->CityName,
				'setCountry' => 'Türkiye',
				'setAddress' => $billing_address->address
			])
			->setItems($data['basketProduct'])
			->paymentForm();
		////////////////////// Iyziko Kart Area Data End /////////////////////////

		$data['paymentContent'] = $payment->getCheckoutFormContent();
		$data['getErrorMessage'] = $payment->getErrorMessage();
		$data['paymentStatus'] = $payment->getStatus();
		print_r($data['getErrorMessage']);
		helper('cookie');
		$ci_session = get_cookie('ci_session');
		$ciSessionData = [
			'value' => json_encode($_SESSION),
			'created_at' => created_at()
		];
		$cisessionModels->add($ciSessionData);
		$ci_id = $db->insertID();
		set_cookie('ci_session_data', $ci_id, 7200, '', '/', '', TRUE, '', 'None');
		getLogDate($this->data['user_id'], '35', $order['order_no'], 'Sipariş Numarası', '');
		$this->viewLoad('checkout-payment', $data);
	}
	
	public function paymentCallback ($status = '') {
		helper('cookie');
		$data = $this->data;
		$this->session = \Config\Services::session();
		$this->session->set('user', json_decode(get_cookie('user'), true));

		$db =  db_connect();
		$category = new Category($db);
		$orderModels = new OrderModels($db);
		$userModels = new UserModels($db);
		$addressModels = new AddressModels($db);
		$cisessionModels = new CIsessionModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$settingModels = new SettingModels($db);
		$basketModels = new BasketModels($db);
		$iyzico = new Iyzico();
		$token = $this->request->getPost('token');
		$response = $iyzico->callbackForm($token, $order_no);
		
		$data['order_no'] = $order_no;
		$data['paymentStatus'] = $response->getPaymentStatus();
		$data['paymentID'] = $response->getpaymentId();
		$paymentItems = $response->getpaymentItems();
		$data['paymentTransactionId '] = '';
		if ($data['paymentStatus'] != 'SUCCESS') {
			$ci_session = get_cookie('ci_session_data');
			$thisCISession = $cisessionModels->c_one(['id' => $ci_session]);
			$_SESSION = json_decode($thisCISession->value, true);
			getLogDate($this->data['user_id'], '36', $order['order_no'], 'Sipariş Numarası', '');
		}else {
			$contactSetting = $settingModels->c_all(['type' => 'contact']);
			$contact = namedSettings($contactSetting);

			$ci_session = get_cookie('ci_session_data');
			$thisCISession = $cisessionModels->c_one(['id' => $ci_session]);
			$sesion = json_decode($thisCISession->value, true);
			$data['order_no'] = $sesion['order']['order_no'];
			$this->session->set('user', $sesion['user']);
			$thisProductOrderFind = $orderModels->c_one(['order_no' => $sesion['order']['order_no'], 'status' => '99']);
			$data['user_id'] = $thisProductOrderFind->user_id;
			if (!$thisProductOrderFind) {
				return redirect()->route('');
			}
			$userCheackOrder = $userModels->c_one(['id' => $thisProductOrderFind->user_id, 'is_active' => '1']);
			$updateOrderData = [
				'status' => '1',
				"iyziko_payment_id" => $data['paymentID'],
				"buy_at" => created_at(),
				"updated_at" => created_at()
			];
			$insertOrder = $orderModels->editOrderNo($sesion['order']['order_no'], $updateOrderData);
			foreach ($paymentItems as $row) {
				$itemID = $row->getitemId();
				$iyziko_payment_transaction_id = $row->getpaymentTransactionId();
				$order_no =  $sesion['order']['order_no'];
				$updateOrderDetailData = [
					"iyziko_payment_transaction_id" => $iyziko_payment_transaction_id,
					"updated_at" => created_at()
				];
				$updateOrderDetail = $orderModels->orderDetailUpdateWhere(['product_order_no' => $order_no, 'variant_id' => $itemID], $updateOrderDetailData);
				if (!$updateOrderDetail) {
					$updateOrderDetail = $orderModels->orderDetailUpdateWhere(['product_order_no' => $order_no, 'product_id' => $itemID], $updateOrderDetailData);
				}
			}
			$thisProductOrderDetailFind = $orderModels->orderDetailAll(['product_order_no' => $sesion['order']['order_no']]);
			foreach ($thisProductOrderDetailFind as $veriable) {
				$row = $productDetailModels->c_one(['p.id' => $veriable->product_id, 'pa.id' => $veriable->variant_id ]);
				if ($row) {
					$product_id = $veriable->product_id;
					$variant_barcode = $veriable->variant_id;
				}else{
					$product_id = $veriable->product_id;
				}

				if ($variant_barcode) {
					$productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.is_active' => '1', 'pa.id' => $veriable->variant_id]);
				}else{
					$productCombinationOne = $productDetailModels->productCombinationOne(['pa.id_product' => $row->id, 'pa.is_active' => '1', 'pa.is_active' => '1']);
				}

				$productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' => $productCombinationOne->id], '1');
				if (!$productPicture) {
					$productPicture = $productDetailModels->c_all_image(['product_id' => $row->id], '1');
				}
				$featureArray = explode(' - ', $row->attr);
				$categoryArray = explode(',', $row->category_id);

				$combinationIDArray = explode(' - ', $productCombinationOne->attr_id);
				$combinationGroupIDArray = explode(' - ', $productCombinationOne->attr_group_id);
				$combinationTitleArray = explode(' - ', $productCombinationOne->attr);
				$combinationGroupTitleArray = explode(' - ', $productCombinationOne->attr_group);
				foreach ($combinationGroupIDArray as $key => $val) {
					$selectCombinationArray[$key]['id'] = $combinationIDArray[$key];
					$selectCombinationArray[$key]['title'] = $combinationTitleArray[$key];
					$selectCombinationArray[$key]['group_title'] = $combinationGroupTitleArray[$key];
					$selectCombinationArray[$key]['group_id'] = $combinationGroupIDArray[$key];
				}
				$combinationView = '';
				foreach ($selectCombinationArray as $item) {
					$combinationView .= '<span class="order-list__item-title"style="color: #555; font-size: 14px; line-height: 1.4"><b>'. $item['group_title'] .'</b> : '.  $item['title'] .'</span> - ';
				}
				$combinationView = rtrim($combinationView, ' - ');
				foreach ($categoryArray as $item) {
					$catFind = '';
					if ($item) {
						$catFind = $category->c_all_list('', $item);
						if(!$catFind){
							$endCatID = $item;
						}
					}
				}
				$data['arr']['topCategoryFind'] = array_reverse($category->c_top_all_list('', $endCatID));
				$endCategoryFind = $category->c_one(["id" =>$endCatID]);

				$brand = $row->b_title;
				$colorTitle = $color_title;
				$sizeTitle = $size_title;
				
				$singlePrice = $veriable->price / $veriable->piece;

				if ($veriable->vat_rate) {
					$vatRate[$veriable->vat_rate] = $vatRate[$veriable->vat_rate] + vat_add(($veriable->price), $veriable->vat_rate);
					$totalVatRate = $totalVatRate + vat_add(($veriable->price), $veriable->vat_rate);
				}

				if ($productPicture) {
					foreach ($productPicture as $item) {
						if (file_exists('uploads/products/min/'.$item->image.'') && $item->image){
							$image = base_url('/uploads/products/min/'.$item->image);
						}else{
							$image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
						}
						
					}
				}else{
					$image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
				}


				if ($productCombinationOne->title) {
					$title = $productCombinationOne->title;
				}else{
					$title = $row->title;
				}

				$productOrderDetailArea .= '
					<table class="row"
						style="border-collapse: collapse; border-spacing: 0; width: 100%">
						<tr class="order-list__item order-list__item--single"
							style="border-bottom-color: #e5e5e5; border-bottom-style: none !important; border-bottom-width: 1px; width: 100%">
							<td class="order-list__item__cell"
								style="padding: 0;">
								<table style="border-collapse: collapse; border-spacing: 0">
									<td
										style="">
										<img src="'.$image.'"
											align="left" width="60" height="60"
											class="order-list__product-image"
											style="border: 1px solid #e5e5e5; border-radius: 8px; margin-right: 15px">
									</td>
									<td class="order-list__product-description-cell"
										style=" width: 100%;">
										<span class="order-list__item-title"style="color: #555; font-size: 16px; font-weight: 600; line-height: 1.4">'. $title .' × '. $veriable->piece .'</span>
										<br>
										'.$combinationView . '
									</td>
									<td class="order-list__price-cell"
										style=" white-space: nowrap;">
										<p class="order-list__item-price"
											style="color: #555; font-size: 16px; font-weight: 600; line-height: 150%; margin: 0 0 0 15px; text-align: right"
											align="right">'. number_format($singlePrice, 2) .' TL</p>
									</td>
								</table>
							</td>
						</tr>
					</table>
				';
			}

			foreach ($vatRate as $key => $item) {
				$vatPriceArea .= '
					<tr class="subtotal-line">
						<td class="subtotal-line__title"
							style=" padding: 5px 0;">
							<p
								style="color: #777; font-size: 16px; line-height: 1.2em; margin: 0">
								<span style="font-size: 16px">KDV ('.$key.'%)</span>
							</p>
						</td>
						<td class="subtotal-line__value"
							style=" padding: 5px 0; text-align: right;"
							align="right"> <strong
								style="color: #555; font-size: 16px">'. number_format($item, 2) .' TL</strong>

						</td>
					</tr>
				';
			}

			$delivery_address = $addressModels->c_one('order_address_clone', ['id' => $thisProductOrderFind->shipping_address]);
			$town = $addressModels->c_one('town', ['TownID' => $delivery_address->user_town], 'TownName ASC');
			$city = $addressModels->c_one('city', ['CityID' => $delivery_address->user_city], 'CityName ASC');
			$neighborhood = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $delivery_address->user_neighborhood], 'NeighborhoodName ASC');

			$billing_address = $addressModels->c_one('order_address_clone', ['id' => $thisProductOrderFind->billing_address]);

			$townBilling = $addressModels->c_one('town', ['TownID' => $billing_address->user_town], 'TownName ASC');
			$cityBilling = $addressModels->c_one('city', ['CityID' => $billing_address->user_city], 'CityName ASC');
			$neighborhoodBilling = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $billing_address->user_neighborhood], 'NeighborhoodName ASC');

			$delivery_options = $orderModels->delivery_c_one(['id' => $thisProductOrderFind->shipping_id]);
			/*
				$url = base_url().'/api/nebimOrderInsert/'.$thisProductOrderFind->user_id.'/'. $thisProductOrderFind->id .'?apiKey=953E1C7C06494141B8DF4BBBDE76ED5E';
				$curl = curl_init();

				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_POSTFIELDS =>'{"query":"","variables":{}}',
					CURLOPT_HTTPHEADER => array(
						'ParentId: 549',
						'Content-Type: application/json',
						'Cookie: ci_session=n6dos362hag1gj3g4j9borqj9pnjo14g'
					),
				));
				$response = curl_exec($curl);
				curl_close($curl);
			*/
			$sendMail = new SendMail();
			$orderFind = $orderModels->c_one(['id' => $thisProductOrderFind->id]);
			
			$orderNebimFind = $orderModels->orderDetailNebimAll(['order_id' => $thisProductOrderFind->id], '', '');

			if (!$orderFind->nebim_OrderNumber) {
				$nebimCustomerData = [
					"ModelType" => '6',
					"CustomerCode" => "".$userCheackOrder->nebim_CurrAccCode."",
					"StoreCode" => "S008",
					"PosTerminalID" => "1",
					"WarehouseCode" => "S008",
					"ShipmentMethodCode" => "2",
					"DeliveryCompanyCode" => "001",
					"IsSalesViaInternet" => true,
					"DocumentNumber" => "". $orderFind->order_no ."",
					"Description" => "". $orderFind->order_note ."",
					"BillingPostalAddressID" => "". $billing_address->nebim_PostalAddressID ."",
					"ShippingPostalAddressID" => "". $delivery_address->nebim_PostalAddressID ."",
					"IsCompleted" => true,
					"Payments" => [
						[
							"PaymentType" => "2",
							"Code" => "",
							"CreditCardTypeCode" => "IYZ",
							"InstallmentCount" => "1",
							"CurrencyCode" => "TRY",
							"Amount" =>  floatval($orderFind->total_price),
						]
					],
					"OrdersViaInternetInfo" => [
						"SalesURL" => base_url().'/',
						"PaymentTypeCode" => 1,
						"PaymentTypeDescription" => "KREDIKARTI/BANKAKARTI",
						"PaymentAgent" => "Iyzoco",
						"PaymentDate" => "". nowDate() ."T". nowTime() ."Z",
						"SendDate" => "". nowDate() ."T". nowTime() ."Z",
					],    
				];
				$orderDetailFind = $orderModels->orderDetailAll(['order_id' => $thisProductOrderFind->id]);
				foreach ($orderDetailFind as $key => $row) {
					helper('text');
					for ($i = 1; $i <= $row->piece; $i++) {
						$LineID = "". random_string('crypto', 8) ."-". random_string('crypto', 4) ."-". random_string('crypto', 4) ."-". random_string('crypto', 4) ."-". random_string('crypto', 12) ."";
						$nebimCustomerData['Lines'][]  = [
							"LineID" => "". $LineID ."",
							"UsedBarcode" => "". $row->variant_barcode ."",
							"Qty1" => $row->piece,
							"PriceVI" => ($row->price / $row->piece),
						];
						if (!$orderNebimFind) {
							$updateOrderDetailDate = [
								"order_id" => $row->order_id,
								"product_order_no" => $row->product_order_no,
								"nebim_line_id" => '',
								"iyziko_payment_transaction_id" => $row->iyziko_payment_transaction_id,
								"request_no" => $row->request_no,
								"user_id" => $row->user_id,
								"product_id" => $row->product_id,
								"variant_id" => $row->variant_id,
								"variant_barcode" => $row->variant_barcode,
								"vat_rate" => $row->vat_rate,
								"price" => $row->price / $row->piece,
								"piece" => 1,
								"coupon_id" => $row->coupon_id,
								"coupon_discount_type" => $row->coupon_discount_type,
								"coupon_discount" => $row->coupon_discount,
								"coupon_rate" => $row->coupon_rate,
								"color_id" => $row->color_id,
								"size_id" => $row->size_id,
								"status" => $row->status,
								"created_at" => $row->created_at,
								"updated_at" => created_at()
							];
							$updateOrder = $orderModels->orderDetailNebimAdd($updateOrderDetailDate);
						}
					}
				}
				$nebimCustomerData['Lines'][]  = [
					"ItemTypeCode" => "5",
					"ItemCode" => "KRG",
					"Qty1" => "1",
					"PriceVI" => floatval($orderFind->shipping_price),
				];
				$updateOrderData = [
					'nebim_json' => json_encode($nebimCustomerData),
					"updated_at" => created_at()
				];
				$insertOrder = $orderModels->editOrderNo($thisProductOrderFind->order_no, $updateOrderData);

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
																<h2 style="font-size: 24px; font-weight: normal; margin: 0 0 10px">Biltstore Nebim Hatalı Sipariş</h2>

																<p style="color: #777; font-size: 16px; line-height: 150%; margin: 0">'. $thisProductOrderFind->order_no .' nolu sipariş nebim tarafına iletilememiştir.</p>
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
																						<a href="'. base_url('/panel/order/detail/'. $thisProductOrderFind->id .'') .'" class="button__text"
																							style="color: #fff; font-size: 16px; text-decoration: none">Siparişe Göz At</a>
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
								</td>
							</tr>
						</table>
					</body>
					</html>
					</body>
					</html>
				';
				$registerMail = $sendMail->SendMail($contact['biltstore_error_email']->value, $contact['biltstore_info_email_general']->value, "", "Bir Sipariş Nebime İletilemedi.", $mailContent);
			}
			
			$shipping_price = $thisProductOrderFind->shipping_price ? number_format($thisProductOrderFind->shipping_price, 2) .' TL' : '<b style="color:#28bb74;">Ücretsiz Kargo</b>';
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
																<td class="order-number__cell"
																	style="color: #999; font-size: 14px; text-align: right; text-transform: uppercase"
																	align="right"> <span class="order-number__text"
																		style="font-size: 16px">
																		Sipariş No : '. $sesion['order']['order_no'] .'
																	</span>

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
										style="padding-bottom: 40px;">
										<center>
											<table class="container"
												style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
												<tr>
													<td
														style="">
														<h2 style="font-size: 24px; font-weight: normal; margin: 0 0 10px">Satın aldığınız için teşekkür ederiz.</h2>

														<p style="color: #777; font-size: 16px; line-height: 150%; margin: 0">Merhaba '. $userCheackOrder->name .', siparişinizi gönderilmeye hazırlıyoruz. Gönderildiğinde sizi bilgilendireceğiz.</p>
														<table class="row actions"
															style="border-collapse: collapse; border-spacing: 0; margin-top: 20px; width: 100%">
															<tr>
																<td class="actions__cell"
																	style="">
																	<table class="button main-action-cell"
																		style="border-collapse: collapse; border-spacing: 0; float: left; margin-right: 15px">
																		<tr>
																			<td class="button__cell" style="background: #1f1f1f; border-radius: 4px; padding: 20px 25px; text-align: center;"
																				align="center" bgcolor="#1f1f1f">
																				<a href="'. base_url('/hesabim/detay/'. $thisProductOrderFind->order_no .'') .'" class="button__text"
																					style="color: #fff; font-size: 16px; text-decoration: none">Sipariş Detayı</a>
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
							<table class="row section"
								style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
								<tr>
									<td class="section__cell"
										style="padding: 40px 0;">
										<center>
											<table class="container"
												style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
												<tr>
													<td
														style="">
														<h3 style="font-size: 20px; font-weight: normal; margin: 0 0 25px">Sipariş Detayları
														</h3>

													</td>
												</tr>
											</table>
											<table class="container"
												style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
												<tr>
													<td
														style="">
														'. $productOrderDetailArea .'
														<table class="row subtotal-lines"
															style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; margin-top: 15px; width: 100%">
															<tr>
																<td class="subtotal-spacer"
																	style=" width: 40%;">
																</td>
																<td
																	style="">
																	<table class="row subtotal-table subtotal-table--total"
																		style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 2px; margin-top: 20px; width: 100%">

																		<tr class="subtotal-line">
																			<td class="subtotal-line__title"
																				style=" padding: 20px 0 0;">
																				<p
																					style="color: #777; font-size: 16px; line-height: 1.2em; margin: 0">
																					<span style="font-size: 16px">Ara Toplam</span>
																				</p>
																			</td>
																			<td class="subtotal-line__value"
																				style=" padding: 5px 0; text-align: right;"
																				align="right"> <strong
																					style="color: #555; font-size: 16px">'. number_format($thisProductOrderFind->overall_total, 2) .' TL</strong>
																			</td>
																		</tr>

																		
																	</table>
																	<table class="row subtotal-table subtotal-table--total"
																		style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 2px; margin-top: 20px; width: 100%">

																		<tr class="subtotal-line">
																			<td class="subtotal-line__title"
																				style=" padding: 20px 0 0;">
																				<p
																					style="color: #777; font-size: 16px; line-height: 1.2em; margin: 0">
																					<span style="font-size: 16px">Kargo Ücreti</span>
																				</p>
																			</td>
																			<td class="subtotal-line__value"
																				style=" padding: 5px 0; text-align: right;"
																				align="right"> <strong
																					style="color: #555; font-size: 16px">'. $shipping_price .' </strong>
																			</td>
																		</tr>

																		
																	</table>
																	
																	<table class="row subtotal-table subtotal-table--total"
																		style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 2px; margin-top: 20px; width: 100%">
																		'. $vatPriceArea .'
																	</table>
																	<table class="row subtotal-table subtotal-table--total"
																		style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 2px; margin-top: 20px; width: 100%">
																		<tr class="subtotal-line">
																			<td class="subtotal-line__title"
																				style=" padding: 20px 0 0;">
																				<p
																					style="color: #777; font-size: 16px; line-height: 1.2em; margin: 0">
																					<span style="font-size: 16px">Toplam Ödenen Tutar</span>
																				</p>
																			</td>
																			<td class="subtotal-line__value"
																				style=" padding: 20px 0 0; text-align: right;"
																				align="right">
																				<strong style="color: #555; font-size: 24px">'. number_format($thisProductOrderFind->total_price, 2) .' TL</strong>

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
							<table class="row section"
								style="border-collapse: collapse; border-spacing: 0; border-top-color: #e5e5e5; border-top-style: solid; border-top-width: 1px; width: 100%">
								<tr>
									<td class="section__cell"
										style="padding: 40px 0;">
										<center>
											<table class="container"
												style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
												<tr>
													<td
														style="">
														<h3 style="font-size: 20px; font-weight: normal; margin: 0 0 25px">Teslimat Fatura Bilgileri</h3>

													</td>
												</tr>
											</table>
											<table class="container"
												style="border-collapse: collapse; border-spacing: 0; margin: 0 auto; text-align: left; width: 560px">
												<tr>
													<td
														style="">
														<table class="row"
															style="border-collapse: collapse; border-spacing: 0; width: 100%">
															<tr>
																<td class="customer-info__item"
																	style="padding-bottom: 40px; width: 50%;">
																	<h4
																		style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
																		Teslimat Adresi</h4>

																	<p
																		style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
																		'. $delivery_address->title .'
																		<br>'. $delivery_address->receiver_name .'
																		<br>'. $delivery_address->address .'
																		<br>
																		<br>'. $neighborhood->NeighborhoodName .', '. $town->TownName .'/'. $city->CityName .'</p>
																</td>
																<td class="customer-info__item"
																	style=" padding-bottom: 40px; width: 50%;">
																	<h4
																		style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
																		Fatura Adresi</h4>

																	<p
																		style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
																		'. $billing_address->title .'
																		<br>'. $billing_address->receiver_name .'
																		<br>'. $billing_address->address .'
																		<br>
																		<br>'. $neighborhoodBilling->NeighborhoodName .', '. $townBilling->TownName .'/'. $cityBilling->CityName .'</p>
																</td>
															</tr>
														</table>
														<table class="row"
															style="border-collapse: collapse; border-spacing: 0; width: 100%">
															<tr>
																<td class="customer-info__item"
																	style="padding-bottom: 40px; width: 50%; ">
																	<h4
																		style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
																		Teslimat Tipi</h4>

																	<p
																		style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
																		'. $delivery_options->title .'</p>
																</td>
																<td class="customer-info__item"
																	style=" padding-bottom: 40px; width: 50%;">
																	<h4
																		style="color: #555; font-size: 16px; font-weight: 500; margin: 0 0 5px">
																		Ödeme Durumu</h4>

																	<p class="customer-info__item-content"
																		style="color: #777; font-size: 16px; line-height: 150%; margin: 0">
																		<span style="font-size: 16px">Online Kredi/Banka Kartı <strong
																		style="color: #555; font-size: 16px">'. number_format($thisProductOrderFind->total_price, 2) .' TL</strong></span>
																	</p>
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
										style=" padding: 35px 0;">
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
			';
			$registerMail = $sendMail->SendMailOrder($userCheackOrder->email, '', [$contact['biltstore_info_email']->value, $contact['biltstore_info_email_general']->value], "Yeni siparişiniz Alındı.", $mailContent, ['file_location' => base_url('/uploads/orders/'.$sesion['order']['order_no'].'/mesafeli_satis_sozlesmesi.pdf'), 'file_name' => 'Mesafeli Satis Sözlesmesi.pdf']);
			getLogDate($this->data['user_id'], '37', $order['order_no'], 'Sipariş Numarası', '');
			unset($_SESSION['order']);
			$basketModels->deleteRow(['user_id' => $this->data['user_id']]);
		}
		$this->viewLoad('checkout-status', $data);
	}

	public function sessionCheckoutProduct ($par = '') {
		
		$db =  db_connect();
		$this->session = \Config\Services::session();
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$campaignModels = new CampaignModels($db);
		$orderModels = new OrderModels($db);
		$delivery_options_first = $orderModels->delivery_c_one(['is_default' => '1', 'is_active' => '1']);
		$order_product = $this->session->get('order.product');
		if (!$order_product) {
			return redirect()->route('sepetim');
		}
		foreach ($order_product as $row) {
			$thisProductFind = $productDetailModels->c_one(['p.id' => $row['id'], 'p.is_active' => '1']);
			if ($row['variant_id']) {
				$thisVariantFind = $productDetailModels->productCombinationOne(['pa.id_product' => $row['id'], 'pa.is_active' => '1', 'pa.id' => $row['variant_id']]);
			}
			
			$productPicture = $productDetailModels->attributePictureAll(['pap.product_attribute_id' =>$thisVariantFind->id], 1);
            if (!$productPicture) {
                $productPicture = $productDetailModels->c_all_image(['product_id' => $row['id']], 1);
            }
			if ($productPicture) {
                foreach ($productPicture as $item) {
                    if (file_exists('uploads/products/min/'.$item->image.'') && $item->image){
                        $image = base_url('/uploads/products/min/'.$item->image);
                    }else{
                        $image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
                    }
                }
            }else{
                $image = base_url('/uploads/products/no_image/bilt_no_product_500x750.png');
            }

			if ($thisProductFind->campaign_id) {
				$productCampaign = $campaignModels->c_one_index(['id' => $thisProductFind->campaign_id, 'is_active' => '1']);
			}

			//////////////////// Product Price Select Area Start ///////////////////
				$discountBool = FALSE;
				$discountRate = '0.00';
				$totalPrice = '';
				$discountPrice = '';
				$basketPrice = '';
				$last_price = '';
				
				if ($thisVariantFind->sale_price) {
					$priceArray = priceAreaFunction($thisVariantFind->sale_price, $thisVariantFind->discount_price, $thisVariantFind->basket_price, $productCampaign->discount);
				}else{
					$priceArray = priceAreaFunction($thisProductFind->sale_price, $thisProductFind->discount_price, $thisProductFind->basket_price, $productCampaign->discount);
				}

				if ($priceArray['basketPrice']) {
					$last_price = $priceArray['basketPrice'];
				}elseif ($priceArray['discountPrice']) {
					$last_price = $priceArray['discountPrice'];
				}elseif ($priceArray['totalPrice']){
					$last_price = $priceArray['totalPrice'];
				}

			/////////////////// Product Price Select Area End ///////////////////
			
			//////////////////// Product Title Select Area Start ///////////////////
				$featureArray = explode(' - ', $thisProductFind->attr);
				$categoryArray = explode(',', $thisProductFind->category_id);
				foreach ($categoryArray as $item) {
					$catFind = '';
					if ($item) {
						$catFind = $category->c_all_list('', $item);
						if(!$catFind){
							$endCatID = $item;
						}
					}
				}
				$endCategoryFind = $category->c_one(["id" =>$endCatID]);
				$title = $thisProductFind->title;
				$link = $thisProductFind->slug.'-p-'.$thisVariantFind->barcode_no;
			//////////////////// Product Title Select Area End ///////////////////
			if ($par == '1') {
				$setPriceArea = ($row['last_price'] * $row['piece']) - $row['price_coupon'];
				$productDataView = [
					'product_id' => $row['id'],
					'variant_id' => $row['variant_id'],
					'image' => $image,
					'title' => $title,
					'link' => $link,
					'categories' => $featureArray['1'] . ' ' . $endCategoryFind->title,
					'size_title' => $thisVariantFind->attr_size,
					'color_title' => $thisVariantFind->attr_color,
					'max_stock' => $thisVariantFind->stock,
					'piece' => $row['piece'],
					'vat_rate' => $row['vat_rate'],
					'coupon_rate' => $row['coupon_rate'],
					'coupon_discount' => $row['coupon_discount'],
					'coupon_discount_type' => $row['coupon_discount_type'],
					'total_price' => $priceArray['totalPrice'] * $row['piece'],
					'discount_price' => $priceArray['discountPrice'] * $row['piece'],
					'basket_price' => $priceArray['basketPrice'] * $row['piece'],
					'basketRate' => $priceArray['basketRate'],
					'last_price' => $row['last_price'],
					'set_price' => $setPriceArea,
					'price_coupon' => $row['price_coupon'],
				]; 
			}else{
				$productDataView = [
					'product_id' => $row['id'],
					'variant_id' => $row['variant_id'],
					'image' => $image,
					'title' => $title,
					'link' => $link,
					'categories' => $featureArray['1'] . ' ' . $endCategoryFind->title,
					'size_title' => $thisVariantFind->attr_size,
					'color_title' => $thisVariantFind->attr_color,
					'max_stock' => $thisVariantFind->stock,
					'piece' => $row['piece'],
					'vat_rate' => $row['vat_rate'],
					'coupon_rate' => $row['coupon_rate'],
					'coupon_discount' => $row['coupon_discount'],
					'coupon_discount_type' => $row['coupon_discount_type'],
					'total_price' => $priceArray['totalPrice'] * $row['piece'],
					'discount_price' => $priceArray['discountPrice'] * $row['piece'],
					'basket_price' => $priceArray['basketPrice'] * $row['piece'],
					'basketRate' => $priceArray['basketRate'],
					'last_price' => $row['last_price'],
					'price_coupon' => $row['price_coupon'],
				]; 
			}
			foreach ($row['combanition'] as $key => $value) {
				$productDataView['combanition'][$key]['id'] = $value['id'];
				$productDataView['combanition'][$key]['title'] = $value['title'];
				$productDataView['combanition'][$key]['group_title'] = $value['group_title'];
				$productDataView['combanition'][$key]['group_id'] = $value['group_id'];
			}
			$data[$row['variant_id'] ? $row['variant_id'] : $row['id']] = $productDataView;
			$headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
			$basketTotalPrice += $row['last_price'] * $row['piece'];
		}

		foreach ($_SESSION['order']['product'] as $row) {
			if ($row['coupon_id']) {
				$totalDiscountPrice = $totalDiscountPrice + $row['last_price'] * $row['piece'];
			}
		}

		if ($totalDiscountPrice) {
			if ($_SESSION['order']['coupon_discount_type'] == '1') {
				$coupon_discount = fiyatHesaplamaPlus($totalDiscountPrice, $_SESSION['order']['coupon_discount_rate']) - $totalDiscountPrice;
				$coupon_discount_total = $coupon_discount;
			}else{
				if ($_SESSION['order']['coupon_discount_type'] == '2' && $_SESSION['order']['coupon_discount_min'] < $basketTotalPrice) {
					if ($totalDiscountPrice < $_SESSION['order']['coupon_discount_rate']) {
						$coupon_discount = $totalDiscountPrice;
					}else{
						$coupon_discount = $_SESSION['order']['coupon_discount_rate'];
					}
					$basketTotalPriceNew = $basketTotalPrice - $coupon_discount;
					$coupon_discount = $basketTotalPrice - $basketTotalPriceNew;
					$coupon_discount_total = $coupon_discount;
					$basketTotalPrice = $basketTotalPriceNew;
				}
			}
		}

		if ($delivery_options_first->free_shipping_price >= $basketTotalPrice) {
			$basketTotalPrice = $basketTotalPrice + $delivery_options_first->shipping_price;
			$headerBasketPriceSesion = $headerBasketPriceSesion;
			$this->session->push('order', ['shipping_price' => $delivery_options_first->shipping_price]);
		}else{
			unset($_SESSION['order']['shipping_price']);
		}
		
		$this->session->push('order', ['header_basket_price' => $headerBasketPriceSesion]);
		$this->session->push('order', ['basket_total_price' => $basketTotalPrice]);
		return $data;
	}

	public function orderStart()
	{
        $db =  db_connect();
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$orderModels = new OrderModels($db);

		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_product = $this->session->get('order.product');

		if (!$order['order_no']) {
			$hash = crc32(sha1(base64_encode(md5(base64_encode(created_at())))));
			if (strlen(date('n')) == '1' ) {
				$order_no = date('y') . date('n') . substr($hash, 1, 8);
			}else{
				$order_no = date('y') . date('n') . substr($hash, 1, 7); 
			} 
			$this->session->set('order', ["order_no" => $order_no]);
		}else{
			$order_no = $order['order_no'];
		}
		if ($this->data['user_id']) {
            $user_id = $this->data['user_id'];
        }
		$thisProductOrderFind = $orderModels->c_one(['order_no' => $order_no]);

		if (!$order_product) {
			$data['error'] = 'Sepetinizde ürün bulunamadı.';
		}else{
			if ($user_id) {
				$this->sessionCheckoutProduct();
			}else{
				$this->sessionCheckoutProduct();
			}
		}
		echo json_encode($data);
	}

	public function checkoutStepOne()
	{
		$data = $this->data;
		$this->session = \Config\Services::session();
		$routes = \Config\Services::routes();
		$db =  db_connect();
		$basket = new Basket($db);
		$category = new Category($db);
		$productFilterModels = new ProductFilterModels($db);
		$productDetailModels = new ProductDetailModels($db);
		$attributeGroupModels = new AttributeGroupModels($db);
		$orderModels = new OrderModels($db);
		if ($this->data['user_id']) {
			$data = $this->userCheckoutStepOne();
        }else{
			$data = $this->sessionCheckoutStepOne();
		}
		echo json_encode($data);
	}

	public function sessionCheckoutStepOne () {
		$db =  db_connect();
		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_no = $order['order_no'];
		$order_product = $this->session->get('order.product');
		$orderModels = new OrderModels($db);
		$addressModels = new AddressModels($db);
		$userModels = new UserModels($db);
		$validation =  \Config\Services::validation();

		$email = $this->request->getPost('email');
        $receiver_name = $this->request->getPost('receiver_name');
        $phone = $this->request->getPost('phone');
        $title = $this->request->getPost('title');
        $city = $this->request->getPost('city');
        $town = $this->request->getPost('town');
        $neighborhood = $this->request->getPost('neighborhood');
        $address = $this->request->getPost('address');
        $shipping = $this->request->getPost('shipping');
        $note = $this->request->getPost('note');

		$userCheack = $userModels->c_one(['email' => $email, 'is_active' => '1', 'is_guest' => '0' ]);
		$cityCheack = $addressModels->c_one('city', ['CountryID' => '212', 'CityID' => $city]);
		$townCheack = $addressModels->c_one('town', ['TownID' => $town]);
		$neighborhoodCheack = $addressModels->c_one('neighborhood', ['NeighborhoodID' => $neighborhood]);
		$shippingCheack = $orderModels->delivery_c_one(['id' => $shipping]);

		if ($userCheack) {
			$data['error'] = 'Girilen E-Posta adresine ait kayıtlı bir kullanıcı bulunmaktadır.';
		}elseif (!$email || !$receiver_name || !$phone || !$title || !$city || !$town || !$neighborhood || !$address|| !$shipping) {
			$data['error'] = 'Lütfen gerekli tüm alanları doldurunuz.';	
		}elseif (!$validation->check($email, 'valid_email')) {
            $data['error'] =  'Girilen email adresi hatalıdır. Lütfen doğru bir formatta giriniz.';
        }elseif (!$cityCheack) {
			$data['error'] = 'Seçtiğiniz il bulunamadı.';	
		}elseif (!$townCheack) {
			$data['error'] = 'Seçtiğiniz ilçe bulunamadı.';	
		}elseif (!$neighborhoodCheack) {
			$data['error'] = 'Seçtiğiniz mahalle bulunamadı.';	
		}elseif (!$shippingCheack) {
			$data['error'] = 'Seçmiş oldugunuz teslimak seçeneği bulunamadı.';	
		}else {
			$userCheackOrder = $userModels->c_one(['email' => $email, 'is_active' => '1', 'is_guest' => '1' ]);
			if (!$userCheackOrder) {
				$insertUserDate = [
					'name' => $receiver_name,
					'surname' => $receiver_name,
					'full_name' => $receiver_name,
					'email' => $email,
					'phone' => $phone,
					'is_active' => '1',
					'is_guest' => '1',
					'created_at' => created_at(),
				];
				$userInsert = $userModels->add($insertUserDate);
				$userID = $db->insertID();
			}else{
				$userID = $userCheackOrder->id;
			}
			$insertOrder = $addressModels->deleteRow('order_address_clone', ['order_no' => $order['order_no']]);
			$insertUserAddressDate = [
				'user_id' => $userID,
				'title' => $title,
				'receiver_name' => $receiver_name,
				'user_town' => $town,
				'user_city' => $city,
				'user_neighborhood' => $neighborhood,
				'address' => $address,
				'phone' => $phone,
				'email' => $email,
				'created_at' => created_at()
			];
			$insertUserAddress = $addressModels->add('order_address_clone', $insertUserAddressDate);
			$userAddressID = $db->insertID();
			$thisProductOrderFind = $orderModels->c_one(['order_no' => $order['order_no']]);
			if (!$thisProductOrderFind) {
				if ($order['basket_total_price'] < $shippingCheack->free_shipping_price) {
					$shipping_price = $shippingCheack->shipping_price;
				}else {
					$shipping_price = '0';
				}
				$insertOrderData = [
					"user_id" => $userID,
					"order_no" => $order['order_no'],
					"shipping_id" => $shipping,
					"shipping_price" => $shipping_price,
					"shipping_address" => $userAddressID,
					"billing_address" => $userAddressID,
					"order_note" => $note,
					"status" => '99',
					"created_at" => created_at(),
				];
				$insertOrder = $orderModels->add($insertOrderData);
				$orderID = $db->insertID();
			}else{
				$updateOrderData = [
					"user_id" => $userID,
					"shipping_id" => $shipping,
					"shipping_price" => $shipping_price,
					"shipping_address" => $userAddressID,
					"billing_address" => $userAddressID,
					"order_note" => $note,
					"status" => '99',
					"updated_at" => created_at(),
				];
				$insertOrder = $orderModels->edit($thisProductOrderFind->id, $updateOrderData);
				$orderID = $thisProductOrderFind->id;
			}

			if ($orderID) {
				$thisProductOrderDetailDelete = $orderModels->orderDetailDelete(['product_order_no' => $order_no]);
				foreach ($order_product as $row) {
					if ($row['variant_id']) {
						$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'product_id' => $row['id'], 'variant_id' => $row['variant_id']]);
					}else{
						$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'product_id' => $row['id']]);
					}
					if (!$thisProductOrderDetailFind) {
						$orderDetailData = [
							'order_id' => $orderID,
							'product_order_no' => $order_no,
							'user_id' => $userID,
							'product_id' => $row['id'],
							'variant_id' => $row['variant_id'],
							'variant_barcode' => $row['variant_barcode'],
							'vat_rate' => $row['vat_rate'],
							'price' => $row['last_price'] * $row['piece'],
							'piece' => $row['piece'],
							'color_id' => $row['color_id'],
							'size_id' => $row['size_id'],
						];
						$insertOrderDetail = $orderModels->orderDetailAdd($orderDetailData);
					}else{
						$orderDetailData = [
							'order_id' => $orderID,
							'product_order_no' => $order_no,
							'user_id' => $user_id,
							'product_id' => $row['id'],
							'variant_id' => $row['variant_id'],
							'variant_barcode' => $row['variant_barcode'],
							'vat_rate' => $row['vat_rate'],
							'price' => $row['last_price'] * $row['piece'],
							'piece' => $row['piece'],
							'color_id' => $row['color_id'],
							'size_id' => $row['size_id'],
						];
						$insertOrderDetail = $orderModels->orderDetailUpdate($thisProductOrderDetailFind->id, $orderDetailData);
					}
					$headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
					$overall_total += vat_deducted(($row['last_price'] * $row['piece']), $row['vat_rate']);
					$vat_price += ($row['last_price'] * $row['piece']) - vat_deducted(($row['last_price'] * $row['piece']), $row['vat_rate']);
					$basketTotalPrice += $row['last_price'] * $row['piece'];
				}
				$totalPrice = $basketTotalPrice + $shipping_price;
				$updateOrderData = [
					"overall_total" => $overall_total,
					"vat_price" => $vat_price,
					"total_price" => $totalPrice,
					"updated_at" => created_at(),
				];
				$insertOrder = $orderModels->edit($orderID, $updateOrderData);

				$updateOrderAddressData = [
					"order_id" => $orderID,
					"order_no" => $order['order_no'],
					"updated_at" => created_at(),
				];
				$insertOrder = $addressModels->edit('order_address_clone', ['id' => $userAddressID], $updateOrderAddressData);

				$this->session->push('order', ['order_user' => $userID]);
				$data['success'] = 'Ödeme Sayfası';
			}else{
				$data['error'] = 'Beklenmeyen bir hata oluştu.';
			}
		}
		return $data;
	}

	public function userCheckoutStepOne () {
		$db =  db_connect();
		$this->session = \Config\Services::session();
		$order = $this->session->get('order');
		$order_no = $order['order_no'];
		$order_product = $this->session->get('order.product');
		$orderModels = new OrderModels($db);
		$addressModels = new AddressModels($db);
		$userModels = new UserModels($db);
		$validation =  \Config\Services::validation();
		$user_id = $this->data['user_id'];

        $billing_type = $this->request->getPost('billing_type');
        $address = $this->request->getPost('address');
        $billing_address = $this->request->getPost('billing_address');
        $shipping = $this->request->getPost('shipping');

		$userCheack = $userModels->c_one(['id' => $this->data['user_id'], 'is_active' => '1', 'is_guest' => '0' ]);
		$shippingCheack = $orderModels->delivery_c_one(['id' => $shipping]);
		$userAddressCheack = $addressModels->c_one('user_address', ['id' => $address, 'user_id' => $user_id]);
		if ($billing_type == '1') {
			$userBillingAddressCheack = $addressModels->c_one('user_address', ['id' => $billing_address, 'user_id' => $user_id, 'address_type' => 'billing' ]);
		}else{
			$userBillingAddressCheack = $addressModels->c_one('user_address', ['id' => $address, 'user_id' => $user_id]);
		}

		if (!$userCheack) {
			$data['error'] =  'İşlemi gerçekleştirmek için lütfen giriş yapınız.';
			$data['location'] =  'giris-yap';
		}elseif (!$userAddressCheack) {
			$data['error'] = 'Seçmiş oldugunuz teslimak adresi bulunamadı.';	
		}elseif (!$shippingCheack) {
			$data['error'] = 'Seçmiş oldugunuz teslimak seçeneği bulunamadı.';	
		}elseif (!$userBillingAddressCheack) {
			$data['error'] = 'Seçmiş oldugunuz fatura adresiniz bulunamadı.';	
		}else {
			$userID = $this->data['user_id'];
			$userAddressID = $address;
			$thisProductOrderFind = $orderModels->c_one(['order_no' => $order['order_no']]);
			$insertOrder = $addressModels->deleteRow('order_address_clone', ['user_id' => $userID, 'order_no' => $order['order_no']]);
			$insertUserAddressDate = [
				'user_id' => $userID,
				'nebim_PostalAddressID' => $userAddressCheack->nebim_PostalAddressID,
				'title' => $userAddressCheack->title,
				'receiver_name' => $userAddressCheack->receiver_name,
				'user_town' => $userAddressCheack->user_town,
				'user_city' => $userAddressCheack->user_city,
				'user_neighborhood' => $userAddressCheack->user_neighborhood,
				'address' => $userAddressCheack->address,
				'identification_number' => $userAddressCheack->identification_number,
				'phone' => $userAddressCheack->phone,
				'email' => $userAddressCheack->email,
				'created_at' => created_at()
			];
			$insertUserAddress = $addressModels->add('order_address_clone', $insertUserAddressDate);
			$userAddressID = $db->insertID();

			$insertUserBillingAddressDate = [
				'user_id' => $userID,
				'nebim_PostalAddressID' => $userBillingAddressCheack->nebim_PostalAddressID,
				'title' => $userBillingAddressCheack->title,
				'receiver_name' => $userBillingAddressCheack->receiver_name,
				'user_town' => $userBillingAddressCheack->user_town,
				'user_city' => $userBillingAddressCheack->user_city,
				'user_neighborhood' => $userBillingAddressCheack->user_neighborhood,
				'address' => $userBillingAddressCheack->address,
				'phone' => $userBillingAddressCheack->phone,
				'email' => $userBillingAddressCheack->email,
				'address_type' => $userBillingAddressCheack->address_type,
				'billing_type' => $userBillingAddressCheack->billing_type,
				'identification_number' => $userBillingAddressCheack->identification_number,
				'tax_number' => $userBillingAddressCheack->tax_number,
				'tax_administration' => $userBillingAddressCheack->tax_administration,
				'created_at' => created_at()
			];
			$insertUserBillingAddress = $addressModels->add('order_address_clone', $insertUserBillingAddressDate);
			$userBillingAddressID = $db->insertID();
			if (floor(($order['basket_total_price'] * 100))/100 < $shippingCheack->free_shipping_price) {
				$shipping_price = $shippingCheack->shipping_price;
			}else {
				$shipping_price = '0';
			}
			if (!$thisProductOrderFind) {
				$insertOrderData = [
					"user_id" => $userID,
					"order_no" => $order['order_no'],
					"shipping_id" => $shipping,
					"shipping_price" => $shipping_price,
					"shipping_address" => $userAddressID,
					"billing_address" => $userBillingAddressID,
					"coupon_id" => $order['coupon_id'],
					"coupon_discount_type" => $order['coupon_discount_type'],
					"coupon_discount" => $order['coupon_discount'],
					"coupon_code" => $order['coupon_code'],
					"coupon_discount_rate" => $order['coupon_discount_rate'],
					"order_note" => $note,
					"status" => '99',
					"created_at" => created_at(),
				];
				$insertOrder = $orderModels->add($insertOrderData);
				$orderID = $db->insertID();
			}else{
				$updateOrderData = [
					"user_id" => $userID,
					"shipping_id" => $shipping,
					"shipping_price" => $shipping_price,
					"shipping_address" => $userAddressID,
					"billing_address" => $userBillingAddressID,
					"coupon_id" => $order['coupon_id'],
					"coupon_discount_type" => $order['coupon_discount_type'],
					"coupon_discount" => $order['coupon_discount'],
					"coupon_code" => $order['coupon_code'],
					"coupon_discount_rate" => $order['coupon_discount_rate'],
					"order_note" => $note,
					"status" => '99',
					"updated_at" => created_at(),
				];
				$insertOrder = $orderModels->edit($thisProductOrderFind->id, $updateOrderData);
				$orderID = $thisProductOrderFind->id;
			}

			if ($orderID) {
				$thisProductOrderDetailDelete = $orderModels->orderDetailDelete(['product_order_no' => $order_no]);
				$thisProductOrderDetailDelete = $orderModels->orderDetailCombinationDelete(['order_no' => $order_no]);
				foreach ($order_product as $row) {
					if ($row['variant_id']) {
						$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'product_id' => $row['id'], 'variant_id' => $row['variant_id']]);
					}else{
						$thisProductOrderDetailFind = $orderModels->orderDetailOne(['product_order_no' => $order_no, 'product_id' => $row['id']]);
					}
					if (!$thisProductOrderDetailFind) {
						$orderDetailData = [
							'order_id' => $orderID,
							'product_order_no' => $order_no,
							'user_id' => $userID,
							'product_id' => $row['id'],
							'variant_id' => $row['variant_id'],
							'variant_barcode' => $row['variant_barcode'],
							'vat_rate' => $row['vat_rate'],
							"coupon_id" => $row['coupon_id'],
							"coupon_discount_type" => $row['coupon_discount_type'],
							"coupon_discount" => $row['coupon_discount'],
							"coupon_rate" => $row['coupon_rate'],
							'price' => $row['last_price'] * $row['piece'] - $row['price_coupon'],
							'piece' => $row['piece'],
							'color_id' => $row['color_id'],
							'size_id' => $row['size_id'],
						];
						$insertOrderDetail = $orderModels->orderDetailAdd($orderDetailData);
					}else{
						$orderDetailData = [
							'order_id' => $orderID,
							'product_order_no' => $order_no,
							'user_id' => $user_id,
							'product_id' => $row['id'],
							'variant_id' => $row['variant_id'],
							'variant_barcode' => $row['variant_barcode'],
							'vat_rate' => $row['vat_rate'],
							"coupon_id" => $coupon_id,
							"coupon_discount_type" => $coupon_discount_type,
							"coupon_rate" => $coupon_rate,
							'price' => $row['last_price'] * $row['piece'] - $row['price_coupon'],
							'piece' => $row['piece'],
							'color_id' => $row['color_id'],
							'size_id' => $row['size_id'],
						];
						$insertOrderDetail = $orderModels->orderDetailUpdate($thisProductOrderDetailFind->id, $orderDetailData);
					}

					foreach ($row['combanition'] as $item) {
						$orderDetailCombinationData = [
							'order_id' => $orderID,
							'order_no' => $order_no,
							'user_id' => $user_id,
							'product_id' => $row['id'],
							'variant_id' => $row['variant_id'],
							'combination_id' => $item['id'],
							'combination_title' => $item['title'],
							'group_title' => $item['group_title'],
							'group_id' => $item['group_id'],
							'created_at' => created_at()
						];
						$insertOrderDetailCombination = $orderModels->orderDetailCombinationAdd($orderDetailCombinationData);
					}

					$headerBasketPriceSesion += $row['header_basket_price'] * $row['piece'];
					$overall_total += vat_deducted((($row['last_price'] * $row['piece']) - $row['price_coupon']), $row['vat_rate']);
					$vat_price += (($row['last_price'] * $row['piece']) - $row['price_coupon']) - vat_deducted((($row['last_price'] * $row['piece'] ) - $row['price_coupon']), $row['vat_rate']);
					$basketTotalPrice += ($row['last_price'] * $row['piece']);
				}
				
				foreach ($_SESSION['order']['product'] as $row) {
					if ($row['coupon_id']) {
						$totalDiscountPrice = $totalDiscountPrice + $row['last_price'] * $row['piece'];
					}
				}
	
				if ($totalDiscountPrice) {
					if ($_SESSION['order']['coupon_discount_type'] == '1') {
						$coupon_discount = fiyatHesaplamaPlus($totalDiscountPrice, $_SESSION['order']['coupon_discount_rate']) - $totalDiscountPrice;
						$coupon_discount_total = $coupon_discount;
					}else{
						if ($_SESSION['order']['coupon_discount_type'] == '2' && $_SESSION['order']['coupon_discount_min'] < $basketTotalPrice) {
							if ($totalDiscountPrice < $_SESSION['order']['coupon_discount_rate']) {
								$coupon_discount = $totalDiscountPrice;
							}else{
								$coupon_discount = $_SESSION['order']['coupon_discount_rate'];
							}
							$basketTotalPriceNew = $basketTotalPrice - $coupon_discount;
							$coupon_discount = $basketTotalPrice - $basketTotalPriceNew;
							$coupon_discount_total = $coupon_discount;
							$basketTotalPrice = $basketTotalPriceNew;
						}
					}
				}

				$totalPrice = $basketTotalPrice + $shipping_price;
				$updateOrderData = [
					"overall_total" => $overall_total,
					"vat_price" => $vat_price,
					"total_price" => $totalPrice,
					"updated_at" => created_at(),
				];
				$insertOrder = $orderModels->edit($orderID, $updateOrderData);

				$updateOrderAddressData = [
					"order_id" => $orderID,
					"order_no" => $order['order_no'],
					"updated_at" => created_at(),
				];
				$insertOrder = $addressModels->edit('order_address_clone', ['id' => $userAddressID], $updateOrderAddressData);
				$insertOrder = $addressModels->edit('order_address_clone', ['id' => $userBillingAddressID], $updateOrderAddressData);
				$this->session->push('order', ['order_user' => $userID]);
				$data['success'] = 'Ödeme Sayfası';
			}else{
				$data['error'] = 'Beklenmeyen bir hata oluştu.';
			}
		}
		return $data;
	}
	
}
