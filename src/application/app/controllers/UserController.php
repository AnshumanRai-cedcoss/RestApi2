<?php

use Phalcon\Mvc\Controller;
use Firebase\JWT\JWT;

class UserController extends Controller
{

    public function indexAction()
    {
        if ($this->request->has('addUser')) {

            $data = $this->request->getPost();
            print_r($data);
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

            /**
             * IMPORTANT:
             * You must specify supported algorithms for your application. See
             * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
             * for a list of spec-compliant algorithms.
             */
            $jwt = JWT::encode($payload, $key, 'HS256');
            $f = 0;

            $a = $this->mongo->users->findOne([

                "email" => $data['email']
            ]);
            if (count($a) > 0) {
                $f = 1;
                $this->view->msg = "Email Must Be Unique!!";
            }

            /**
             * Storing All Details in DB
             */
            if ($f == 0) {
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
}
