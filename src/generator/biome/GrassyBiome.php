<?php

declare(strict_types=1);

namespace generator\biome;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

abstract class GrassyBiome extends NormalBiome implements CaveBiome{

	/**
	 * GrassyBiome constructor.
	 */
	public function __construct(){
		$this->setGroundCover([
			BlockFactory::get(Block::GRASS),
			BlockFactory::get(Block::DIRT),
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