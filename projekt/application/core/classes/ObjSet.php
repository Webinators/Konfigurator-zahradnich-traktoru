<?php
/**
 * Created by PhpStorm.
 * User: Radim
 * Date: 22. 4. 2016
 * Time: 20:02
 */

namespace System\Objects\Collection;

use System\Authentication;

    trait ObjSet
    {
        protected $Root = null;
        protected $Permissions = null;
        protected $Sessions = null;
        protected $Icons = null;
        protected $Output = null;

        private $load = false;

        function __construct(){

            $this->Root = new \TreeDirectory();
            $this->Sessions = \Sessions::initialize();
            $this->Admin = Authentication\Admin::initialize();
            $this->Permissions = Authentication\Permissions::initialize();
            $this->User = Authentication\Users::initialize();
            $this->Icons = new \Icons();

        }

        private function loadSystem(){

            if($this->load){
                return;
            }

            $this->Root = new \TreeDirectory();
            $this->Sessions = \Sessions::initialize();
            $this->Admin = Authentication\Admin::initialize();
            $this->Permissions = Authentication\Permissions::initialize();
            $this->User = Authentication\Users::initialize();
            $this->Icons = new \Icons();

            $this->load = true;

        }

        public function __get($name){

            if(property_exists("ObjSet", $name)){
                return $this->$$name;
            }

        }
    }
