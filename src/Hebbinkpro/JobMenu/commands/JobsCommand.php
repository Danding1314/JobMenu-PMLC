<?php


namespace Hebbinkpro\JobMenu\commands;

use Hebbinkpro\BeautifulParticles\utils\Form as particleForm;
use Hebbinkpro\JobMenu\Main;
use Hebbinkpro\JobMenu\utils\Form;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\Player;

class JobsCommand extends PluginCommand {

    public function __construct(string $name, Main $main) {
        parent::__construct($name, $main);
        $this->main = $main;
        $this->config = $main->getConfig();

        $this->setDescription("List of jobs you can use");
        $this->setPermission("jm.command.jobs");

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{

        if(!$sender instanceof Player){
            $sender->sendMessage("§cYou must run this command in-game!");
            return true;
        }

        $menu = new Form();
        $send = $menu->sendForm($sender);


        return true;
    }
}