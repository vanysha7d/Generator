<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorSugarcane;
use generator\populator\PopulatorTallSugarcane;

class OceanBiome extends WateryBiome{

	public function __construct(){
		parent::__construct();

		$sugarcane = new PopulatorSugarcane;
		$sugarcane->setBaseAmount(6);
		$this->addPopulator($sugarcane);

		$tallSugarcane = new PopulatorTallSugarcane;
		$tallSugarcane->setBaseAmount(60);
		$this->addPopulator($tallSugarcane);

		$this->setElevation(46, 58);

		$this->temperature = 0.5;
		$this->rainfall = 0.5;
	}

	public function getGroundCover() : array{
		return parent::getGroundCover();
	}

	public function getName() : string{
		return "Ocean";
	}
}