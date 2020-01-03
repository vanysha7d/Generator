<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorGrass;
use generator\populator\PopulatorSugarcane;
use generator\populator\PopulatorTallGrass;
use generator\populator\PopulatorTallSugarcane;

class RiverBiome extends WateryBiome{

	public function __construct(){
		parent::__construct();

		$sugarcane = new PopulatorSugarcane;
		$sugarcane->setBaseAmount(6);
		$this->addPopulator($sugarcane);

		$tallSugarcane = new PopulatorTallSugarcane;
		$tallSugarcane->setBaseAmount(60);
		$this->addPopulator($tallSugarcane);

		$grass = new PopulatorGrass;
		$grass->setBaseAmount(30);
		$this->addPopulator($grass);

		$tallGrass = new PopulatorTallGrass;
		$tallGrass->setBaseAmount(5);
		$this->addPopulator($tallGrass);

		$this->setElevation(58, 62);

		$this->temperature = 0.5;
		$this->rainfall = 0.7;
	}

	public function getName() : string{
		return "River";
	}
}