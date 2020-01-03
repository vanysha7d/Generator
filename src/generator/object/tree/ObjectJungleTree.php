<?php

declare(strict_types=1);

namespace generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\Wood;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class ObjectJungleTree extends ObjectTree{

	/** @var int */
	private $treeHeight = 8;

	public function getTrunkBlock() : int{
		return Block::LOG;
	}

	public function getLeafBlock() : int{
		return Block::LEAVES;
	}

	public function getType() : int{
		return Wood::JUNGLE;
	}

	public function getTreeHeight() : int{
		return $this->treeHeight;
	}

	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) : void{
		$this->treeHeight = $random->nextBoundedInt(6) + 4;
		parent::placeObject($level, $x, $y, $z, $random);
	}
}