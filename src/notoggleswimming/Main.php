<?php

namespace notoggleswimming;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerToggleSwimEvent;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener{
	private array $worlds;
	private bool $mode;

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveConfig();
		$this->reloadConfig();
		$this->initConfig();
	}

	protected function initConfig() : void{
		$config = $this->getConfig();
		$caseInsensitive = $config->get("caseInsensitive", "yes");
		$this->mode = strtolower($config->get("mode", "whitelist")) === "whitelist";
		$this->worlds = array_flip($config->get("worlds", []));
		if($caseInsensitive === "yes"||$caseInsensitive === "on"){
			$this->worlds = array_map("strtolower", $this->worlds);
		}
	}

	public function PlayerToggleSwim(PlayerToggleSwimEvent $event) : void{
		if(!$event->isSwimming()){
			return;
		}
		$player = $event->getPlayer();
		$worldName = strtolower($player->getWorld()->getFolderName());
		$canCancellable = isset($this->worlds[$worldName]);
		if($this->mode){
			//whitelist
			if(!$canCancellable){
				$event->cancel();
			}
		}else{
			//blacklist
			if($canCancellable){
				$event->cancel();
			}
		}
	}
}
