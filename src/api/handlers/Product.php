<?php

namespace Api\Handlers;

use Phalcon\Di\Injectable;

/**
 * Producr Handler class
 * to handle all the product requests
 */     
class Product extends Injectable
{
    function createToken()
    {
        
    }
    function allProducts()
    {
        $result = $this->mongo->product->find();
        $this->response->setStatusCode(200, 'Found');
        $this->response->setJsonContent([
            "status" => "200",
            "data" => $result->toArray()
        ]);
        $this->response->send();
    }
}
