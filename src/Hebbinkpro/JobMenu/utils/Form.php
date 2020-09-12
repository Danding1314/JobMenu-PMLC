<?php


namespace Hebbinkpro\JobMenu\utils;

use Hebbinkpro\JobMenu\Main;
use Hebbinkpro\JobMenu\utils\JobController;

use pocketmine\Player;
use _64FF00\PureChat\PureChat;
use jojoe77777\FormAPI\SimpleForm;

class Form{

    public function sendForm(Player $player){

        $this->main = Main::getInstance();

        $this->pc = $this->main->getServer()->getPluginManager()->getPlugin("PureChat");

        $buttons = [];

        $this->jobs = JobController::getJobs($player->getName());

        foreach($this->jobs as $job){
            $button["text"] = $job;
            $buttons[] = $button;
        }

        $form = new SimpleForm(function(Player $player, $data){
            if($data !== null){

                if(isset($data)){

                    if(is_int($data)) {

                        $job = $this->jobs[$data];
                        if($this->pc instanceof PureChat){
                            if($this->pc->getPrefix($player) === $job){
                                $player->sendMessage("§cYou already selected that job!");
                                return true;
                            }
                            $this->pc->setPrefix($job, $player);
                            $player->sendMessage("§aYour prefix is changed to §e'$job'");
                            return true;
                        }
                    }
                }
            }
        });

        $form->setTitle("§a§lJob Menu");

        foreach ($buttons as $button){

            $form->addButton($button["text"]);

        }

        $player->sendForm($form);



    }

}