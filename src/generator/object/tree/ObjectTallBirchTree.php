<?php

declare(strict_types=1);

namespace generator\object\tree;

use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class ObjectTallBirchTree extends ObjectBirchTree{

	/**
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param Random       $random
	 */
	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) : void{
		$this->treeHeight = $random->nextBoundedInt(3) + 10;
		parent::placeObject($level, $x, $y, $z, $random);
	}
}