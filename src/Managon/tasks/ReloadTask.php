<?php

use pocketmine\scheduler\PluginTask;

class ReloadTask extends PluginTask{

  public function __construct(PluginBase $main){
      parent::__construct($main);
      $this->main = $main;
  }
}
