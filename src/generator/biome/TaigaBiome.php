<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorGrass;
use generator\populator\PopulatorTallGrass;
use generator\populator\PopulatorTree;
use pocketmine\block\Sapling;

class TaigaBiome extends SnowyBiome{

	/**
	 * TaigaBiome constructor.
	 */
	public function __construct(){
		parent::__construct();

		$grass = new PopulatorGrass;
		$grass->setBaseAmount(6);
		$this->addPopulator($grass);

		$tree = new PopulatorTree(Sapling::SPRUCE);
		$tree->setBaseAmount(10);
		$this->addPopulator($tree);

		$tallGrass = new PopulatorTallGrass;
		$tallGrass->setBaseAmount(1);
		$this->addPopulator($tallGrass);

		$this->setElevation(63, 81);

		$this->temperature = 0.05;
		$this->rainfall = 0.8;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "Taiga";
	}
}