<?php

namespace Managon\events;

use pocketmine\event\Cancellable;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\Server;
use pocketmine\entity\Living;
use pocketmine\entity\Projectile;
use pocketmine\event\player\PlayerEvent;
use pocketmine\item\Item;

class PlayerShootGunEvent extends PlayerEvent implements Cancellable{
    

    public function __construct(Player $shooter,Item $item,Projectile $torch,$damage = 1.5){
    	$this->player = $shooter;
		$this->item = $item;
		$this->projectile = $torch;
		$this->force = $damage;
    }

    public function getPlayer(){
    	return $this->player;
    }

    public function setProjectile(Entity $projectile){
		if($projectile !== $this->projectile){
				$this->projectile->kill();
				$this->projectile->close();
			}
			$this->projectile = $projectile;
		}
	}
}