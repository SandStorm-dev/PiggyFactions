<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyFactions\claims;

use DaPigGuy\PiggyFactions\factions\Faction;
use DaPigGuy\PiggyFactions\factions\FactionsManager;
use DaPigGuy\PiggyFactions\PiggyFactions;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;

class Claim
{
    /** @var int */
    private $id;
    /** @var int */
    private $faction;
    /** @var int */
    private $chunkX;
    /** @var int */
    private $chunkZ;
    /** @var string */
    private $level;

    public function __construct(int $id, int $faction, int $chunkX, int $chunkZ, string $level)
    {
        $this->id = $id;
        $this->faction = $faction;
        $this->chunkX = $chunkX;
        $this->chunkZ = $chunkZ;
        $this->level = $level;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFaction(): ?Faction
    {
        return FactionsManager::getInstance()->getFaction($this->faction);
    }

    public function getLevel(): ?Level
    {
        return PiggyFactions::getInstance()->getServer()->getLevelByName($this->level);
    }

    public function getChunk(): ?Chunk
    {
        $level = PiggyFactions::getInstance()->getServer()->getLevelByName($this->level);
        if ($level === null) return null;
        return $level->getChunk($this->chunkX, $this->chunkZ);
    }
}