<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\MushroomPopulator;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

class MushroomFieldsBiome extends NormalBiome implements CaveBiome{

	/**
	 * MushroomFieldsBiome constructor.
	 */
	public function __construct(){
		$this->setGroundCover([
			BlockFactory::get(Block::MYCELIUM),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT),
			BlockFactory::get(Block::DIRT)
		]);

		$mushroom = new MushroomPopulator;
		$mushroom->setBaseAmount(1);
		$this->addPopulator($mushroom);

		$this->setElevation(60, 70);

		$this->temperature = 0.9;
		$this->rainfall = 1.0;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Mushroom Fields";
	}

	/**
	 * @return int
	 */
	public function getSurfaceBlock() : int{
		return Block::MYCELIUM;
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