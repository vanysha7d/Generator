<?php

declare(strict_types=1);

namespace generator\object\tree;

use generator\object\BasicGenerator;
use pocketmine\block\Dirt;
use pocketmine\item\Item;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;

abstract class TreeGenerator extends BasicGenerator{

	/**
	 * @param int $id
	 * @return bool
	 */
	protected function canGrowInto(int $id) : bool{
		return $id == Item::AIR || $id == Item::LEAVES || $id == Item::GRASS || $id == Item::DIRT || $id == Item::LOG || $id == Item::LOG2 || $id == Item::SAPLING || $id == Item::VINE;
	}

	/**
	 * @param ChunkManager $level
	 * @param Vector3      $pos
	 */
	protected function setDirtAt(ChunkManager $level, Vector3 $pos) : void{
		if($level->getBlockIdAt((int) $pos->x, (int) $pos->y, (int) $pos->z) != Item::DIRT){
			$this->setBlockAndNotifyAdequately($level, $pos, new Dirt);
		}
	}
}