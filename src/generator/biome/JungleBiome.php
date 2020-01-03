<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorGrass;
use generator\populator\tree\JungleBigTreePopulator;
use generator\populator\tree\JungleTreePopulator;

class JungleBiome extends GrassyBiome{

	public function __construct(){
		parent::__construct();

		$tree = new JungleTreePopulator;
		$tree->setBaseAmount(10);
		$this->addPopulator($tree);

		$bigTree = new JungleBigTreePopulator;
		$bigTree->setBaseAmount(6);
		$this->addPopulator($bigTree);

		$grass = new PopulatorGrass;
		$grass->setBaseAmount(20);
		$this->addPopulator($grass);

		$this->setElevation(62, 63);

		$this->temperature = 1.2;
		$this->rainfall = 0.9;
	}

	public function getName() : string{
		return "Jungle";
	}
}