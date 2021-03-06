<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\commands\subcommands\roles;

use CortexPE\Commando\args\TextArgument;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyFactions\commands\subcommands\FactionSubCommand;
use DaPigGuy\PiggyFactions\event\role\FactionRoleChangeEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\language\LanguageManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use DaPigGuy\PiggyFactions\utils\Roles;
use pocketmine\Player;

class DemoteSubCommand extends FactionSubCommand
{

    public function onNormalRun(Player $sender, ?Faction $faction, FactionsPlayer $member, string $aliasUsed, array $args): void
    {
        $targetMember = $faction->getMember($args["name"]);
        if ($targetMember === null) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.member-not-found", ["{PLAYER}" => $args["name"]]);
            return;
        }
        if (Roles::ALL[$targetMember->getRole()] >= Roles::ALL[$member->getRole()] && !$member->isInAdminMode()) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.demote.cant-demote-higher", ["{PLAYER}" => $targetMember->getUsername()]);
            return;
        }
        $currentRole = $targetMember->getRole();
        if ($currentRole === Roles::RECRUIT) {
            LanguageManager::getInstance()->sendMessage($sender, "commands.demote.already-lowest", ["{PLAYER}" => $targetMember->getUsername()]);
            return;
        }
        $ev = new FactionRoleChangeEvent($faction, $targetMember, $currentRole, ($role = array_keys(Roles::ALL)[Roles::ALL[$currentRole] - 2]));
        $ev->call();
        if ($ev->isCancelled()) return;
        $targetMember->setRole($role);
        LanguageManager::getInstance()->sendMessage($sender, "commands.demote.success", ["{PLAYER}" => $targetMember->getUsername(), "{ROLE}" => $role]);
        if (($player = $this->plugin->getServer()->getPlayerByUUID($targetMember->getUuid())) !== null) LanguageManager::getInstance()->sendMessage($player, "commands.demote.demoted", ["{ROLE}" => $role]);
    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->registerArgument(0, new TextArgument("name"));
    }
}