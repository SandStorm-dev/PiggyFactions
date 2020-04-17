<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use pocketmine\Player;

class LeaderSubCommand extends FactionSubCommand
{
    public function onNormalRun(Player $sender, ?Faction $faction, string $aliasUsed, array $args): void
    {
        if ($faction->getMember($sender->getName())->getRole() !== Faction::ROLE_LEADER) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.not-leader");
            return;
        }
        $member = $faction->getMember($args["name"]);
        if ($member === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        $player = $this->plugin->getServer()->getPlayerByUUID($member->getUuid());
        if ($player === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.leader.offline");
            return;
        }
        $faction->setLeader($member->getUuid());
        $faction->getMember($sender->getName())->setRole(Faction::ROLE_MEMBER);
        $member->setRole(Faction::ROLE_LEADER);
        LanguageManager::getInstance()->sendMessage($player, "commands.leader.recipient");
        LanguageManager::getInstance()->sendMessage($sender, "commands.leader.success", ["{PLAYER}" => $player->getName()]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}