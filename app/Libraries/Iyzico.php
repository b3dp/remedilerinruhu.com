<?php

namespace App\Libraries;

use App\Controllers\BaseController;

class Iyzico extends BaseController
{
    protected $options;
    protected $request;
    protected $buyer;
    protected $shippingAddress;
    protected $billingAddress;
    protected $basketItems;

    public function __construct()
   
    {  
        $this->options = new \Iyzipay\Options();
         
        /*
            $this->options->setApiKey("TioFMCtaXIsh1KagsUgZTXsrNCOrAAD9");
            $this->options->setSecretKey("r0r2BkvUbe3Ds4EntgH2fpRpfq6HfPvj");
            $this->options->setBaseUrl("https://api.iyzipay.com");
        */
            $this->options->setApiKey("sandbox-EyJzuDFP2uSufajQuCdLWaopcGmCYDrm");
            $this->options->setSecretKey("sS3xQWkDRuEzvmotmO80pVjpKrlajOxC");
            $this->options->setBaseUrl("https://sandbox-api.iyzipay.com");
        
        $this->basketItems = [];
    }

    public function setForm(Array $params)
    {
        $this->request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $this->request->setLocale(\Iyzipay\Model\Locale::TR);
        $this->request->setConversationId($params['ConversationId']);
        $this->request->setPrice($params['setPrice']);
        $this->request->setPaidPrice($params['setPaidPrice']);
        $this->request->setCurrency(\Iyzipay\Model\Currency::TL);
        $this->request->setBasketId($params['setBasketId']);
        $this->request->setEnabledInstallments(array(2, 3, 6, 9));
        $this->request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $this->request->setCallbackUrl(base_url(route_to('payment_callback')));
        return $this;
    }

    public function setBuyer(Array $params)
    {
        $this->buyer = new \Iyzipay\Model\Buyer();
        $this->buyer->setId($params['setId']);
        $this->buyer->setName($params['setName']);
        $this->buyer->setSurname($params['setSurname']);
        $this->buyer->setGsmNumber($params['setGsmNumber']);
        $this->buyer->setEmail($params['setEmail']);
        $this->buyer->setIdentityNumber('123456');
        $this->buyer->setRegistrationAddress($params['setRegistrationAddress']);
        $this->buyer->setIp($params['setIp']);
        $this->buyer->setCity($params['setCity']);
        $this->buyer->setCountry($params['setCountry']);
        $this->request->setBuyer($this->buyer);
        
        return $this;
    }

    public function setShipping(Array $params)
    {
        $this->shippingAddress = new \Iyzipay\Model\Address();
        $this->shippingAddress->setContactName($params['setContactName']);
        $this->shippingAddress->setCity($params['setCity']);
        $this->shippingAddress->setCountry($params['setCountry']);
        $this->shippingAddress->setAddress($params['setAddress']);
        $this->request->setShippingAddress($this->shippingAddress);

        return $this;
    }

    public function setBilling(Array $params)
    {
        $this->billingAddress = new \Iyzipay\Model\Address();
        $this->billingAddress->setContactName($params['setContactName']);
        $this->billingAddress->setCity($params['setCity']);
        $this->billingAddress->setCountry($params['setCountry']);
        $this->billingAddress->setAddress($params['setAddress']);
        $this->request->setBillingAddress($this->billingAddress);

        return $this;
    }

    public function setItems(Array $items)
    {
        foreach ($items as $row) {
            $product_id = $row['variant_id'] ? $row['variant_id'] : $row['product_id'];
            $basketItem = new \Iyzipay\Model\BasketItem();
            $basketItem->setId($product_id);
            $basketItem->setName($row['title']);
            $basketItem->setCategory1($row['categories']);
            $basketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
            $basketItem->setPrice($row['set_price']);
            array_push($this->basketItems,  $basketItem);
        }
        $this->request->setBasketItems($this->basketItems);

        return $this;
    }

    public function paymentForm()
    {
        $form = \Iyzipay\Model\CheckoutFormInitialize::create($this->request, $this->options);
        return $form;
    }

    public function callbackForm($token, $order_no)
    {
        $request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setConversationId($order_no);
        $request->setToken($token);
        $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($request, $this->options);

        return $checkoutForm;
    }

    public function cancelRequest($paymentId, $price)
    {
        $request = new \Iyzipay\Request\CreateRefundRequest();
        $request->setLocale(\Iyzipay\Model\Locale::TR);
        $request->setPaymentTransactionId($paymentId);
        $request->setPrice($price);
        $request->setCurrency(\Iyzipay\Model\Currency::TL);
        $request->setIp(getClientIpAddress());
        
        $refund = \Iyzipay\Model\Refund::create($request, $this->options);
        return $refund;
    }
}