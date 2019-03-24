<?php

declare(strict_types=1);

namespace generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\Leaves2;
use pocketmine\block\Wood2;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class ObjectDarkOakTree extends TreeGenerator{

	/** @var Block */
	private $darkOakLog;

	/** @var Block */
	private $darkOakLeaves;

	/**
	 * ObjectDarkOakTree constructor.
	 */
	public function __construct(){
		$this->darkOakLog = new Wood2(Wood2::DARK_OAK);
		$this->darkOakLeaves = new Leaves2(Leaves2::DARK_OAK);
	}

	/**
	 * @param ChunkManager $level
	 * @param Random       $rand
	 * @param Vector3      $position
	 * @return bool
	 */
	public function generate(ChunkManager $level, Random $rand, Vector3 $position) : bool{
		$i = $rand->nextBoundedInt(3) + $rand->nextBoundedInt(2) + 6;
		$j = $position->getFloorX();
		$k = $position->getFloorY();
		$l = $position->getFloorZ();

		if($k >= 1 && $k + $i + 1 < 256){
			$blockpos = $position->down();
			$block = $level->getBlockIdAt((int) $blockpos->getFloorX(), (int) $blockpos->getFloorY(), (int) $blockpos->getFloorZ());

			if($block != Block::GRASS && $block != Block::DIRT){
				return false;
			}else{
				if(!$this->placeTreeOfHeight($level, $position, $i)){
					return false;
				}else{
					$this->setDirtAt($level, $blockpos);
					$this->setDirtAt($level, $blockpos->east());
					$this->setDirtAt($level, $blockpos->south());
					$this->setDirtAt($level, $blockpos->south()->east());
					$i1 = $i - $rand->nextBoundedInt(4);
					$j1 = 2 - $rand->nextBoundedInt(3);
					$k1 = $j;
					$l1 = $l;
					$i2 = $k + $i - 1;

					for($j2 = 0; $j2 < $i; ++$j2){
						if($j2 >= $i1 && $j1 > 0){
							--$j1;
						}

						$k2 = $k + $j2;
						$blockpos1 = new Vector3($k1, $k2, $l1);
						$material = $level->getBlockIdAt((int) $blockpos1->getFloorX(), (int) $blockpos1->getFloorY(), (int) $blockpos1->getFloorZ());

						if($material == Block::AIR || $material == Block::LEAVES){
							$this->placeLogAt($level, $blockpos1);
							$this->placeLogAt($level, $blockpos1->east());
							$this->placeLogAt($level, $blockpos1->south());
							$this->placeLogAt($level, $blockpos1->east()->south());
						}
					}

					for($i3 = -2; $i3 <= 0; ++$i3){
						for($l3 = -2; $l3 <= 0; ++$l3){
							$k4 = -1;
							$this->placeLeafAt($level, $k1 + $i3, $i2 + $k4, $l1 + $l3);
							$this->placeLeafAt($level, 1 + $k1 - $i3, $i2 + $k4, $l1 + $l3);
							$this->placeLeafAt($level, $k1 + $i3, $i2 + $k4, 1 + $l1 - $l3);
							$this->placeLeafAt($level, 1 + $k1 - $i3, $i2 + $k4, 1 + $l1 - $l3);

							if(($i3 > -2 || $l3 > -1) && ($i3 != -1 || $l3 != -2)){
								$k4 = 1;
								$this->placeLeafAt($level, $k1 + $i3, $i2 + $k4, $l1 + $l3);
								$this->placeLeafAt($level, 1 + $k1 - $i3, $i2 + $k4, $l1 + $l3);
								$this->placeLeafAt($level, $k1 + $i3, $i2 + $k4, 1 + $l1 - $l3);
								$this->placeLeafAt($level, 1 + $k1 - $i3, $i2 + $k4, 1 + $l1 - $l3);
							}
						}
					}

					if($rand->nextBoolean()){
						$this->placeLeafAt($level, $k1, $i2 + 2, $l1);
						$this->placeLeafAt($level, $k1 + 1, $i2 + 2, $l1);
						$this->placeLeafAt($level, $k1 + 1, $i2 + 2, $l1 + 1);
						$this->placeLeafAt($level, $k1, $i2 + 2, $l1 + 1);
					}

					for($j3 = -3; $j3 <= 4; ++$j3){
						for($i4 = -3; $i4 <= 4; ++$i4){
							if(($j3 != -3 || $i4 != -3) && ($j3 != -3 || $i4 != 4) && ($j3 != 4 || $i4 != -3) && ($j3 != 4 || $i4 != 4) && (abs($j3) < 3 || abs($i4) < 3)){
								$this->placeLeafAt($level, $k1 + $j3, $i2, $l1 + $i4);
							}
						}
					}

					for($k3 = -1; $k3 <= 2; ++$k3){
						for($j4 = -1; $j4 <= 2; ++$j4){
							if(($k3 < 0 || $k3 > 1 || $j4 < 0 || $j4 > 1) && $rand->nextBoundedInt(3) <= 0){
								$l4 = $rand->nextBoundedInt(3) + 2;

								for($i5 = 0; $i5 < $l4; ++$i5){
									$this->placeLogAt($level, new Vector3($j + $k3, $i2 - $i5 - 1, $l + $j4));
								}

								for($j5 = -1; $j5 <= 1; ++$j5){
									for($l2 = -1; $l2 <= 1; ++$l2){
										$this->placeLeafAt($level, $k1 + $k3 + $j5, $i2, $l1 + $j4 + $l2);
									}
								}

								for($k5 = -2; $k5 <= 2; ++$k5){
									for($l5 = -2; $l5 <= 2; ++$l5){
										if(abs($k5) != 2 || abs($l5) != 2){
											$this->placeLeafAt($level, $k1 + $k3 + $k5, $i2 - 1, $l1 + $j4 + $l5);
										}
									}
								}
							}
						}
					}

					return true;
				}
			}
		}else{
			return false;
		}
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Vector3      $pos
	 * @param int          $height
	 * @return bool
	 */
	private function placeTreeOfHeight(ChunkManager $worldIn, Vector3 $pos, int $height) : bool{
		$i = $pos->getFloorX();
		$j = $pos->getFloorY();
		$k = $pos->getFloorZ();
		$blockPos = new Vector3;

		for($l = 0; $l <= $height + 1; ++$l){
			$i1 = 1;

			if($l == 0){
				$i1 = 0;
			}

			if($l >= $height - 1){
				$i1 = 2;
			}

			for($j1 = -$i1; $j1 <= $i1; ++$j1){
				for($k1 = -$i1; $k1 <= $i1; ++$k1){
					$blockPos->setComponents($i + $j1, $j + $l, $k + $k1);
					if(!$this->canGrowInto($worldIn->getBlockIdAt((int) $blockPos->getFloorX(), (int) $blockPos->getFloorY(), (int) $blockPos->getFloorZ()))){
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Vector3      $pos
	 */
	private function placeLogAt(ChunkManager $worldIn, Vector3 $pos) : void{
		if($this->canGrowInto($worldIn->getBlockIdAt((int) $pos->getFloorX(), (int) $pos->getFloorY(), (int) $pos->getFloorZ()))){
			$this->setBlockAndNotifyAdequately($worldIn, $pos, $this->darkOakLog);
		}
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 */
	private function placeLeafAt(ChunkManager $worldIn, int $x, int $y, int $z) : void{
		$blockpos = new Vector3($x, $y, $z);
		$material = $worldIn->getBlockIdAt((int) $blockpos->getFloorX(), (int) $blockpos->getFloorY(), (int) $blockpos->getFloorZ());

		if($material == Block::AIR){
			$this->setBlockAndNotifyAdequately($worldIn, $blockpos, $this->darkOakLeaves);
		}
	}
}