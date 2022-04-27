<?php

namespace App\Components;

use Phalcon\Di\Injectable;
use Firebase\JWT\JWT;
use Phalcon\Events\ManagerInterface;

/**
 * Loader Class
 */
class Helper extends Injectable
{
    /**
     * process function
     * @return void
     */
    public function validate()
    {
        if (!isset($this->session->user)) {
            $this->response->redirect("application/login");
        }
    }

    public function tokenValidate($name, $email)
    {
        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "https://target.phalcon.io",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "role" => 'user',
            "name" => $name,
            "email" =>  $email,
            "fsf" => "https://phalcon.io"
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    public function validateAdmin()
    {
        if (!isset($this->session->user) || $this->session->user["role"] != "admin") {
            $this->response->redirect("application/user/error");
        }
    }
}
