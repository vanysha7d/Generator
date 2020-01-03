<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorTallGrass;
use generator\populator\PopulatorTree;
use pocketmine\block\Sapling;

class SnowyTundraBiome extends SnowyBiome{

	public function __construct(){
		parent::__construct();

		$tallGrass = new PopulatorTallGrass;
		$tallGrass->setBaseAmount(5);
		$this->addPopulator($tallGrass);

		$tree = new PopulatorTree(Sapling::SPRUCE);
		$tree->setBaseAmount(1);
		$tree->setRandomAmount(1);
		$this->addPopulator($tree);

		$this->setElevation(63, 74);

		$this->temperature = 0;
		$this->rainfall = 0.5;
	}

	public function getName() : string{
		return "Snowy Tundra";
	}
}