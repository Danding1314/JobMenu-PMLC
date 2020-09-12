<?php /** @noinspection ALL */

declare(strict_types=1);

namespace Hebbinkpro\JobMenu;

use Hebbinkpro\JobMenu\EventListener;
use Hebbinkpro\JobMenu\commands\JobMenuCommand;
use Hebbinkpro\JobMenu\commands\JobsCommand;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use SQLite3;

class Main extends PluginBase implements Listener{


    public $config;
    public $db;
    public static $instance;

    public static function getInstance() {
        return self::$instance;
    }

	public function onEnable() : void{

        self::$instance = $this;

		$this->db = new SQLite3($this->getDataFolder() . "jobs.db");
		$this->db->query("CREATE TABLE IF NOT EXISTS jobs (player_id INTEGER PRIMARY KEY AUTOINCREMENT, player_name TEXT NOT NULL, jobs TEXT DEFAULT NULL)");

		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML, [
		    "default_job" => "burger",
            "max_jobs" => 2
        ]);

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);

        $this->getServer()->getCommandMap()->register("job", new JobMenuCommand("jobmenu", $this));
        $this->getServer()->getCommandMap()->register("job", new JobsCommand("jobs", $this));
	}

}
