<?php 

    namespace App\Controllers\Api;
    
    use App\Controllers\BaseController;
    use MailchimpTransactional;
    use MailchimpMarketing;

    class MailchimpApi extends BaseController
    { 
        public function _construct()
        {
            
        }

        function MailchimpRun(){
            try {
                $client = new MailchimpMarketing\ApiClient();
                $client->setConfig([
                    'apiKey' => '465aeb7963be377220b20e8de250ec61-us20',
                    'server' => 'us20',
                ]);
                
                $response = $client->lists->getAllLists();
                print_r($response);
             } catch (Error $e) {
                   echo 'Error: ',  $e->getMessage(), "\n";
             }
        }
    }
