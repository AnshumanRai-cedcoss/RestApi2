<?php

namespace App\Components;
use Phalcon\Di\Injectable;

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
        return  $this->eventsManager->fire('notifications:check', $this);
    }
  
   
}