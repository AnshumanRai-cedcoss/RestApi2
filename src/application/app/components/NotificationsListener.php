<?php

namespace App\Components;

use Phalcon\Di\Injectable;
use GuzzleHttp\Client;

class NotificationsListener extends Injectable
{
    /**
     * Check function
     * to update an existing product
     * @return void
     */
    public function check()
    {
        $products = $this->mongo->product->find()->toArray();
        $webhooks = $this->mongo->webhook->find()->toArray();
        $client = new Client();
        foreach ($webhooks as $key => $value) {
            $action = (array)$value["event"];
            if ($action[0] == "update") {
                $client->request(
                    'POST',
                    $value['url'],
                    ["form_params" => ["data" => json_encode($products)]]
                );
            }
        }
    }

    /**
     * Add function
     * To add a new function
     * @return void
     */
    public function add()
    {
        $options = [
            "limit" => 1,
            "sort" => ["_id" => -1]
        ];
        $data = $this->mongo->product->findOne([], $options);
        $webhooks = $this->mongo->webhook->find()->toArray();
        $client = new Client();
        foreach ($webhooks as $key => $value) {
            $action = (array)$value["event"];
            if ($action[0] == "create") {
                echo $value["url"];
                $client->request(
                    'POST',
                    $value['url'],
                    ["form_params" => ["data" => json_encode($data)]]
                );
            }
        }
    }
}
