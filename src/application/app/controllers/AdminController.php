<?php

use Phalcon\Mvc\Controller;
use MongoDB\BSON\ObjectID;
use Phalcon\Events\Manager as EventsManager;

/**
 * Admin Controller
 * All the work of the admin
 */
class AdminController extends Controller
{
  /**
   * To view all orders for admin
   * @return void
   */
  public function indexAction()
  {
    $this->view->orders = $this->mongo->orders->find()->toArray();
  }

  /**
   * addProduct function
   * Admin adding a product
   * @return void
   */
  public function addProductAction()
  {
    if ($this->request->has('add')) {
      $data = $this->request->getPost();
      $this->mongo->product->insertOne([
        'name' => $data["name"],
        'price' => $data["price"],
        'stock' => $data["stock"]
      ]);
      $eventsManager = new EventsManager();
      $com = new \App\Components\Loader();
      $com->setEventsManager($eventsManager);
      $eventsManager->attach(
        'notifications',
        new \App\Components\NotificationsListener()
      );
      $com->addNew();
    $this->response->redirect("http://".BASE_URI.'/application/admin');
    }
  }


  /**
   * ProductList function
   * To list all products
   * @return void
   */
  public function productListAction()
  {
    $this->view->products = $this->mongo->product->find()->toArray();
  }

  /**
   * Change Status
   * When admin changes the status
   * @return void
   */
  public function statusChangeAction()
  {
    $data = $this->request->getPost();
    $this->mongo->orders->updateOne(
      ["_id" => new ObjectID($data['id'])],
      ['$set' => ["status" => ($data['status'])]]
    );
    $this->response->redirect("http://".BASE_URI.'/application/admin');
  }

  /**
   * updateProduct Action
   * When admin updates a product
   * @return void
   */
  public function updateProdAction()
  {
    $id = $this->request->get("id");
    $result = $this->mongo->product->findOne(['_id' => new ObjectID($id)]);
    $this->view->data = (array)$result;
    if ($this->request->has('update')) {
      $res = $this->request->getPost();
      $this->mongo->product->updateOne(
        ["_id" => new ObjectID($id)],
        ['$set' => ["name" => $res['name'], "price" => $res["price"], "stock" => $res['stock']]]
      );
    }
    $eventsManager = new EventsManager();
    $com = new \App\Components\Loader();
    $com->setEventsManager($eventsManager);
    $eventsManager->attach(
      'notifications',
      new \App\Components\NotificationsListener()
    );
    $com->processRequest();
  }
}
