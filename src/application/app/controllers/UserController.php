<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;

class UserController extends Controller
{

    public function indexAction()
    {
        if ($this->request->has('addUser')) {
            $data = $this->request->getPost();
            $key = "example_key";
            $payload = array(
                "iss" => "http://example.org",
                "aud" => "https://target.phalcon.io",
                "iat" => 1356999524,
                "nbf" => 1357000000,
                "role" => 'user',
                "name" => $data["uName"],
                "email" =>  $data["email"],
                "fsf" => "https://phalcon.io"
            );
            $jwt = JWT::encode($payload, $key, 'HS256');

            $result = $this->mongo->users->findOne(["email" => $data['email']]);
            if (count($result) <= 0) {
                $this->mongo->users->insertOne([
                    "name" => $data['uName'],
                    "email" => $data['email'],
                    "password" => $data['password'],
                    "role" => "user",
                    "token" => $jwt
                ]);
                $this->view->token = $jwt;
            }
        }
    }

    public function webhookAction()
    {
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
}
