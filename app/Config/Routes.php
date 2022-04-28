<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->group('panel', function($routes)
{
	$routes->get('login', 'Admin\Login::index');
	$routes->get('logout', 'Admin\Login::logout');

}); 

$routes->group('api', ['filter' => 'AuthApiKey'], function($routes)
{
	$routes->get('nebimProductView', 'Api\Nebim::nebimProductView');
	$routes->get('nebimProductInsert', 'Api\Nebim::nebimProductInsert');
	$routes->get('nebimProductStockUpdate', 'Api\Nebim::nebimProductStockUpdate');
	$routes->get('nebimOrderInsert/(:num)/(:any)', 'Api\Nebim::nebimOrderInsert/$1/$2');
	
	$routes->get('nebimUserInsert/(:num)', 'Api\Nebim::nebimUserInsert/$1');
	$routes->get('nebimUserAddressInsert/(:num)/(:num)', 'Api\Nebim::nebimUserAddressInsert/$1/$2');
	$routes->get('nebimUserAddressUpdate/(:num)/(:num)', 'Api\Nebim::nebimUserAddressUpdate/$1/$2');
	$routes->get('nebimOrderDetailCanceled/(:num)/(:num)/(:num)', 'Api\Nebim::nebimOrderDetailCanceled/$1/$2/$3');

	$routes->get('orderCreateCargo/(:num)/(:num)/(:any)', 'Api\Cargo::orderCreateCargo/$1/$2/$3');
	$routes->get('cargoTracking/(:num)', 'Api\Cargo::cargoTracking/$1');

	$routes->get('productData', 'Api\Mubiko::nebimProductInsert');
	$routes->get('orderMubiko', 'Api\Mubiko::orderInsertMubiko');

	$routes->get('MailchimpApi', 'Api\MailchimpApi::MailchimpRun');

	$routes->get('searchProductInsert', 'Api\SearchApi::searchProductInsert');

	$routes->get('trendyolGetBrand', 'Api\TrendyolApi::getBrand');
	$routes->get('trendyolGetCategories', 'Api\TrendyolApi::getCategories');

	
}); 

