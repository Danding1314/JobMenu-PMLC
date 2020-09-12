<?php


namespace Hebbinkpro\JobMenu\utils;

use Hebbinkpro\JobMenu\Main;

class JobController
{

    public static function createUser($player){
        $player = strtolower($player);
        $main = Main::getInstance();
        $db = $main->db;

        if(self::existsUser($player)){
            return false;
        }
        $default = $main->config->get("default_job");
        $jobs = serialize(array($default));
        $stmt = $db->prepare("INSERT INTO jobs (player_name, jobs) VALUES (:player_name, :jobs)");
        $stmt->bindValue("player_name", $player, SQLITE3_TEXT);
        $stmt->bindValue("jobs", $jobs, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();

        return true;
    }

    public static function existsUser($player){
        $player = strtolower($player);
        $main = Main::getInstance();
        $db = $main->db;

        $stmt = $db->prepare("SELECT player_name FROM jobs WHERE player_name = :player_name");
        $stmt->bindValue("player_name", $player, SQLITE3_TEXT);
        $res = $stmt->execute()->fetchArray();
        $stmt->close();

        if($res["player_name"] === $player){
            return true;
        }
        return false;

    }

    public static function addJob($player, $job){
        $player = strtolower($player);
        $main = Main::getInstance();
        $db = $main->db;

        $jobs = self::getJobs($player);
        if(!$jobs){
            return false;
        }

        if(sizeof($jobs) >= $main->config->get("max_jobs")){
            return 1;
        }

        if(in_array($job, $jobs)){
            return 2;
        }

        array_push($jobs, $job);
        $jobs = serialize($jobs);

        $stmt = $db->prepare("UPDATE jobs SET jobs = :jobs WHERE player_name = :player_name");
        $stmt->bindValue("jobs", $jobs, SQLITE3_TEXT);
        $stmt->bindValue("player_name", $player, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();

        return true;
    }

    public static function hasJob($player, $job){
        $player = strtolower($player);
        $main = Main::getInstance();
        $db = $main->db;

        if(!self::existsUser($player)){
            return false;
        }
        $jobs = self::getJobs($player);

        if(in_array($job, $jobs)){
            return true;
        }

        return false;
    }

    public static function removeJob($player, $job){
        $player = strtolower($player);
        $main = Main::getInstance();
        $db = $main->db;

        $jobs = self::getJobs($player);
        if(!in_array($job, $jobs)){
            return false;
        }

        $job = array_search($job, $jobs);
        unset($jobs[$job]);
        $jobs = serialize($jobs);

        $stmt = $db->prepare("UPDATE jobs SET jobs = :jobs WHERE player_name = :player_name");
        $stmt->bindValue("jobs", $jobs, SQLITE3_TEXT);
        $stmt->bindValue("player_name", $player, SQLITE3_TEXT);
        $stmt->execute();
        $stmt->close();

        return true;
    }

    public static function getJobs($player){
        $player = strtolower($player);
        $main = Main::getInstance();
        $db = $main->db;

        if(!self::existsUser($player)){
            return false;
        }

        $stmt = $db->prepare("SELECT jobs FROM jobs WHERE player_name = :player_name");
        $stmt->bindValue("player_name", $player, SQLITE3_TEXT);
        $res = $stmt->execute()->fetchArray();
        $stmt->close();

        $jobs = unserialize($res["jobs"]);

        return $jobs;
    }

}