<?php

declare(strict_types=1);

namespace generator\biome;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

abstract class WateryBiome extends NormalBiome implements CaveBiome{

	public function __construct(){
		$this->setGroundCover([
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT)
		]);
	}

	public function getSurfaceBlock() : int{
		return Block::DIRT;
	}

	public function getGroundBlock() : int{
		return Block::DIRT;
	}

	public function getStoneBlock() : int{
		return Block::STONE;
	}
}