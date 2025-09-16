<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Exceptions\PageNotFoundException;

class Pages extends BaseController
{
    public function index()
    {
        return view('welcome_message');
    }

    public function view(string $pages = 'home'){
            if(!is_file(APPPATH.'Views/pages/'.$pages.'.php')){
                // we don't have a page for that!
                throw new PageNotFoundException($pages);
            }

            $data['title']=ucfirst($pages); // Capitalize the first letter
            return view('templates/header',$data)
                .view('pages/'.$pages)
                .view('templates/footer');

    }
}
