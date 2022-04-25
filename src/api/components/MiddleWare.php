<?php

namespace Api\Components;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Phalcon\Mvc\Micro\MiddlewareInterface;
use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use MongoDB\BSON\ObjectID;

$userMail = '';
$UserNm = '';

class MiddleWare implements MiddlewareInterface
{

    public function authorize($app)
    {
        $key = "example_key";
        $payload = array(
            "iss" => "http://example.org",
            "aud" => "https://target.phalcon.io",
            "iat" => 1356999524,
            "nbf" => 1357000000,
            "role" => 'user',
            "name" => 'Ashu',
            "email" => 'abc@xyz.com',
            "fsf" => "https://phalcon.io"
        );

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        $jwt = JWT::encode($payload, $key, 'HS256');


        $app->response->setStatusCode(400)
            ->setJsonContent($jwt)
            ->send();
    }
    public function validate($token, $app)
    {
        $key = "example_key";
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $GLOBALS["userMail"] = $decoded->email;
        $GLOBALS["userNm"] = $decoded->name;

    }
    public function call(Micro $app)
    {
        // $token = $this->authorize($app);

        $check = explode('/', $app->request->get()['_url'])[2];
        if ($check == "create") {
            $response = new Response();
            $request = new Request();

            $getproduct = json_decode(json_encode($request->getJsonRawBody()), true);
            $checkproduct_id = ($getproduct['product_id']);
            try {
                $result = $app->mongo->product->findOne([
                    "_id" => new ObjectID($checkproduct_id)
                ]);
                if (!empty($result)) {

                    if ($getproduct['quantity'] < 0 || $getproduct['quantity'] > $result->stock) {
                        if ($getproduct['quantity'] < 0) {
                            $response->setStatusCode(404, 'Not Available');
                            $response->setJsonContent(" Quantity Can't be Negative'");
                        } else {
                            $response->setStatusCode(404, 'Not Available');
                            $response->setJsonContent("This much Quantity not available for this Product!!'");
                        }
                        $response->send();
                        die;
                    }
                    $token =  $app->request->get("token");
                    $this->validate($token, $app);
                } else {
                    $response->setStatusCode(404, 'No Match Found ');
                    $response->setJsonContent("Invalid Product ID");
                    $response->send();
                    die;
                }
            } catch (\Exception $e) {
                $response->setStatusCode(404, 'Please Enter Valid Product ID');
                $response->setJsonContent("Please Enter Valid Product ID!!");
                $response->send();
                die;
            }
        } else {
            $token =  $app->request->get("token");
            $this->validate($token, $app);
        }
    }
}
