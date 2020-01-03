<?php

declare(strict_types=1);

namespace generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\Wood;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class ObjectOakTree extends ObjectTree{

	/** @var int */
	private $treeHeight = 7;

	public function getTrunkBlock() : int{
		return Block::LOG;
	}


	public function getLeafBlock() : int{
		return Block::LEAVES;
	}

	public function getType() : int{
		return Wood::OAK;
	}

	public function getTreeHeight() : int{
		return $this->treeHeight;
	}

	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) : void{
		$this->treeHeight = $random->nextBoundedInt(3) + 4;
		parent::placeObject($level, $x, $y, $z, $random);
	}
}