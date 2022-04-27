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
    $ob = new App\Components\Helper;
    $ob->validateAdmin();
    $this->view->orders = $this->mongo->orders->find()->toArray();
  }

  /**
   * addProduct function
   * Admin adding a product
   * @return void
   */
  public function addProductAction()
  {
    $ob = new App\Components\Helper;
    $ob->validateAdmin();
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
      $this->response->redirect("http://" . BASE_URI . '/application/admin/productList');
    }
  }


  /**
   * ProductList function
   * To list all products
   * @return void
   */
  public function productListAction()
  {
    $ob = new App\Components\Helper;
    $ob->validateAdmin();
    $this->view->products = $this->mongo->product->find()->toArray();
  }

  /**
   * deleting the product by admin
   * product id is must
   * @return void
   */
  public function deleteProdAction()
  {
    $ob = new App\Components\Helper;
    $ob->validateAdmin();
    $id = $this->request->get("id");
    $this->mongo->product->deleteOne(
      ["_id" => new ObjectID($id)]
    );
    $eventsManager = new EventsManager();
    $com = new \App\Components\Loader();
    $com->setEventsManager($eventsManager);
    $eventsManager->attach(
      'notifications',
      new \App\Components\NotificationsListener()
    );
    $com->deleteProduct();
    $this->response->redirect("http://" . BASE_URI . '/application/admin/productList');
  }


  /**
   * Change Status
   * When admin changes the status
   * @return void
   */
  public function statusChangeAction()
  {
    $ob = new App\Components\Helper;
    $ob->validateAdmin();
    $data = $this->request->getPost();
    $this->mongo->orders->updateOne(
      ["_id" => new ObjectID($data['id'])],
      ['$set' => ["status" => ($data['status'])]]
    );
    $this->response->redirect("http://" . BASE_URI . '/application/admin');
  }

  /**
   * updateProduct Action
   * When admin updates a product
   * @return void
   */
  public function updateProdAction()
  {
    $ob = new App\Components\Helper;
    $ob->validateAdmin();
    $id = $this->request->get("id");
    $result = $this->mongo->product->findOne(['_id' => new ObjectID($id)]);
    $this->view->data = (array)$result;
    if ($this->request->has('update')) {
      $res = $this->request->getPost();
      $this->mongo->product->updateOne(
        ["_id" => new ObjectID($id)],
        ['$set' => ["name" => $res['name'], "price" => $res["price"], "stock" => $res['stock']]]
      );
      $eventsManager = new EventsManager();
      $com = new \App\Components\Loader();
      $com->setEventsManager($eventsManager);
      $eventsManager->attach(
        'notifications',
        new \App\Components\NotificationsListener()
      );
      $com->processRequest();
      $this->response->redirect("http://" . BASE_URI . '/application/admin/productList');
    }
  }
}
