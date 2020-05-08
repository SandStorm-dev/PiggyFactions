<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\event\management;

use DaPigGuy\PiggyFactions\event\FactionEvent;
use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\logs\FactionLog;
use DaPigGuy\PiggyFactions\logs\LogsManager;
use DaPigGuy\PiggyFactions\players\FactionsPlayer;
use pocketmine\event\Cancellable;
use pocketmine\Player;

class FactionInviteEvent extends FactionEvent implements Cancellable
{
    /** @var FactionsPlayer */
    private $invitedBy;
    /** @var Player */
    private $invited;

    public function __construct(Faction $faction, FactionsPlayer $invitedBy, Player $invited)
    {
        parent::__construct($faction);
        $this->invitedBy = $invitedBy;
        $this->invited = $invited;
    }

    public function call(): void
    {
        $factionLog = new FactionLog(FactionLog::INVITE, ["invitedBy" => $this->getInvitedBy()->getUsername(), "invited" => $this->getInvited()->getName()]);
        LogsManager::getInstance()->addFactionLog($this->getFaction(), $factionLog);
        parent::call();
    }

    public function getInvitedBy(): FactionsPlayer
    {
        return $this->invitedBy;
    }

    public function getInvited(): Player
    {
        return $this->invited;
    }
}