<?php

namespace Managon;

use pocketmine\Player;
use pocketmine\Server;


use pocketmine\level\Level;
use pocketmine\block\Block;
use pocketmine\math\Vector3;


use pocketmine\event\Listener;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerItemHeldEvent;

use pocketmine\event\server\DataPacketRecieveEvent;


use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;


use Managon\guns\BaseGun as Guns;
use Managon\RandomChest\Items;
use Managon\Message;


class Dayz extends PluginBase implements Listener{

	public $players;

	public $wingSpeed = 0;
	public $wingType = 1;

	private $foo = true;

	public $gun = [];//$this->gun[$name] = [$gun_name]

	public $lang = [];

	public function onEnable(){
		Server::getInstance()->getPluginManager()->registerEvents($this,$this);

		if(!file_exists($this->getDataFolder())){
        @mkdir($this->getDataFolder(), 0744, true);
        }

        $this->con = new Config($this->getDataFolder(). "Config.json", Config::JSON, array());
        $this->lv = new Config($this->getDataFolder(). "lv.yml", Config::YAML, array());
        $this->exp = new Config($this->getDataFolder(). "exp.yml", Config::YAML, array());

        $this->country = new Config($this->getDataFolder(). "country.yml", Config::YAML, array(
        	"ja" => "ja",
        	"en" => "en",
        	"ch" => "ch"));//今は3ヶ国語


		$this->Guns = new Guns();
		$this->Items = new Items();
		$this->Message = new Message();
	}

	//=============================================Main===========================================================

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();

		$this->players = [$player->getName()];

		$ip = $player->getAddress();
        $location = json_decode(file_get_contents('http://ip-api.com/json/', $ip));
        $country = $location->countryCode;

        $player->country = $country;

        if(!$this->country->exists($country)){
        	$player->country = "en";
        }

        $this->lang[$player->getName()] = ($this->getDataFolder()."resources/".$country.".json");

        $this->Message->sendWelcomeMessage($player);
	}

	public function onDeath(PlayerDeathEvent $event){
		$player = $event->getEntity();

		if($player instanceof Player){

           $this->Message->sendDeathMessage($player);

           $player->teleport();//要追加
		}
	}

	public function onQuit(PlayerQuitEvent $event){
		unset($this->players[$event->getPlayer()->getName()]);
        $this->Message->sendQuitMessage($event->getPlayer()->getName());
	}

	public function onHeld(PlayerItemHeldEvent $event){
		$id = $event->item->getId();
		switch ($id) {
			case 292:
				$this->gun[$event->getPlayer()->getName()] = $this->Guns->getGun("pistol");
				break;

			case 336:
				$this->gun[$event->getPlayer()->getName()] = $this->Guns->getGun("rifle");
				break;

			case 369:
				$this->gun[$event->getPlayer()->getName()] = $this->Guns->getGun("torchGun");
				break;
		}
	}

	//==============================================API===========================================================
    
    public function getPlayers(){
    	return $this->players;
    }
    

	/*public function getChestItems(){
		$items = $this->items->chooseItems();
		return $items;
	}*/

	public function getWingSpeed(){//風
		return $this->wingSpeed;
	}

	public function setWingType(){//風の向き
		$this->wingType = mt_rand(1,4);
	}


	public function getNextExp($lv){
		if($lv === 1) return 10;
		return ($lv*$lv-$lv)*10;
	}


	public function addExp($name, $exp){//経験値関数

		$expCalc = $exp + $this->exp->get($name);

		$nextExp = $this->getNextExp($this->lv->get($name));

		if($expCalc > $nextExp){

           $this->lvup($name, 1);

           $remainder = $expCalc - $nextExp;//余ったExp

           $this->addExp($name, $remainder);

		}else{
           $this->exp->set($name, $expCalc);
		}
	}

	public function lvup($name, $value){
        $this->lv->set($name, $value + $this->lv->get($name));
	}

	public function getGun($name){
		return $this->gun[$name];
	}


	public function display(){
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
        if($player->getInventory()->getItemInHand()->getId() === 292 ||
           $player->getInventory()->getItemInHand()->getId() === 336 ||
           $player->getInventory()->getItemInHand()->getId() === 369){
        	$gun = $this->gun[$player->getName()];
        	$bullets = $gun->bullets;
        	$max = $gun->max;
        	$player->sendPopup($bullets ."/". $max);
        }
        }
	}
}