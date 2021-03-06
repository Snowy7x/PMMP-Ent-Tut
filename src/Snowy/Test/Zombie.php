<?php

declare(strict_types=1);

namespace Snowy\Test;

use pocketmine\block\BlockIds;
use pocketmine\entity\Entity;
use pocketmine\entity\Monster;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\Server;

class Zombie extends Monster
{
    public const NETWORK_ID = self::ZOMBIE;
    public $width = 0.6;
    public $height = 1.8;

    /** @var Player $owner */
    public $owner;

    public $speed = 10;

    /**
     * @return int
     */
    public function getSpeed(): int
    {
        return $this->speed;
    }

    public function __construct(Level $level, CompoundTag $nbt, Player $owner = null)
    {
        parent::__construct($level, $nbt);
        $this->owner = $owner;
    }

    public function getDrops(): array
    {
        return parent::getDrops(); // TODO: Change the autogenerated stub
    }

    public function getName(): string
    {
        return "Zombie";
    }

    public function spawnToAll(): void
    {
        parent::spawnToAll();
    }

    public function onUpdate(int $currentTick): bool
    {
        $block = $this->getLevel()->getBlock($this->add(0, 0, 1));
        if (!$this->owner){
            $this->setHealth(0);
            if ($this->isAlive()) {
                $this->kill();
                $this->close();
            }
            return false;
        }
        $this->lookAt($this->owner);
        $this->follow($this->owner);
        $this->updateMovement();
        return parent::onUpdate($currentTick); // TODO: Change the autogenerated stub
    }

    public function follow(Entity $target, float $xOffset = 0.0, float $yOffset = 0.0, float $zOffset = 0.0): void {
        $x = $target->x + $xOffset - $this->x;
        $y = $target->y + $yOffset - $this->y;
        $z = $target->z + $zOffset - $this->z;

        $xz_sq = $x * $x + $z * $z;
        $xz_modulus = sqrt($xz_sq);

        if($xz_sq < 10) {
            $this->motion->x = 0;
            $this->motion->z = 0;
        } else {
            $speed_factor = $this->getSpeed() * 0.15;
            $this->motion->x = $speed_factor * ($x / $xz_modulus);
            $this->motion->z = $speed_factor * ($z / $xz_modulus);
        }

        if((float) $y !== 0.0) {
            $this->motion->y = $this->getSpeed() * 0.25 * $y;
        }

        $this->yaw = rad2deg(atan2(-$x, $z));
        $this->pitch = rad2deg(-atan2($y, $xz_modulus));

        $this->move($this->motion->x, $this->motion->y, $this->motion->z);
    }

}