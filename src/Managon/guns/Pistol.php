<?php

use Managon\Dayz as Main;
use Managon\guns\BaseGun;
use pocketmine\Player;

class Pistol extends BaseGun{

	public $speed = 0.8;
	public $bullets = 6;
    public $max = 48;
	public $user;

	public function __construct($speed = 0.8,$bullets = 6);
	   $this->speed = $speed;
	   $this->bullets = $bullets;

    }

    public function shot(){
    	$this->bullets--;
    	parent::GunCalc($this,$this->user);
    	if($this->bullets === 0) $this->reload($this->getUser());
    }

    public function reload($name){
        parent::reload($name);
    }

    public function getName(){
    	return "pistol";
    }

    public function setUser(Player $player){
    	$this->user = $player;
    }

    public function getUser(Player $player)
        return $this->user;
}