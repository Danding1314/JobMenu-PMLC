<?php


namespace Hebbinkpro\JobMenu;

use Hebbinkpro\JobMenu\utils\JobController;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\Listener;
use _64FF00\PureChat\PureChat;


class EventListener implements Listener {

    public function __construct($main){
        $this->main = $main;
        $this->pc = $main->getServer()->getPluginManager()->getPlugin("PureChat");
    }

    public function onJoin(PlayerJoinEvent $e){

        $player = $e->getPlayer();
        if(!JobController::existsUser($player->getName())){
            JobController::createUser($player->getName());
        }

        if($this->pc instanceof PureChat){
            $prefix = $this->pc->getPrefix($player);
            if($prefix === null){
                $this->pc->setPrefix($this->main->config->get("default_job"), $player);
            }
            if(!JobController::hasJob($player->getName(), $prefix)){
                $this->pc->setPrefix($this->main->config->get("default_job"), $player);
            }
        }

    }

}