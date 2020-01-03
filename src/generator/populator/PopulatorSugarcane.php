<?php

declare(strict_types=1);

namespace generator\populator;

use generator\math\Math;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PopulatorSugarcane extends Populator{

	/** @var ChunkManager */
	private $level;
	/** @var int */
	private $randomAmount;
	/** @var int */
	private $baseAmount;

	public function setRandomAmount(int $randomAmount) : void{
		$this->randomAmount = $randomAmount;
	}

	public function setBaseAmount(int $baseAmount) : void{
		$this->baseAmount = $baseAmount;
	}

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$this->level = $level;
		$amount = $random->nextBoundedInt($this->randomAmount + 1) + $this->baseAmount;
		for($i = 0; $i < $amount; ++$i){
			$x = Math::randomRange($random, $chunkX * 16, $chunkX * 16 + 15);
			$z = Math::randomRange($random, $chunkZ * 16, $chunkZ * 16 + 15);
			$y = $this->getHighestWorkableBlock($x, $z);

			if($y != -1 && $this->canSugarcaneStay($x, $y, $z)){
				$this->level->setBlockIdAt($x, $y, $z, Block::SUGARCANE_BLOCK);
				$this->level->setBlockDataAt($x, $y, $z, 1);
			}
		}
	}

	private function getHighestWorkableBlock(int $x, int $z) : int{
		for($y = 127; $y >= 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b != Block::AIR && $b != Block::LEAVES && $b != Block::LEAVES2){
				break;
			}
		}

		return $y == 0 ? -1 : ++$y;
	}

	private function canSugarcaneStay(int $x, int $y, int $z) : bool{
		$b = $this->level->getBlockIdAt($x, $y, $z);
		$c = $this->level->getBlockIdAt($x, $y - 1, $z);
		return ($b == Block::AIR) && ($c == Block::SAND || $c == Block::GRASS) && $this->findWater($x, $y - 1, $z);
	}

	private function findWater(int $x, int $y, int $z) : bool{
		$count = 0;
		for($i = $x - 4; $i < ($x + 4); $i++){
			for($j = $z - 4; $j < ($z + 4); $j++){
				$b = $this->level->getBlockIdAt($i, $y, $j);
				if($b == Block::WATER || $b == Block::STILL_WATER){
					$count++;
				}
				if($count > 10){
					return true;
				}
			}
		}
		return ($count > 10);
	}
}