$routes->group('panel', ['filter' => 'AuthLogin'], function($routes)
{
	$routes->get('', 'Admin\Dashboard::index');
	$routes->get('dashboard', 'Admin\Dashboard::index');

	$routes->group('categories', function($routes)
	{
		$routes->get('list', 'Admin\Categories::index');
		$routes->get('list/(:any)', 'Admin\Categories::index/$1');
		$routes->get('add', 'Admin\Categories::add');
		$routes->get('add/(:any)', 'Admin\Categories::add/$1');
		$routes->get('edit/(:num)', 'Admin\Categories::edit/$1');
		$routes->get('edit/(:num)/(:any)', 'Admin\Categories::edit/$1/$2');
	}); 

	$routes->group('attribute', function($routes)
	{
		$routes->get('group-list', 'Admin\AttributeGroup::index');
		$routes->get('group-add', 'Admin\AttributeGroup::add');
		$routes->get('group-edit/(:num)', 'Admin\AttributeGroup::edit/$1');

		$routes->get('list/(:num)', 'Admin\Attribute::index/$1');
		$routes->get('list/(:num)/(:num)', 'Admin\Attribute::index/$1/$2');
		$routes->get('add/(:num)', 'Admin\Attribute::add/$1');
		$routes->get('edit/(:num)/(:num)', 'Admin\Attribute::edit/$1/$2');

		$routes->get('change', 'Admin\Attribute::changeView');
	}); 

	$routes->group('brand', function($routes)
	{
		$routes->get('list', 'Admin\Brand::index');
		$routes->get('add', 'Admin\Brand::add');
		$routes->get('edit/(:num)', 'Admin\Brand::edit/$1');
	}); 

	$routes->group('product', function($routes)
	{
		$routes->get('list', 'Admin\Product::index');
		$routes->get('list/(:num)', 'Admin\Product::index/$1');
		$routes->get('add', 'Admin\Product::add');
		$routes->get('edit/(:num)', 'Admin\Product::edit/$1');
		$routes->get('edit/(:num)/(:any)', 'Admin\Product::edit/$1/$2');
	}); 

	$routes->group('marketplace', function($routes)
	{
		$routes->get('product/(:any)', 'Admin\Marketplace::productView/$1');
		$routes->get('productAdd/(:any)', 'Admin\Marketplace::productAddView/$1');
		$routes->get('list/(:num)', 'Admin\Product::index/$1');
		$routes->get('add', 'Admin\Product::add');
		$routes->get('edit/(:num)', 'Admin\Product::edit/$1');
		$routes->get('edit/(:num)/(:any)', 'Admin\Product::edit/$1/$2');
	}); 

	$routes->group('user', function($routes)
	{ 
		$routes->get('add', 'Admin\User::add'); 
		$routes->get('detail/(:num)', 'Admin\User::detail/$1'); 
		$routes->get('detail/(:num)/(:num)', 'Admin\User::detail/$1/$2'); 
		$routes->get('list', 'Admin\User::list'); 
	}); 

	$routes->group('managers', function($routes)
	{ 
		$routes->get('add', 'Admin\Managers::add'); 
		$routes->get('detail/(:num)', 'Admin\Managers::detail/$1'); 
		$routes->get('list', 'Admin\Managers::list'); 
	}); 

	$routes->group('invoice', function($routes)
	{ 
		$routes->get('add', 'Admin\Invoice::add'); 
		$routes->get('edit', 'Admin\Invoice::edit'); 
	}); 

	$routes->group('order', function($routes)
	{ 
		$routes->get('add', 'Admin\Order::add'); 
		$routes->get('detail/(:num)', 'Admin\Order::detail/$1'); 
		$routes->get('all', 'Admin\Order::all' , ['as' => 'order/all']); 
		$routes->get('waiting', 'Admin\Order::waiting' , ['as' => 'order/waiting']); 
		$routes->get('prepared', 'Admin\Order::prepared' , ['as' => 'order/prepared']); 
		$routes->get('shipped', 'Admin\Order::shipped' , ['as' => 'order/shipped']); 
		$routes->get('today', 'Admin\Order::today' , ['as' => 'order/today']); 
		$routes->get('delivered', 'Admin\Order::delivered' , ['as' => 'order/delivered']); 
	}); 
 
	$routes->group('slider', function($routes)
	{ 
		$routes->get('add', 'Admin\Slider::add'); 
		$routes->get('edit/(:num)', 'Admin\Slider::edit/$1');
		$routes->get('list', 'Admin\Slider::list');  
	}); 

	$routes->group('blog', function($routes)
	{ 
		$routes->get('add', 'Admin\Blog::add'); 
		$routes->get('edit/(:num)', 'Admin\Blog::edit/$1');
		$routes->get('list', 'Admin\Blog::index');
	}); 

	$routes->group('about', function($routes)
	{ 
		$routes->get('add', 'Admin\About::add'); 
		$routes->get('edit/(:num)', 'Admin\About::edit/$1');
		$routes->get('list', 'Admin\About::index');
	}); 

	$routes->group('shopping-center', function($routes)
	{ 
		$routes->get('add', 'Admin\ShoppingCenter::add'); 
		$routes->get('edit/(:num)', 'Admin\ShoppingCenter::edit/$1');
		$routes->get('list', 'Admin\ShoppingCenter::index');
	}); 

	$routes->group('cargoSetting', function($routes)
	{ 
		$routes->get('add', 'Admin\cargoSetting::add'); 
		$routes->get('edit/(:num)', 'Admin\cargoSetting::edit/$1');
		$routes->get('list', 'Admin\cargoSetting::index');
	}); 

	$routes->group('setting', function($routes)
	{ 
		$routes->get('general-setting', 'Admin\GeneralSetting::generalList'); 
		$routes->get('social-setting', 'Admin\GeneralSetting::socialList'); 
		$routes->get('contact-setting', 'Admin\GeneralSetting::contactList'); 
	}); 

	$routes->group('fixedFields', function($routes)
	{ 
		$routes->get('edit/(:num)', 'Admin\fixedFields::edit/$1');
		$routes->get('list', 'Admin\fixedFields::index');
	}); 

	$routes->group('contracts', function($routes)
	{ 
		$routes->get('add', 'Admin\Contracts::add'); 
		$routes->get('edit/(:num)', 'Admin\Contracts::edit/$1');
		$routes->get('list', 'Admin\Contracts::list' , ['as' => 'contracts/list']);
	}); 

	$routes->group('special', function($routes)
	{ 
		$routes->get('add', 'Admin\Special::add'); 
		$routes->get('edit/(:num)', 'Admin\Special::edit/$1');
		$routes->get('list', 'Admin\Special::list' , ['as' => 'special/list']);
	}); 

	$routes->group('coupon', function($routes)
	{ 
		$routes->get('add', 'Admin\Coupon::add'); 
		$routes->get('edit/(:num)', 'Admin\Coupon::edit/$1'); 
		$routes->get('list', 'Admin\Coupon::list');
	}); 

	$routes->group('comment', function($routes)
	{  
		$routes->get('add', 'Admin\Comment::add'); 
		$routes->get('edit', 'Admin\Comment::edit'); 
		$routes->get('list', 'Admin\Comment::list');
	});

	$routes->group('return', function($routes)
	{   
		$routes->get('add', 'Admin\Returnorder::add'); 
		$routes->get('detail/(:num)', 'Admin\Returnorder::detail/$1'); 
		$routes->get('list', 'Admin\Returnorder::list');
	}); 

	$routes->group('ticket', function($routes)
	{   
		$routes->get('add', 'Admin\Ticket::add'); 
		$routes->get('edit', 'Admin\Ticket::edit'); 
		$routes->get('list', 'Admin\Ticket::list');
	});

	$routes->group('campaign', function($routes)
	{   
		$routes->get('add', 'Admin\Campaign::add');
		$routes->get('edit/(:num)', 'Admin\Campaign::edit/$1');
		$routes->get('list', 'Admin\Campaign::list');
	});

	$routes->group('faq', function($routes)
	{
		$routes->get('group-list', 'Admin\FaqGroup::index');
		$routes->get('group-add', 'Admin\FaqGroup::add');
		$routes->get('group-edit/(:num)', 'Admin\FaqGroup::edit/$1');

		$routes->get('list/(:num)', 'Admin\Faq::index/$1');
		$routes->get('list/(:num)/(:num)', 'Admin\Faq::index/$1/$2');
		$routes->get('add/(:num)', 'Admin\Faq::add/$1');
		$routes->get('edit/(:num)/(:num)', 'Admin\Faq::edit/$1/$2');
	}); 

	$routes->group('menu', function($routes)
	{
		$routes->get('group-list', 'Admin\MenuGroup::index');
		$routes->get('group-add', 'Admin\MenuGroup::add');
		$routes->get('group-edit/(:num)', 'Admin\MenuGroup::edit/$1');

		$routes->get('list', 'Admin\Menu::index');
		$routes->get('list/(:num)', 'Admin\Menu::index/$1');
		$routes->get('add', 'Admin\Menu::add');
		$routes->get('edit/(:num)', 'Admin\Menu::edit/$1');
	}); 

}); 

