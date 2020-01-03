<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\MushroomPopulator;
use generator\populator\PopulatorFlower;
use generator\populator\PopulatorGrass;
use generator\populator\tree\DarkOakTreePopulator;

class DarkForestHillsBiome extends GrassyBiome{

	public function __construct(){
		parent::__construct();

		$tree = new DarkOakTreePopulator;
		$tree->setBaseAmount(20);
		$this->addPopulator($tree);

		$grass = new PopulatorGrass;
		$grass->setBaseAmount(10);
		$this->addPopulator($grass);

		$flower = new PopulatorFlower;
		$flower->setBaseAmount(2);
		$this->addPopulator($flower);

		$mushroom = new MushroomPopulator;
		$mushroom->setBaseAmount(1);
		$mushroom->setRandomAmount(1);
		$this->addPopulator($mushroom);

		$this->setElevation(63, 127);

		$this->temperature = 0.7;
		$this->rainfall = 0.8;
	}

	public function getName() : string{
		return "Dark Forest Hills";
	}
}