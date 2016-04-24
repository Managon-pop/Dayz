<?php

namespace Managon;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;

use Managon\Dayz;

class Message extends PluginBase{

    private $lang = [];

    
    public function sendWelcomeMessage(Player $player){
        $messages = $this->getLang($player);

    	$welcomeMessage = $messages["welcome"];

    	$player->sendMessage("§b".$welcomeMessage);
    }

    public function sendDeathMessage(Player $player){
    	$messages = $this->getLang($player);

    	$deathMessage = $messages["death"];

    	$player->sendMessage("§c".$deathMessage);
    }

    public function sendQuitMessage($name){//$name=Quit Player's Name
        foreach (Server::getInstance()->getOnlinelayers() as $player) {
        	$messages = $this->getLang($player);

    	    $quitMessage = $messages["quit"];

    	    $player->sendMessage($name.$quiMessage);
        }
    	
    }

    public function Message_decode($file){
        $messages = file_get_contents($file);
        $json = json_decode($messages,true);
        return $json;
    }

    public function getLang(Player $player){
        $api = new Dayz;
        $file = $api->lang[$player->getName()];
        $json = $this->Message_decode($file);
        return $json;
    }

}