$routes->post('insertLogData', 'Admin\Logs::insertLogData');

$routes->post('admin/LoginCheack', 'Admin\Login::LoginCheack');
$routes->post('categoryAdd', 'Admin\Categories::insert');
$routes->post('categoryEdit', 'Admin\Categories::update');
$routes->post('categoryDelete', 'Admin\Categories::delete');
$routes->post('deleteCategoryImg', 'Admin\Categories::deleteImage');
$routes->post('statusCategories', 'Admin\Categories::status');
$routes->post('statusCimriCategories', 'Admin\Categories::statusCimri');
$routes->post('statusAkakceCategories', 'Admin\Categories::statusAkakce');
$routes->post('statusMubikoCategories', 'Admin\Categories::statusMubiko');

$routes->post('admin/LoginCheack', 'Admin\Login::LoginCheack');
$routes->post('brandAdd', 'Admin\Brand::insert');
$routes->post('brandEdit', 'Admin\Brand::update');
$routes->post('brandDelete', 'Admin\Brand::delete');
$routes->post('brandDeleteImg', 'Admin\Brand::deleteImg');
$routes->post('statusBrand', 'Admin\Brand::status');
$routes->post('populerBrand', 'Admin\Brand::populer');
$routes->post('statusCimriBrand', 'Admin\Brand::statusCimri');
$routes->post('statusAkakceBrand', 'Admin\Brand::statusAkakce');
$routes->post('statusMubikoBrand', 'Admin\Brand::statusMubiko');

$routes->post('attributeGroupAdd', 'Admin\AttributeGroup::insert');
$routes->post('attributeGroupEdit', 'Admin\AttributeGroup::update');
$routes->post('attributeGroupDelete', 'Admin\AttributeGroup::delete');
$routes->post('statusAttributeGroup', 'Admin\AttributeGroup::status');

$routes->post('attributeAdd', 'Admin\Attribute::insert');
$routes->post('attributeEdit', 'Admin\Attribute::update');
$routes->post('attributeChange', 'Admin\Attribute::change');
$routes->post('attributeDelete', 'Admin\Attribute::delete');
$routes->post('statusAttribute', 'Admin\Attribute::status');
$routes->post('attributeRank', 'Admin\Attribute::rank');

