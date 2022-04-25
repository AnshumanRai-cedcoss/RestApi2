<?php

use Phalcon\Mvc\Controller;
use MongoDB\BSON\ObjectID;


class AdminController extends Controller
{

  public function indexAction()
  {
    $this->view->orders = $this->mongo->orders->find()->toArray();
  }
  public function productListAction()
  {
    $this->view->products = $this->mongo->product->find()->toArray();

  }
  public function statusChangeAction()
  {
    $data = $this->request->getPost();
    $this->mongo->orders->updateOne(
        ["_id" => new ObjectID($data['id'])],
        ['$set' => ["status" => ($data['status'])]]
    );
    $this->response->redirect('http://localhost:8080/application/admin');
  }
}