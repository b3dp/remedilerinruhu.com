<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Ticket extends BaseController
{ 
    public function _construct()
    {
       
    } 
    public function add()
    {   
        return view ("admin/ticket/ticket-add");
    }
    public function edit()
    {
        return view ("admin/ticket/ticket-edit");
    }
    public function list()
    {
        return view ("admin/ticket/ticket-list");
    } 
}