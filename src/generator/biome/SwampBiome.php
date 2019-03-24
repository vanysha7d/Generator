<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorFlower;
use generator\populator\PopulatorLilyPad;
use generator\populator\tree\SwampTreePopulator;
use pocketmine\block\Block;
use pocketmine\block\Flower;

class SwampBiome extends GrassyBiome{

	/**
	 * SwampBiome constructor.
	 */
	public function __construct(){
		parent::__construct();

		$lilyPad = new PopulatorLilyPad;
		$lilyPad->setBaseAmount(4);
		$this->addPopulator($lilyPad);

		$tree = new SwampTreePopulator;
		$tree->setBaseAmount(2);
		$this->addPopulator($tree);

		$flower = new PopulatorFlower;
		$flower->setBaseAmount(2);
		$flower->addType([Block::RED_FLOWER, Flower::TYPE_BLUE_ORCHID]);
		$this->addPopulator($flower);

		$this->setElevation(62, 63);

		$this->temperature = 0.8;
		$this->rainfall = 0.9;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Swamp";
	}
}