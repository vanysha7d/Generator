<?php

declare(strict_types=1);

namespace generator\biome;

use generator\populator\PopulatorGrass;
use generator\populator\PopulatorTallGrass;
use generator\populator\PopulatorTree;
use pocketmine\block\Sapling;

class ForestBiome extends GrassyBiome{

	public const TYPE_NORMAL = 0;
	public const TYPE_BIRCH = 1;

	/** @var int */
	public $type;

	/**
	 * ForestBiome constructor.
	 * @param int $type
	 */
	public function __construct(int $type = self::TYPE_NORMAL){
		parent::__construct();

		$this->type = $type;

		$tree = new PopulatorTree($type == self::TYPE_BIRCH ? Sapling::BIRCH : Sapling::OAK);
		$tree->setBaseAmount(5);
		$this->addPopulator($tree);

		$grass = new PopulatorGrass;
		$grass->setBaseAmount(30);
		$this->addPopulator($grass);

		$tallGrass = new PopulatorTallGrass;
		$tallGrass->setBaseAmount(3);
		$this->addPopulator($tallGrass);

		$this->setElevation(63, 81);

		if($type == self::TYPE_BIRCH){
			$this->temperature = 0.5;
			$this->rainfall = 0.5;
		}else{
			$this->temperature = 0.7;
			$this->temperature = 0.8;
		}
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->type == self::TYPE_BIRCH ? "Birch Forest" : "Forest";
	}
}