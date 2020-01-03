<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorGrass;
use generator\populator\PopulatorTallGrass;
use generator\populator\PopulatorTree;

class MountainsBiome extends GrassyBiome{

	public function __construct(){
		parent::__construct();

		$tree = new PopulatorTree;
		$tree->setBaseAmount(1);
		$this->addPopulator($tree);

		$grass = new PopulatorGrass;
		$grass->setBaseAmount(30);
		$this->addPopulator($grass);

		$tallGrass = new PopulatorTallGrass;
		$tallGrass->setBaseAmount(1);
		$this->addPopulator($tallGrass);

		$this->setElevation(63, 127);

		$this->temperature = 0.4;
		$this->rainfall = 0.5;
	}

	public function getName() : string{
		return "Mountains";
	}
}