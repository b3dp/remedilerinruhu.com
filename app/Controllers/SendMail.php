<?php

namespace App\Controllers;

use Config\Email;
use CodeIgniter\I18n\Time;
use App\Models\UserModels;
use App\Models\SettingModels;

class SendMail extends BaseController
{

    public function __construct()
    {
        $corporateMailStart = '
            <!DOCTYPE html>
            <html>
            
            <head>
                <meta charset="UTF-8">
                <title>Customer account confirmation</title>
            
            </head>
            
            <body>
            
                <!DOCTYPE html>
                <html lang="en">
            
                <head>
                    <title>Welcome to Shine Craft Vessel Co.!</title>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                    <meta name="viewport" content="width=device-width">
                </head>
            
                <body style="margin: 0">
                    <style type="text/css">
                        body {
                            margin: 0;
                        }
            
                        h1 a:hover {
                            font-size: 30px;
                            color: #333;
                        }
            
                        h1 a:active {
                            font-size: 30px;
                            color: #333;
                        }
            
                        h1 a:visited {
                            font-size: 30px;
                            color: #333;
                        }
            
                        a:hover {
                            text-decoration: none;
                        }
            
                        a:active {
                            text-decoration: none;
                        }
            
                        a:visited {
                            text-decoration: none;
                        }
            
                        .button__text:hover {
                            color: #fff;
                            text-decoration: none;
                        }
            
                        .button__text:active {
                            color: #fff;
                            text-decoration: none;
                        }
            
                        .button__text:visited {
                            color: #fff;
                            text-decoration: none;
                        }
            
                        a:hover {
                            color: #080e66;
                        }
            
                        a:active {
                            color: #080e66;
                        }
            
                        a:visited {
                            color: #080e66;
                        }
            
                        @media (max-width: 600px) {
                            .container {
                                width: 94% !important;
                            }
            
                            .main-action-cell {
                                float: none !important;
                                margin-right: 0 !important;
                            }
            
                            .secondary-action-cell {
                                text-align: center;
                                width: 100%;
                            }
            
                            .header {
                                margin-top: 20px !important;
                                margin-bottom: 2px !important;
                            }
            
                            .shop-name__cell {
                                display: block;
                            }
            
                            .order-number__cell {
                                display: block;
                                text-align: left !important;
                                margin-top: 20px;
                            }
            
                            .button {
                                width: 100%;
                            }
            
                            .or {
                                margin-right: 0 !important;
                            }
            
                            .apple-wallet-button {
                                text-align: center;
                            }
            
                            .customer-info__item {
                                display: block;
                                width: 100% !important;
                            }
            
                            .spacer {
                                display: none;
                            }
            
                            .subtotal-spacer {
                                display: none;
                            }
                        }
                    </style>
        
        ';

        $orderMailStart = '
            <!DOCTYPE html>
            <html >
            <head>
                <meta charset="UTF-8">
                <title>Order SCV2016 confirmed</title>
            </head>
            
            <body>
            
                <!DOCTYPE html>
            <html lang="en">
            
            <head>
            <title>Thank you for your purchase!</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <meta name="viewport" content="width=device-width">
            </head>
            
            <body style="margin: 0">
                <style type="text/css">
                    body {
                    margin: 0;
                    }
                    h1 a:hover {
                    font-size: 30px; color: #333;
                    }
                    h1 a:active {
                    font-size: 30px; color: #333;
                    }
                    h1 a:visited {
                    font-size: 30px; color: #333;
                    }
                    a:hover {
                    text-decoration: none;
                    }
                    a:active {
                    text-decoration: none;
                    }
                    a:visited {
                    text-decoration: none;
                    }
                    .button__text:hover {
                    color: #fff; text-decoration: none;
                    }
                    .button__text:active {
                    color: #fff; text-decoration: none;
                    }
                    .button__text:visited {
                    color: #fff; text-decoration: none;
                    }
                    a:hover {
                    color: #080e66;
                    }
                    a:active {
                    color: #080e66;
                    }
                    a:visited {
                    color: #080e66;
                    }
                    @media (max-width: 600px) {
                        .container {
                        width: 94% !important;
                        }
                        .main-action-cell {
                        float: none !important; margin-right: 0 !important;
                        }
                        .secondary-action-cell {
                        text-align: center; width: 100%;
                        }
                        .header {
                        margin-top: 20px !important; margin-bottom: 2px !important;
                        }
                        .shop-name__cell {
                        display: block;
                        }
                        .order-number__cell {
                        display: block; text-align: left !important; margin-top: 20px;
                        }
                        .button {
                        width: 100%;
                        }
                        .or {
                        margin-right: 0 !important;
                        }
                        .apple-wallet-button {
                        text-align: center;
                        }
                        .customer-info__item {
                        display: block; width: 100% !important;
                        }
                        .spacer {
                        display: none;
                        }
                        .subtotal-spacer {
                        display: none;
                        }
                    }
                </style>
        ';
    }

    public function SendMail($setTo = '', $setCC = '', $setBCC = '', $setSubject = '', $setMessage = '', $attach = array())
	{
        $email = \Config\Services::email();
        $db =  db_connect();
        $settingModels = new SettingModels($db);
        $contactSetting = $settingModels->c_all(['type' => 'contact']);
        $contact = namedSettings($contactSetting);
        if ($setCC) {
            $email->setCC($setCC);
        }
        if ($setBCC) {
            $email->setBCC($setBCC);
        }
        $email->attach($attach['file_location'], 'attachment', $attach['file_name']);
        $email->setSubject($setSubject);
        $email->setMessage($this->corporateMailStart.$setMessage);
        
        if ($email->send()) {
            return TRUE;
        }else{
            return FALSE;
        }
        
	}
    public function SendMailOrder($setTo = '', $setCC = '', $setBCC = '', $setSubject = '', $setMessage = '', $attach = array())
	{
      

        $db =  db_connect();
        $settingModels = new SettingModels($db);
        $contactSetting = $settingModels->c_all(['type' => 'contact']);
        $contact = namedSettings($contactSetting);

        $generalSetting = $settingModels->c_all(['type' => 'contact']);
        $general = namedSettings($generalSetting);
        $email = \Config\Services::email();
        
        $email->setTo($setTo);
        if ($setCC) {
            $email->setCC($setCC);
        }
        if ($setBCC) {
            $email->setBCC($setBCC);
        }
        $email->attach($attach['file_location'], 'attachment', $attach['file_name']);
        $email->setSubject($setSubject);
        $email->setMessage($this->orderMailStart.$setMessage);
        
        if ($email->send()) {
            return TRUE;
        }else{
            return FALSE;
        }
        
	}

}