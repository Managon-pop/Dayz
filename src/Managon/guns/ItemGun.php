<?php

namespace Managon\guns;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\entity\Entity;
use pocketmine\item\Item;

use Managon\Dayz;
use Managon\guns\BaseGun;
use Managon\events\PlayerShootGunEvent;

class ItemGun extends BaseGun{

	private $speed = 0.6;
	private $bullets = 8;
    private $user;

	public function __construct($speed = 0.6,$bullets = 8){
		$this->speed = $speed;
		$this->bullets = $bullets;s
	}

	public function shot(){
    	$this->bullets--;
    	parent::GunCalc_Item($this,$item_id);
    	if($this->bullets === 0) $this->reload($this->getUser());
    }

    public function reload($name){
        parent::reload($name);
    }

    public function getName(){
    	return "ItemGun";
    }

    public function setUser(Player $player){
    	$this->user = $player;
    }

    public function getUser(){
        return $this->user;
    }
}