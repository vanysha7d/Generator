<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorFlower;
use generator\populator\PopulatorGrass;
use generator\populator\PopulatorSugarcane;
use generator\populator\PopulatorTallGrass;
use generator\populator\PopulatorTallSugarcane;
use pocketmine\block\Block;
use pocketmine\block\Flower;

class PlainBiome extends GrassyBiome{

	public function __construct(){
		parent::__construct();

		$sugarcane = new PopulatorSugarcane;
		$sugarcane->setBaseAmount(6);
		$this->addPopulator($sugarcane);

		$tallSugarcane = new PopulatorTallSugarcane;
		$tallSugarcane->setBaseAmount(60);
		$this->addPopulator($tallSugarcane);

		$grass = new PopulatorGrass;
		$grass->setBaseAmount(40);
		$this->addPopulator($grass);

		$tallGrass = new PopulatorTallGrass;
		$tallGrass->setBaseAmount(7);
		$this->addPopulator($tallGrass);

		$flower = new PopulatorFlower;
		$flower->setBaseAmount(10);
		$flower->addType([Block::DANDELION, 0]);
		$flower->addType([Block::RED_FLOWER, Flower::TYPE_POPPY]);
		$flower->addType([Block::RED_FLOWER, Flower::TYPE_AZURE_BLUET]);
		$flower->addType([Block::RED_FLOWER, Flower::TYPE_RED_TULIP]);
		$flower->addType([Block::RED_FLOWER, Flower::TYPE_ORANGE_TULIP]);
		$flower->addType([Block::RED_FLOWER, Flower::TYPE_WHITE_TULIP]);
		$flower->addType([Block::RED_FLOWER, Flower::TYPE_PINK_TULIP]);
		$flower->addType([Block::RED_FLOWER, Flower::TYPE_OXEYE_DAISY]);
		$this->addPopulator($flower);

		$this->setElevation(63, 74);

		$this->temperature = 0.8;
		$this->rainfall = 0.4;
	}

	public function getName() : string{
		return "Plains";
	}
}