<?php

namespace Admin;

use \App;
use \View;

class GroupController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->data['menu_active'] = 'group';
    }

    public function index()
    {
        $this->data['title'] = 'Group List';
        /** render the template */
        View::display('admin/group/index.twig', $this->data);
    }

    public function show()
    {

    }

    public function store()
    {

    }

    public function create()
    {

    }

    public function edit()
    {

    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}