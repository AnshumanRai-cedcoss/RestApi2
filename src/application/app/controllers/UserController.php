<?php

use Phalcon\Mvc\Controller;

class UserController extends Controller
{
    public function indexAction()
    {
        if ($this->request->has('addUser')) {
            $data = $this->request->getPost();
            $result = $this->mongo->users->findOne(["email" => $data['email']]);
            if (count($result) <= 0) {
                $ob = new \App\Components\Helper;
                $jwt = $ob->tokenValidate($data["uName"], $data['email']);
                $this->mongo->users->insertOne([
                    "name" => $data['uName'],
                    "email" => $data['email'],
                    "password" => $data['password'],
                    "role" => "user",
                    "token" => $jwt
                ]);
                $this->view->token = $jwt;
            } else {
                $this->view->message = "Email already exists!Please sign in";
            }
        }
    }

    public function webhookAction()
    {
        $ob = new App\Components\Helper;
        $ob->validate();
        if ($this->request->has('addWeb')) {
            $data = $this->request->getPost();
            $arr = [];
            foreach ($data as $key => $value) {
                if ($value == "on") {
                    array_push($arr, strtolower($key));
                }
            }
            $this->mongo->webhook->insertOne([
                "name" => $data['WebHook'],
                "url" => $data['url'],
                "key" => $data['key'],
                "event" => $arr
            ]);
        }
    }

    public function errorAction()
    {
    }

    public function signOutAction()
    {
        $this->session->remove('user');
        $this->session->destroy();
        $this->response->redirect('index');
    }
}
