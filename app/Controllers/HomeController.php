<?php

    namespace App\Controllers;

    use Wow;

    class HomeController extends BaseController {


        public function IndexAction() {


            $this->view->set('title', 'Homepage');
            $this->view->set('keywords', 'app, wow app, php');
            $this->view->set('description', 'Wellcome to wow framework.');

            return $this->view();

        }


    }

