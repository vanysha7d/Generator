<?php

declare(strict_types=1);

namespace generator\biome;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

abstract class SnowyBiome extends NormalBiome implements CaveBiome{

	/**
	 * SnowyBiome constructor.
	 */
	public function __construct(){
		$this->setGroundCover([
			BlockFactory::get(Block::SNOW_LAYER),
			BlockFactory::get(Block::GRASS),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT)
		]);
	}

	/**
	 * @return int
	 */
	public function getSurfaceBlock() : int{
		return Block::GRASS;
	}

	/**
	 * @return int
	 */
	public function getGroundBlock() : int{
		return Block::DIRT;
	}

	/**
	 * @return int
	 */
	public function getStoneBlock() : int{
		return Block::STONE;
	}
}