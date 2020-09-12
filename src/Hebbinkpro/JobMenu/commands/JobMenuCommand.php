<?php


namespace Hebbinkpro\JobMenu\commands;

use Hebbinkpro\JobMenu\Main;
use Hebbinkpro\JobMenu\utils\JobController;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use _64FF00\PureChat\PureChat;

class JobMenuCommand extends PluginCommand {

    public function __construct(string $name, Main $main)
    {
        parent::__construct($name, $main);
        $this->main = $main;
        $this->config = $main->getConfig();

        $this->pc = $this->main->getServer()->getPluginManager()->getPlugin("PureChat");

        $this->setDescription("Edit jobs from a user");
        $this->setPermission("jm.command.jobmenu");
        $this->setPermissionMessage("§cYou don't have the permission to use this command!");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool{

        if(!$sender->hasPermission("jm.command.jobmenu")){
            $sender->sendMessage($this->getPermissionMessage());
            return true;
        }

        if(isset($args[0])){
            switch($args[0]){

                case "a":
                case "add":
                case "addjob":
                    if(!$sender->hasPermission("jm.addjob")){
                        $sender->sendMessage($this->getPermissionMessage());
                        return true;
                    }
                    if(isset($args[1]) and isset($args[2])){

                        $job = $args[2];
                        $player = $args[1];

                        if(!$this->main->getServer()->getPlayer($player)){
                            $sender->sendMessage("§c$player is not online");
                            return true;
                        }

                        if(JobController::hasJob($player, $job)){
                            $sender->sendMessage("§c$player has already that job!");
                            return true;
                        }

                        $addJob = JobController::addJob($player, $job);
                        if(!$addJob){
                            $sender->sendMessage("§c$player doesn't exists");
                            return true;
                        }
                        if($addJob === 1){
                            $sender->sendMessage("§c$player has already reached his max amount of jobs!");
                            return true;
                        }
                        if($addJob === 2){
                            $sender->sendMessage("§c$player has already this job!");
                            return true;
                        }
                        if($addJob === true){
                            $sender->sendMessage("§aThe job §e'$job'§a, is added to $player");
                            return true;
                        }
                        $sender->sendMessage("§cSomething went wrong, please try again!");
                        return true;

                    }else{
                        $sender->sendMessage("§cUsage: /jobmenu add <player> <job>");
                        return true;
                    }
                    return true;
                case "remove":
                case "removejob":
                case "deletejob":
                case "delete":
                    if(!$sender->hasPermission("jm.removejob")){
                        $sender->sendMessage($this->getPermissionMessage());
                        return true;
                    }
                    if(isset($args[1]) and isset($args[2])){
                        $player = $args[1];
                        $job = $args[2];

                        if(!$this->main->getServer()->getPlayer($player)){
                            $sender->sendMessage("§c$player is not online!");
                            return true;
                        }

                        if(JobController::hasJob($player, $job)){
                            if($job === $this->main->config->get("default_job")){
                                $sender->sendMessage("§cYou can't delete the default job!");
                                return true;
                            }
                            $remove = JobController::removeJob($player, $job);
                            if($remove){
                                $sender->sendMessage("§aThe job §e'$job'§a is removed from $player");

                                $player = $this->main->getServer()->getPlayer($player);
                                if($this->pc instanceof PureChat){
                                    if($this->pc->getPrefix($player) === $job){
                                        $this->pc->setPrefix($this->main->config->get("default_job"), $player);
                                        return true;
                                    }
                                }
                                return true;
                            }
                        }else{
                            $sender->sendMessage("§c$player doesn't have that job");
                            return true;
                        }
                    }else{
                        $sender->sendMessage("§cUsage: /jobmenu remove <player> <job>");
                        return true;
                    }
                    return true;
                case "get":
                case "getjobs":
                case "check":
                case "checkjobs":
                    if(!$sender->hasPermission("jm.checkjob")){
                        $sender->sendMessage($this->getPermissionMessage());
                        return true;
                    }
                    if(isset($args[1])){
                        $player = $args[1];
                        if(!$this->main->getServer()->getPlayer($player)){
                            $sender->sendMessage("§c$player is not online!");
                            return true;
                        }

                        $jobs = JobController::getJobs($player);
                        $msg = '';
                        $num = 0;
                        foreach($jobs as $job){
                            if($num === 0){
                                $num++;
                                $msg = $job;
                            }
                            else {
                                $msg = $msg . ", " . $job;
                            }
                        }
                        if($msg === ''){
                            $msg = "§cNo jobs";
                        }

                        $sender->sendMessage("§l§6Jobs from $player");
                        $sender->sendMessage($msg);
                        return true;
                    }else{
                        $sender->sendMessage("§cUsage: /jobmenu check <player>");
                        return true;
                    }

                    return true;

                default:
                  $sender->sendMessage("§cUsage: /jobmenu help");
                  return true;
            }
        }
        else{
            $sender->sendMessage("§cUsage: /jobmenu help");
            return true;
        }
        return false;
    }
}