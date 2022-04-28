<?php 

    namespace App\Filters;

    use CodeIgniter\HTTP\RequestInterface;
    use CodeIgniter\HTTP\ResponseInterface;
    use CodeIgniter\Filters\FilterInterface;

    class UserAuthLogin Implements FilterInterface 
    {   
        public function before(RequestInterface $request = null, $arguments = null) 
        {
            if (!session()->get('user')) {
                return redirect()->to(site_url('giris-yap'));
            }
        }

        public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) 
        {
          
        }
    }
?>