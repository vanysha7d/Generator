<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorFlower;
use generator\populator\PopulatorGrass;
use generator\populator\PopulatorTallGrass;
use generator\populator\tree\SavannaTreePopulator;
use pocketmine\block\Sapling;

class SavannaBiome extends GrassyBiome{

	/**
	 * SavannaBiome constructor.
	 */
	public function __construct(){
		parent::__construct();

		$tree = new SavannaTreePopulator(Sapling::ACACIA);
		$tree->setBaseAmount(1);
		$this->addPopulator($tree);

		$tallGrass = new PopulatorTallGrass;
		$tallGrass->setBaseAmount(20);
		$this->addPopulator($tallGrass);

		$grass = new PopulatorGrass;
		$grass->setBaseAmount(20);
		$this->addPopulator($grass);

		$flower = new PopulatorFlower;
		$flower->setBaseAmount(4);
		$this->addPopulator($flower);

		$this->setElevation(62, 68);

		$this->temperature = 1.2;
		$this->rainfall = 0.0;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Savanna";
	}
}