<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\relations;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationEvent;
use DaPigGuy\PiggyFactions\event\relation\FactionRelationWishEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Relations;
use pocketmine\Player;

class AllySubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetFaction = FactionsManager::getInstance()->getFactionByName($args["faction"]);
        if ($targetFaction === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.invalid-faction", ["{FACTION}" => $args["faction"]]);
            return;
        }
        if ($targetFaction->getId() === $faction->getId()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.ally.self");
            return;
        }
        if ($targetFaction->isAllied($faction)) {
            LanguageManager::getInstance()->sendMessage($sender, "already-allied");
            return;
        }
        if ($targetFaction->getRelationWish($faction) === Relations::ALLY) {
            $ev = new FactionRelationEvent([$faction, $targetFaction], Relations::ALLY);
            $ev->call();
            if ($ev->isCancelled()) return;

            $targetFaction->revokeRelationWish($faction);
            $faction->setRelation($targetFaction, Relations::ALLY);
            $targetFaction->setRelation($faction, Relations::ALLY);
            foreach ($faction->getOnlineMembers() as $p) {
                LanguageManager::getInstance()->sendMessage($p, "commands.ally.allied", ["{ALLY}" => $targetFaction->getName()]);
            }
            foreach ($targetFaction->getOnlineMembers() as $p) {
                LanguageManager::getInstance()->sendMessage($p, "commands.ally.allied", ["{ALLY}" => $faction->getName()]);
            }
            return;
        }
        $ev = new FactionRelationWishEvent($faction, $targetFaction, Relations::ALLY);
        $ev->call();
        if ($ev->isCancelled()) return;

        $faction->setRelationWish($targetFaction, Relations::ALLY);
        LanguageManager::getInstance()->sendMessage($sender, "commands.ally.success", ["{FACTION}" => $targetFaction->getName()]);
        foreach ($targetFaction->getOnlineMembers() as $p) {
            LanguageManager::getInstance()->sendMessage($p, "commands.ally.request", ["{FACTION}" => $faction->getName()]);
        }
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("faction"));
    }
}