$routes->post('contractAdd', 'Admin\Contracts::insert');
$routes->post('contractEdit', 'Admin\Contracts::update');
$routes->post('contractDelete', 'Admin\Contracts::delete');
$routes->post('contractStatus', 'Admin\Contracts::status');
$routes->post('contractRank', 'Admin\Contracts::rank');

$routes->post('specialAdd', 'Admin\Special::insert');
$routes->post('specialEdit', 'Admin\Special::update');
$routes->post('specialDelete', 'Admin\Special::delete');
$routes->post('specialStatus', 'Admin\Special::status');
$routes->post('specialRank', 'Admin\Special::rank');
$routes->post('spacialPictureRankChange', 'Admin\Special::spacialPictureRankChange');

$routes->post('productCombination', 'Admin\Product::combination');
$routes->post('productInsert', 'Admin\Product::insert');
$routes->post('productUpdate', 'Admin\Product::update');
$routes->post('productDelete', 'Admin\Product::delete');
$routes->post('statusProduct', 'Admin\Product::status');
$routes->post('productCombinationNew/(:num)', 'Admin\Product::combinationNew/$1');
$routes->post('prodcutIMGUpload', 'Admin\Product::imageUpload');
$routes->post('deleteProductImg', 'Admin\Product::imageDelete');
$routes->post('deleteProductImgEdit', 'Admin\Product::imageDeleteEdit');
$routes->post('productCombinationEdit', 'Admin\Product::productCombinationEdit');
$routes->post('pictureRankChange', 'Admin\Product::pictureRankChange');
$routes->post('ProductCombinationDefault', 'Admin\Product::productCombinationDefault');
$routes->post('productAttributeDelete', 'Admin\Product::productAttributeDelete');
$routes->post('getFeatureCategories', 'Admin\Product::getFeatureCategories');

$routes->post('sliderAdd', 'Admin\Slider::insert');
$routes->post('sliderEdit', 'Admin\Slider::update');
$routes->post('sliderDelete', 'Admin\Slider::delete');
$routes->post('sliderDesktopDeleteImg', 'Admin\Slider::desktopDeleteImg');
$routes->post('sliderMobileDeleteImg', 'Admin\Slider::mobileDeleteImg');
$routes->post('sliderStatus', 'Admin\Slider::status');
$routes->post('sliderRank', 'Admin\Slider::rank');

$routes->post('campaignAdd', 'Admin\Campaign::insert');
$routes->post('campaignEdit', 'Admin\Campaign::update');
$routes->post('campaignDelete', 'Admin\Campaign::delete');
$routes->post('campaignDeleteImg', 'Admin\Campaign::deleteImg');
$routes->post('campaignStatus', 'Admin\Campaign::status');
$routes->post('campaignRank', 'Admin\Campaign::rank');

$routes->post('couponAdd', 'Admin\Coupon::insert');
$routes->post('couponEdit', 'Admin\Coupon::update');
$routes->post('couponDelete', 'Admin\Coupon::delete');
$routes->post('couponStatus', 'Admin\Coupon::status');


$routes->post('userEdit', 'Admin\User::userUpdate');
$routes->post('userEmailChange', 'Admin\User::userEmailChange');
$routes->post('userPasswordChange', 'Admin\User::userPasswordChange');
$routes->post('userAddressDeleteAdmin', 'Admin\User::userAddressDeleteAdmin');
$routes->post('getCheckoutEditAddressFormAdmin', 'Admin\User::getCheckoutEditAddressFormAdmin');
$routes->post('panel/userEditAddress', 'Admin\User::userEditAddress');
$routes->post('userStatus', 'Admin\User::userStatus');
$routes->post('userDelete', 'Admin\User::userDelete');


$routes->post('managersAdd', 'Admin\Managers::insert');
$routes->post('managersStatus', 'Admin\Managers::userStatus');
$routes->post('managersDelete', 'Admin\Managers::userDelete');

$routes->post('blogAdd', 'Admin\Blog::insert');
$routes->post('blogEdit', 'Admin\Blog::update');
$routes->post('blogDelete', 'Admin\Blog::delete');
$routes->post('blogCoverDeleteImg', 'Admin\Blog::coverDeleteImg');
$routes->post('blogDeleteImg', 'Admin\Blog::deleteImg');
$routes->post('blogStatus', 'Admin\Blog::status');
$routes->post('blogRank', 'Admin\Blog::rank');

