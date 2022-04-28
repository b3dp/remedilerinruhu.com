<?php 

    namespace App\Filters\Admin;

    use CodeIgniter\HTTP\RequestInterface;
    use CodeIgniter\HTTP\ResponseInterface;
    use CodeIgniter\Filters\FilterInterface;

    class AuthLogin Implements FilterInterface 
    {   
        public function before(RequestInterface $request, $arguments = null) 
        {
            if (!session()->get('admin')) {
                $data["title"] = "Giriş Yap";
                return redirect()->to(site_url('panel/login'));
            }
        }

        public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) 
        {
          
        }
    }
?>