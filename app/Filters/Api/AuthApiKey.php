<?php 

    namespace App\Filters\Api;

    use CodeIgniter\HTTP\RequestInterface;
    use CodeIgniter\HTTP\ResponseInterface;
    use CodeIgniter\Filters\FilterInterface;

    class AuthApiKey Implements FilterInterface 
    {   
        public function before(RequestInterface $request, $arguments = null) 
        {
            $apiKey = $request->getGet('apiKey');
            if ($apiKey != '953E1C7C06494141B8DF4BBBDE76ED5E') {
                $data = [
                    'status' => 'error',
                    'message' => 'Lütfen api key anahtarınızı kontrol ederek tekrar deneyiniz.',
                ];
                print_r(json_encode($data));
                exit;
            }
        }

        public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) 
        {
          
        }
    }
?>