$routes->post('aboutAdd', 'Admin\About::insert');
$routes->post('aboutEdit', 'Admin\About::update');
$routes->post('aboutDelete', 'Admin\About::delete');
$routes->post('aboutDeleteImg', 'Admin\About::deleteImg');
$routes->post('aboutStatus', 'Admin\About::status');
$routes->post('aboutRank', 'Admin\About::rank');

$routes->post('shoppingCenterAdd', 'Admin\ShoppingCenter::insert');
$routes->post('shoppingCenterEdit', 'Admin\ShoppingCenter::update');
$routes->post('shoppingCenterDelete', 'Admin\ShoppingCenter::delete');
$routes->post('shoppingCenterStatus', 'Admin\ShoppingCenter::status');
$routes->post('shoppingCenterRank', 'Admin\ShoppingCenter::rank');

$routes->post('panelOrderEdit', 'Admin\Order::panelOrderEdit');
$routes->post('orderProductNebimAgain', 'Admin\Order::orderProductNebimAgain');

$routes->post('returnOrderEdit', 'Admin\Returnorder::returnOrderEdit');

$routes->post('faqGroupAdd', 'Admin\FaqGroup::insert');
$routes->post('faqGroupEdit', 'Admin\FaqGroup::update');
$routes->post('faqGroupDelete', 'Admin\FaqGroup::delete');
$routes->post('faqGroupRank', 'Admin\FaqGroup::rank');
$routes->post('statusFaqGroup', 'Admin\FaqGroup::status');

$routes->post('faqAdd', 'Admin\Faq::insert');
$routes->post('faqEdit', 'Admin\Faq::update');
$routes->post('faqRank', 'Admin\Faq::rank');
$routes->post('faqDelete', 'Admin\Faq::delete');
$routes->post('statusFaq', 'Admin\Faq::status');

$routes->post('menuGroupAdd', 'Admin\MenuGroup::insert');
$routes->post('menuGroupEdit', 'Admin\MenuGroup::update');
$routes->post('menuGroupDelete', 'Admin\MenuGroup::delete');
$routes->post('menuGroupRank', 'Admin\MenuGroup::rank');
$routes->post('statusMenuGroup', 'Admin\MenuGroup::status');

$routes->post('menuAdd', 'Admin\Menu::insert');
$routes->post('menuEdit', 'Admin\Menu::update');
$routes->post('menuRank', 'Admin\Menu::rank');
$routes->post('menuDelete', 'Admin\Menu::delete');
$routes->post('statusMenu', 'Admin\Menu::status');

$routes->post('cargoEdit', 'Admin\cargoSetting::update');
$routes->post('generalSettingsEdit', 'Admin\GeneralSetting::update');
$routes->post('fieldsEdit', 'Admin\fixedFields::update');

/////////////////////////// Front End Area /////////////////////////////////////
$routes->get('cimri-sitemap.xml', 'Sitemap::cimriSitemap');
$routes->get('product-sitemap.xml', 'Sitemap::productSitemap');
$routes->get('akakce-sitemap.xml', 'Sitemap::akakceSitemap');
$routes->get('giris-yap', 'User::loginView');
$routes->get('aktivasyon/(:any)/(:any)', 'User::activation/$1/$2');
$routes->get('sifremi-sifirla/(:any)/(:any)', 'User::resetPasswordView/$1/$2');
$routes->get('kayit-ol', 'User::registerView');
$routes->get('logout', 'User::logout');


$routes->group('hesabim', ['filter' => 'UserAuthLogin'], function($routes)
{
	$routes->get('bilgi', 'Account::index');
	$routes->get('iletisim', 'Account::notification');
	$routes->get('adres', 'Account::addressView');
	$routes->get('adres-ekle', 'Account::addressAddView');
	$routes->get('adres-duzenle/(:num)', 'Account::addressEditView/$1');
	$routes->get('siparis', 'Account::orderView', ['as' => 'order']);
	$routes->get('siparis/(:any)', 'Account::orderView/$1');
	$routes->get('favori', 'Account::favoriteView');
	$routes->get('favori/(:any)', 'Account::favoriteView/$1');
	$routes->get('detay/(:any)', 'Account::orderDetailView/$1');
	$routes->get('iptal/(:any)', 'Account::orderCancellationView/$1');
	$routes->get('iade-istegi/(:any)', 'Account::orderReturnRequestView/$1');
	$routes->get('iade-detay/(:any)', 'Account::orderReturnRequestDetail/$1');
	$routes->get('iade', 'Account::orderReturnRequestListView');
	$routes->get('iade/(:any)', 'Account::orderReturnRequestListView/$1');
	$routes->get('kupon', 'Account::couponListView');
	$routes->get('kupon/(:any)', 'Account::couponListView/$1');
	
}); 

