<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorCactus;
use generator\populator\PopulatorDeadBush;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

abstract class SandyBiome extends NormalBiome implements CaveBiome{

	public function __construct(){
		$this->setGroundCover([
			BlockFactory::get(Block::SAND),
			BlockFactory::get(Block::SAND),
			BlockFactory::get(Block::SANDSTONE),
			BlockFactory::get(Block::SANDSTONE),
			BlockFactory::get(Block::SANDSTONE)
		]);

		$cactus = new PopulatorCactus;
		$cactus->setBaseAmount(2);
		$this->addPopulator($cactus);

		$deadBush = new PopulatorDeadBush;
		$deadBush->setBaseAmount(2);
		$this->addPopulator($deadBush);
	}

	public function getSurfaceBlock() : int{
		return Block::SAND;
	}

	public function getGroundBlock() : int{
		return Block::SAND;
	}

	public function getStoneBlock() : int{
		return Block::SANDSTONE;
	}
}