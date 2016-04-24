<?php

use pocketmine\scheduler\PluginTask;
use Managon\Dayz;

class ReloadTask extends PluginTask{

  public function __construct(PluginBase $main,$name){
      parent::__construct($main);
      $this->main = $main;
      $this->name = $name;
  }
  
  public function onRun($tick){
    $dayz = new Dayz;
    $gun = $dayz->getGun($this->name);
    $gun->reload();
  }
}
