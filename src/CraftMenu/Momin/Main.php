<?php

namespace CraftMenu\Momin;

//Basic Class 
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use pocketmine\item\Item;

//Guis Class 
use pocketmine\scheduler\ClosureTask;
use libs\muqsit\invmenu\InvMenu;
use libs\muqsit\invmenu\InvMenuHandler;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;

//Command Class 
use pocketmine\command\Command;
use pocketmine\command\Commandsender;

//Sound Class 
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;;

class Main extends PluginBase implements Listener {
    public function onEnable()
    {
        $this->menu= InvMenu::create(InvMenu::TYPE_CHEST);
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
    } 
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        switch($cmd->getName()){
            case "craftmenu":
                if(!$sender instanceof Player){
                    $sender->sendMessage("§c§lType In Game -_-");
                    return false;
                }
             if(!$sender->hasPermission("craftmenu.open.menu")){
                $sender->sendMessage("§c§l> §r§7You don't have permission Ask Admin Or Owner Of Server About This Problem");
                $volume = mt_rand();
              $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_ANVIL_FALL, (int) $volume);
             }
             $this->menu($sender);
             $sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_OPEN);
             break;
        }
             return true;
    }
    public function menu($sender)
    {
        $this->menu->readonly();
        $this->menu->setListener([$this, "menu1"]);
         $this->menu->setName("Craft Menu");
        $inventory = $this->menu->getInventory();
        $inventory->setItem(13, Item::get(58, 0, 1)->setCustomName("§r§eCustomCrafting\n§8click to open custom crafting table"));
        $inventory->setItem(15, Item::get(58, 0, 1)->setCustomName("§r§eNormalCrafting\n§8click to open normal crafting table"));
        
$this->menu->send($sender);
    }
    public function menu1(Player $sender, Item $item)
    {
       $hand = $sender->getInventory()->getItemInHand()->getCustomName();
        $inventory = $this->menu->getInventory();
        
        
    if($item->getId() === "160" && $item->getDamage() === "15"){
        $volume = mt_rand();
         $sender->getLevel()->broadcastLevelEvent($sender, LevelEventPacket::EVENT_SOUND_CLICK, (int) $volume);
    }
        if($item->getCustomName() === "§r§eCustomCrafting\n§8click to open custom crafting table"){
        $sender->removeWindow($inventory);
        $sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_CLOSED);
        $seconds = 2; 
          $this->getScheduler()->scheduleDelayedTask(new \pocketmine\scheduler\ClosureTask( 
                function(int $currentTick) use ($sender): void {
                    $this->getServer()->dispatchCommand($sender, "invcraft");
                    $sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_OPEN);
                }
                ), 4 * $seconds);
    }
    if($item->getCustomName() === "§r§eNormalCrafting\n§8click to open normal crafting table"){
        $sender->removeWindow($inventory);
        $sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_CLOSED);
        $seconds = 2; 
          $this->getScheduler()->scheduleDelayedTask(new \pocketmine\scheduler\ClosureTask( 
                function(int $currentTick) use ($sender): void {
                    $this->getServer()->dispatchCommand($sender, "craft");
                    $sender->getLevel()->broadcastLevelSoundEvent($sender->add(0, $sender->eyeHeight, 0), LevelSoundEventPacket::SOUND_CHEST_OPEN);
                }
                ), 4 * $seconds);
    
           }
      }
}
