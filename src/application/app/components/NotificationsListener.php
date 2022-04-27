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
            foreach ($action as $key => $v) {
                if ($v == "update") {
                    $client->request(
                        'POST',
                        $value['url'] . $v,
                        ["form_params" => ["data" => json_encode($products)]]
                    );
                }
            }
        }
    }


    public function delete()
    {
        $id = $this->request->get('id');
        $webhooks = $this->mongo->webhook->find()->toArray();
        $client = new Client();
        foreach ($webhooks as $key => $value) {
            $action = (array)$value["event"];
            foreach ($action as $key => $v) {
                if ($v == "delete") {
                    $client->request(
                        'POST',
                        $value['url'] . $v,
                        ["form_params" => ["data" => $id]]
                    );
                }
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
            foreach ($action as $key => $v) {
                if ($v == "create") {
                    $client->request(
                        'POST',
                        $value['url'] . $v,
                        ["form_params" => ["data" => json_encode($data)]]
                    );
                }
            }
        }
    }
}