$routes->get('sepetim', 'Basket::index');
$routes->get('siparis', 'Checkout::index');
$routes->get('odeme', 'Checkout::paymentView');
$routes->get('odeme-sonuc/(:any)', 'Checkout::paymentCallback/$1');
$routes->match(['get', 'post'],'checkout-status', 'Checkout::paymentCallback', ['as' => 'payment_callback']);

$routes->get('hakkimizda', 'AboutList::index');
$routes->get('magazalar', 'ShoppingCenterList::index');
$routes->get('sss', 'FaqList::index');
$routes->get('sss/(:any)', 'FaqList::index/$1');
$routes->get('sayfa/(:any)', 'PageList::index/$1');
$routes->get('ozel/(:any)', 'SpacialView::index/$1');

$routes->get('kampanyalar', 'CampaignList::list');
$routes->get('kampanyalar/(:any)', 'CampaignList::list/$1');

$routes->get('blog', 'BlogList::index');
$routes->get('blog/(:any)', 'BlogList::index/$1');
$routes->get('tags/(:any)', 'BlogList::tags/$1');
$routes->get('tags/(:any)/(:any)', 'BlogList::tags/$1/$2');

$routes->get('blog-detail/(:any)/(:any)', 'BlogList::detail/$1/$2');

$routes->get('arama', 'Search::index');
$routes->get('arama/(:any)', 'Search::index/$1');

$routes->get('', 'Home::index');
$routes->get('(:any)', 'Home::index/$1');
$routes->get('(:any)/(:any)', 'Home::index/$1/$2');
$routes->get('(:any)/(:any)/(:any)', 'Home::index/$1/$2/$3');

$routes->post('registerUser', 'User::register');
$routes->post('loginUser', 'User::login');
$routes->post('forgotPassword', 'User::forgotPassword');
$routes->post('resetPassword', 'User::resetPassword');

$routes->post('informationEdit', 'Account::informationEdit');
$routes->post('notificationEdit', 'Account::notificationEdit');
$routes->post('userNewAddress', 'Account::userNewAddress');
$routes->post('userEditAddress', 'Account::userEditAddress');
$routes->post('userAddressDelete', 'Account::userAddressDelete');
$routes->post('getCheckoutEditAddressForm', 'Account::getCheckoutEditAddressForm');
$routes->post('cancellationOrderProduct', 'Account::cancellationOrderProduct');
$routes->post('returnRequestOrderProduct', 'Account::returnRequestOrderProduct');

$routes->post('panel/citySelectd', 'GetData::citySelectd');
$routes->post('citySelectd', 'GetData::citySelectd');
$routes->post('panel/townSelectd', 'GetData::townSelectd');
$routes->post('townSelectd', 'GetData::townSelectd');
$routes->post('productQuickview', 'GetData::productQuickview');
$routes->post('productStock', 'GetData::productStock');
$routes->post('getPriceArea', 'GetData::getPriceArea');
$routes->post('getCargoReceiptPrint', 'GetData::getCargoReceiptPrint');
$routes->post('lastVisited', 'GetData::lastVisited');
$routes->post('desktopSearch', 'GetData::desktopSearch');
$routes->post('panel/productGetVariantOption', 'GetData::productGetVariantOption');

$routes->post('productFavoritesAdd', 'CategoryList::productFavoritesAdd');
$routes->post('productFavoritesRemove', 'CategoryList::productFavoritesRemove');
$routes->post('filter_add', 'GetData::filter_add');
$routes->post('panel/filter_add', 'GetData::filter_add');

$routes->post('productBasketAdd', 'Basket::productBasketAdd');
$routes->post('basketProductPiece', 'Basket::basketProductPiece');
$routes->post('basketProductDelete', 'Basket::basketProductDelete');
$routes->post('promationCodeUse', 'Basket::promationCodeUse');
$routes->post('promationCodeCanceled', 'Basket::promationCodeCanceled');

$routes->post('orderStart', 'Checkout::orderStart');
$routes->post('checkoutStepOne', 'Checkout::checkoutStepOne');

$routes->post('productCommentAdd', 'ProductDetail::productCommentAdd');



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
