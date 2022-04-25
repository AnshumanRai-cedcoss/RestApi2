<?php

use Phalcon\Mvc\Controller;

class LoginController extends Controller
{

  public function indexAction()
  {
    if($this->request->has('loginAdmin'))
    {
        $v="Wrong Credentials!!";
        $data = $this->request->getPost();
       
        $a = $this->mongo->users->findOne([

            "email" => $data['email'],
            "password" => $data['password']

        ]);  
        /**
         * Matching Credentials
         */
         if(!$a==null && $a->role=="admin")
         {
          $this->response->redirect("http://localhost:8080/application/admin");
         }
         else if (!$a == null && $a->role == "customer") {
             die("you are not admin");
            $this->response->redirect("http://localhost:8080/application/user/userwebhook");
        } 
        else
        {
            $v= "Wrong Credentials";
            
        }
    }
  }
}