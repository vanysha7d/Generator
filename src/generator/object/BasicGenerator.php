<?php

declare(strict_types=1);

namespace generator\object;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

abstract class BasicGenerator{

	public abstract function generate(ChunkManager $level, Random $random, Vector3 $position) : bool;

	protected function setBlockAndNotifyAdequately(ChunkManager $level, Vector3 $pos, Block $state) : void{
		$this->setBlock($level, $pos, $state);
	}

	protected function setBlock(ChunkManager $level, Vector3 $v, Block $b) : void{
		$level->setBlockIdAt((int) $v->x, (int) $v->y, (int) $v->z, $b->getId());
		$level->setBlockDataAt((int) $v->x, (int) $v->y, (int) $v->z, $b->getDamage());
	}
}