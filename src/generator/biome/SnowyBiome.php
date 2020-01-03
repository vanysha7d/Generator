<?php

declare(strict_types=1);

namespace generator\biome;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

abstract class SnowyBiome extends NormalBiome implements CaveBiome{

	public function __construct(){
		$this->setGroundCover([
			BlockFactory::get(Block::SNOW_LAYER),
			BlockFactory::get(Block::GRASS),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT)
		]);
	}

	public function getSurfaceBlock() : int{
		return Block::GRASS;
	}

	public function getGroundBlock() : int{
		return Block::DIRT;
	}

	public function getStoneBlock() : int{
		return Block::STONE;
	}
}