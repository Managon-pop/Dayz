<?php

namespace Managon\guns;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

use Managon\Dayz as Main;
use Managon\guns\Pistol;
use Managon\guns\Rifle;
use Managon\guns\ItemGun;
use Managon\tasks\ReloadTask;
use Managon\events\PlayerShootGunEvent;

class BaseGun extends PluginBase implements Listener{

	private $guns = [];
	private $lastX,$lastY,$lastZ;

	public function __construct(Main $owner){
		$this->main = $owner;
		$this->registerGuns();
	}



	public function GunCalc(BaseGun $gun, Player $player){//Marge Beam(A little!) Beam is mine...

		$speed = $gun->speed;
		$damage = $gun->damage;

		$this->lastX = $player->x;
		$this->lastY = $player->y;
		$this->lastZ = $player->z;

		for($s = 1; $s <= 1200; $s++){
      /*
        $Ux = $speed*cos(deg2rad($pitch));
		$Uy = -9.80665*($s/240)+$speed*sin(deg2rad($pitch));
		$x = $speed*($s/240)*cos(deg2rad($pitch));
		$y = -(1/2)*9.80665*($s/240)^2+$speed*($s/240)*sin(deg2rad($pitch))+$player->y;
	  */
		$y = tan(deg2rad(-$player->getPitch()))*$speed;
        $base_t = ($speed**2 - $y ** 2) ** 0.5;
        $x = cos(deg2rad($player->getYaw()+90))*$base_t;
        $z = sin(deg2rad($player->getYaw()+90))*$base_t;

        $pos = new Vector3($this->lastX+$x,
        	               $this->lastY+$y,
        	               $this->lastZ+$z);

        if ($player->getLevel()->getBlock($pos)->getId() !== 0) {
        	break;
        }

        foreach($level->getEntities() as $p){
        if($p !== $player){
        if($pos->distance($p) < 0.2){
          $ev = new EntityDamageByEntityEvent($player,$p, 1, $damage, 0.4);
          $p->attack($damage, $ev);
        }
    }
}
        
        $this->lastX += $x;
        $this->lastY += $y;
        $this->lastZ += $z;

	}
	}

	public function GunCalc_Item(ItemGun $gun, $id){
		$v3 = new Vector3($gun->user->x,$gun->user->y+2,$gun->user->z);
		$chunk = $gun->user->getLevel()->getChunk($v3->x >> 4, $v3->z >> 4, true);
		$nbt2 = new CompoundTag("", [
										"Pos" => new ListTag("Pos", [
											new DoubleTag("", $gun->User->x),
											new DoubleTag("", $gun->User->y),
											new DoubleTag("", $gun->User->z)
										]),
										"Motion" => new ListTag("Motion", [
											new DoubleTag("", -\sin($gun->user->getYaw()) * \cos($gun->user->getPitch()/ 180 * M_PI)),
											new DoubleTag("", -\sin($gun->user->getPitch()/ 180 * M_PI)),
											new DoubleTag("", \cos($gun->user->getYaw() / 180 * M_PI) * \cos($gun->user->getPitch() / 180 * M_PI))
										]),
										"Rotation" => new ListTag("Rotation", [
											new FloatTag("", $gun->user->getYaw()),
											new FloatTag("", $gun->user->getPitch())
										]),
									]);
	$ev = new PlayerShootGunEvent($gun->user, new ITEM(262,0), Entity::createEntity("Arrow", $chunk, $nbt2, $this->user), $damage = 2);
	}

	public function registerGuns(){
        $this->guns[
         "pistol" => new Pistol(),
         "rifle" => new Rifle(),
         "itemgun" => new ItemGun();
        ];
	}

	public function reload($name){
		$gun = $this->main->getGun($name);
		$gun_name = $gun->getName();
        $class = new ReloadTask($this);
		switch($gun_name){
			case "pistol":
			   Server::getInstance()->getScheduler()->scheduleDelayedTask($class,1.8*20);
			   break;
			case "rifle":
			   Server::getInstance()->getScheduler()->scheduleDelayedTask($class,3.2*20);
				break;
			case "ItemGun":
			   Server::getInstance()->getScheduler()->scheduleDelayedTask($class,2.8*20);
		}
	}

	public function getGun($gun_name){
		return isset($this->guns[strtolower($gun_name)]) ? $this->guns[strtolower($gun_name)] : false;
	}

}