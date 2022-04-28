<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Page extends BaseController
{ 
    public function _construct()
    {
       
    } 
    public function add()
    {   
        return view ("admin/page/page-add");
    }
    public function edit()
    {
        return view ("admin/page/page-edit");
    }
    public function list()
    {
        return view ("admin/page/page-list");
    } 
}