<?php

declare(strict_types=1);

namespace generator\biome;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

class BeachBiome extends SandyBiome{

	/**
	 * BeachBiome constructor.
	 */
	public function __construct(){
		$this->setGroundCover([
			BlockFactory::get(Block::SAND),
			BlockFactory::get(Block::SAND),
			BlockFactory::get(Block::SANDSTONE),
			BlockFactory::get(Block::SANDSTONE),
			BlockFactory::get(Block::SANDSTONE)
		]);

		$this->setElevation(62, 65);

		$this->temperature = 2;
		$this->rainfall = 0;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Beach";
	}
}