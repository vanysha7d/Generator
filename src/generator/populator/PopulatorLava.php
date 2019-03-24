<?php

declare(strict_types=1);

namespace generator\populator;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PopulatorLava extends Populator{

	/** @var ChunkManager */
	private $level;
	/** @var int */
	private $randomAmount;
	/** @var int */
	private $baseAmount;
	/** @var Random */
	private $random;

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
	 * @param ChunkManager $level
	 * @param int          $chunkX
	 * @param int          $chunkZ
	 * @param Random       $random
	 */
	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$this->random = $random;
		if($random->nextRange(0, 100) < 5){
			$this->level = $level;
			$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
			$chunk = $level->getChunk($chunkX, $chunkZ);
			$bx = $chunkX << 4;
			$bz = $chunkZ << 4;
			for($i = 0; $i < $amount; ++$i){
				$x = $random->nextRange(0, 15);
				$z = $random->nextRange(0, 15);
				$y = $this->getHighestWorkableBlock($chunk, $x, $z);
				if($y != -1 && $chunk->getBlockId($x, $y, $z) == Block::AIR){
					$chunk->setBlock($x, $y, $z, Block::LAVA);
					$chunk->setBlockLight($x, $y, $z, BlockFactory::$light[Block::LAVA]);
					$this->lavaSpread($bx + $x, $y, $bz + $z);
				}
			}
		}
	}

	/**
	 * @param int $x1
	 * @param int $y1
	 * @param int $z1
	 * @param int $x2
	 * @param int $y2
	 * @param int $z2
	 * @return int
	 */
	private function getFlowDecay(int $x1, int $y1, int $z1, int $x2, int $y2, int $z2) : int{
		if($this->level->getBlockIdAt($x1, $y1, $z1) != $this->level->getBlockIdAt($x2, $y2, $z2)){
			return -1;
		}else{
			return $this->level->getBlockDataAt($x2, $y2, $z2);
		}
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 */
	private function lavaSpread(int $x, int $y, int $z) : void{
		if($this->level->getChunk($x >> 4, $z >> 4) == null){
			return;
		}
		$decay = $this->getFlowDecay($x, $y, $z, $x, $y, $z);
		$multiplier = 2;
		if($decay > 0){
			$smallestFlowDecay = -100;
			$smallestFlowDecay = $this->getSmallestFlowDecay($x, $y, $z, $x, $y, $z - 1, $smallestFlowDecay);
			$smallestFlowDecay = $this->getSmallestFlowDecay($x, $y, $z, $x, $y, $z + 1, $smallestFlowDecay);
			$smallestFlowDecay = $this->getSmallestFlowDecay($x, $y, $z, $x - 1, $y, $z, $smallestFlowDecay);
			$smallestFlowDecay = $this->getSmallestFlowDecay($x, $y, $z, $x + 1, $y, $z, $smallestFlowDecay);
			$k = $smallestFlowDecay + $multiplier;
			if($k >= 8 || $smallestFlowDecay < 0){
				$k = -1;
			}
			$topFlowDecay = $this->getFlowDecay($x, $y, $z, $x, $y + 1, $z);
			if($topFlowDecay >= 0){
				if($topFlowDecay >= 8){
					$k = $topFlowDecay;
				}else{
					$k = $topFlowDecay | 0x08;
				}
			}
			if($decay < 8 && $k < 8 && $k > 1 && $this->random->nextRange(0, 4) != 0){
				$k = $decay;
			}
			if($k != $decay){
				$decay = $k;
				if($decay < 0){
					$this->level->setBlockIdAt($x, $y, $z, 0);
				}else{
					$this->level->setBlockIdAt($x, $y, $z, Block::LAVA);
					$this->level->setBlockDataAt($x, $y, $z, $decay);
					$this->lavaSpread($x, $y, $z);
					return;
				}
			}
		}
		if($this->canFlowInto($x, $y - 1, $z)){
			if($decay >= 8){
				$this->flowIntoBlock($x, $y - 1, $z, $decay);
			}else{
				$this->flowIntoBlock($x, $y - 1, $z, $decay | 0x08);
			}
		}else{
			if($decay >= 0 && ($decay == 0 || !$this->canFlowInto($x, $y - 1, $z))){
				$flags = $this->getOptimalFlowDirections($x, $y, $z);
				$l = $decay + $multiplier;
				if($decay >= 8){
					$l = 1;
				}
				if($l >= 8){
					return;
				}
				if($flags[0]){
					$this->flowIntoBlock($x - 1, $y, $z, $l);
				}
				if($flags[1]){
					$this->flowIntoBlock($x + 1, $y, $z, $l);
				}
				if($flags[2]){
					$this->flowIntoBlock($x, $y, $z - 1, $l);
				}
				if($flags[3]){
					$this->flowIntoBlock($x, $y, $z + 1, $l);
				}
			}
		}
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @param int $newFlowDecay
	 */
	private function flowIntoBlock(int $x, int $y, int $z, int $newFlowDecay) : void{
		if($this->level->getBlockIdAt($x, $y, $z) == Block::AIR){
			$this->level->setBlockIdAt($x, $y, $z, Block::LAVA);
			$this->level->setBlockDataAt($x, $y, $z, $newFlowDecay);
			$this->lavaSpread($x, $y, $z);
		}
	}

	/**
	 * @param int $x
	 * @param int $y
	 * @param int $z
	 * @return bool
	 */
	private function canFlowInto(int $x, int $y, int $z) : bool{
		$id = $this->level->getBlockIdAt($x, $y, $z);
		return $id == Block::AIR || $id == Block::LAVA || $id == Block::STILL_LAVA;
	}

	/**
	 * @param int $xx
	 * @param int $yy
	 * @param int $zz
	 * @param int $accumulatedCost
	 * @param int $previousDirection
	 * @return int
	 */
	private function calculateFlowCost(int $xx, int $yy, int $zz, int $accumulatedCost, int $previousDirection) : int{
		$cost = 1000;
		for($j = 0; $j < 4; ++$j){
			if(
				($j == 0 && $previousDirection == 1) ||
				($j == 1 && $previousDirection == 0) ||
				($j == 2 && $previousDirection == 3) ||
				($j == 3 && $previousDirection == 2)
			){
				$x = $xx;
				$y = $yy;
				$z = $zz;
				if($j == 0){
					--$x;
				}else{
					if($j == 1){
						++$x;
					}else{
						if($j == 2){
							--$z;
						}else{
							if($j == 3){
								++$z;
							}
						}
					}
				}
				if(!$this->canFlowInto($x, $y, $z)){
					continue;
				}else{
					if($this->canFlowInto($x, $y, $z) && $this->level->getBlockDataAt($x, $y, $z) == 0){
						continue;
					}else{
						if($this->canFlowInto($x, $y - 1, $z)){
							return $accumulatedCost;
						}
					}
				}
				if($accumulatedCost >= 4){
					continue;
				}
				$realCost = $this->calculateFlowCost($x, $y, $z, $accumulatedCost + 1, $j);
				if($realCost < $cost){
					$cost = $realCost;
				}
			}
		}
		return $cost;
	}

	/**
	 * @param int $xx
	 * @param int $yy
	 * @param int $zz
	 * @return array
	 */
	private function getOptimalFlowDirections(int $xx, int $yy, int $zz) : array{
		$flowCost = [0, 0, 0, 0];
		$isOptimalFlowDirection = [false, false, false, false];
		for($j = 0; $j < 4; ++$j){
			$flowCost[$j] = 1000;
			$x = $xx;
			$y = $yy;
			$z = $zz;
			if($j == 0){
				--$x;
			}else{
				if($j == 1){
					++$x;
				}else{
					if($j == 2){
						--$z;
					}else{
						if($j == 3){
							++$z;
						}
					}
				}
			}
			if(!$this->canFlowInto($x, $y, $z)){
				continue;
			}else{
				if($this->canFlowInto($x, $y, $z) && $this->level->getBlockDataAt($x, $y, $z) == 0){
					continue;
				}else{
					if($this->canFlowInto($x, $y - 1, $z)){
						$flowCost[$j] = 0;
					}else{
						$flowCost[$j] = $this->calculateFlowCost($x, $y, $z, 1, $j);
					}
				}
			}
		}
		$minCost = $flowCost[0];
		for($i = 1; $i < 4; ++$i){
			if($flowCost[$i] < $minCost){
				$minCost = $flowCost[$i];
			}
		}
		for($i = 0; $i < 4; ++$i){
			$isOptimalFlowDirection[$i] = ($flowCost[$i] == $minCost);
		}
		return $isOptimalFlowDirection;
	}

	/**
	 * @param int $x1
	 * @param int $y1
	 * @param int $z1
	 * @param int $x2
	 * @param int $y2
	 * @param int $z2
	 * @param int $decay
	 * @return int
	 */
	private function getSmallestFlowDecay(int $x1, int $y1, int $z1, int $x2, int $y2, int $z2, int $decay) : int{
		$blockDecay = $this->getFlowDecay($x1, $y1, $z1, $x2, $y2, $z2);
		if($blockDecay < 0){
			return $decay;
		}else{
			if($blockDecay >= 8){
				$blockDecay = 0;
			}
		}
		return ($decay >= 0 && $blockDecay >= $decay) ? $decay : $blockDecay;
	}

	/**
	 * @param Chunk $chunk
	 * @param int   $x
	 * @param int   $z
	 * @return int
	 */
	private function getHighestWorkableBlock(Chunk $chunk, int $x, int $z) : int{
		for($y = 127; $y >= 0; $y--){
			$b = $chunk->getBlockId($x, $y, $z);
			if($b == Block::AIR){
				break;
			}
		}
		return $y == 0 ? -1 : $y;
	}
}