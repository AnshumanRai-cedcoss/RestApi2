<?php

namespace App\Components;
use Phalcon\Di\Injectable;

use Phalcon\Events\ManagerInterface;

/**
 * Loader Class
 */
class Loader extends Injectable
{
    protected $eventsManager;

    /**
     * setEventsManager function
     * setting hte events manager
     * @param ManagerInterface $eventsManager
     * @return void
     */
    public function setEventsManager(ManagerInterface $eventsManager)
    {
        $this->eventsManager = $eventsManager;
    }
    /**
     * process function
     * @return void
     */
    public function processRequest()
    {
        return  $this->eventsManager->fire('notifications:check', $this);
    }

    public function addNew()
    {
        return  $this->eventsManager->fire('notifications:add', $this);
    }

    public function deleteProduct()
    {
        return  $this->eventsManager->fire('notifications:delete', $this);
    }
  
   
}
