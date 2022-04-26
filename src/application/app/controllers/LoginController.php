<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LoginController extends Controller
{

  public function indexAction()
  {
    if ($this->request->has('loginAdmin')) {
      $v = "Wrong Credentials!!";
      $data = $this->request->getPost();
      $a = $this->mongo->users->findOne([
        "email" => $data['email'],
        "password" => $data['password']
      ]);
      /**
       * Matching Credentials
       */
      if (!$a == null && $a->role == "admin") {
        $this->response->redirect("http://".BASE_URI."/application/admin");
      } else if (!$a == null && $a->role == "user") {
        $token = $a->token;
        $key = "example_key";
        try {
          $decoded = JWT::decode($token, new Key($key, 'HS256'));
          $GLOBALS["userMail"] = $decoded->email;
          $GLOBALS["userNm"] = $decoded->name;
          $this->response->redirect("http://".BASE_URI."/application/user/webhook");
        } catch (\Exception $e) {
            $this->response->setStatusCode(400)
            ->setJsonContent($e->getMessage())
            ->send();
          die;
        }
      } else {
        $v = "Wrong Credentials";
      }
    }
  }
}
