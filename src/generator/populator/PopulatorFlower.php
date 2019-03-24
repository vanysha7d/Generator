<?php

declare(strict_types=1);

namespace generator\populator;

use pocketmine\block\Block;
use pocketmine\block\Flower;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PopulatorFlower extends Populator{

	/** @var ChunkManager */
	private $level;
	/** @var int */
	private $randomAmount;
	/** @var int */
	private $baseAmount;

	/** @var array */
	private $flowerTypes = [];

	/**
	 * @param int $randomAmount
	 */
	public function setRandomAmount(int $randomAmount) : void{
		$this->randomAmount = $randomAmount;
	}

	/**
	 * @param int $baseAmount
	 */
	public function setBaseAmount(int $baseAmount) : void{
		$this->baseAmount = $baseAmount;
	}

	/**
	 * @param array $type
	 */
	public function addType(array $type) : void{
		$this->flowerTypes[] = $type;
	}

	/**
	 * @return array
	 */
	public function getTypes() : array{
		return $this->flowerTypes;
	}

	/**
	 * @param ChunkManager $level
	 * @param int          $chunkX
	 * @param int          $chunkZ
	 * @param Random       $random
	 */
	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$this->level = $level;
		$amount = $random->nextBoundedInt($this->randomAmount + 1) + $this->baseAmount;

		if(count($this->flowerTypes) === 0){
			$this->addType([Block::DANDELION, 0]);
			$this->addType([Block::RED_FLOWER, Flower::TYPE_POPPY]);
		}
		$endNum = count($this->flowerTypes) - 1;
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
			$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y !== -1 and $this->canFlowerStay($x, $y, $z)){
				$type = mt_rand(0, $endNum);
				$this->level->setBlockIdAt($x, $y, $z, $this->flowerTypes[$type][0]);
				$this->level->setBlockDataAt($x, $y, $z, $this->flowerTypes[$type][1]);
			}
		}
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @return bool
	 */
	private function canFlowerStay(int $x, int $y, int $z) : bool{
		$b = $this->level->getBlockIdAt($x, $y, $z);
		return ($b == Block::AIR || $b == Block::SNOW_LAYER) && $this->level->getBlockIdAt($x, $y - 1, $z) == Block::GRASS;
	}

	/**
	 * @param int $x
	 * @param int $z
	 * @return int
	 */
	private function getHighestWorkableBlock(int $x, int $z) : int{
		for($y = 127; $y >= 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b != Block::AIR && $b != Block::LEAVES && $b != Block::LEAVES2 && $b != Block::SNOW_LAYER){
				break;
			}
		}

		return $y == 0 ? -1 : ++$y;
	}
}