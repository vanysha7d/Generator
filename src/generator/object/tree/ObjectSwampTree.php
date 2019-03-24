<?php

declare(strict_types=1);

namespace generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\Leaves;
use pocketmine\block\Vine;
use pocketmine\block\Wood;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class ObjectSwampTree extends TreeGenerator{

	/** @var Block */
	private $metaWood;

	/** @var Block */
	private $metaLeaves;

	/**
	 * ObjectSwampTree constructor.
	 */
	public function __construct(){
		$this->metaWood = new Wood(Wood::OAK);
		$this->metaLeaves = new Leaves(Leaves::OAK);
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Random       $rand
	 * @param Vector3      $vectorPosition
	 * @return bool
	 */
	public function generate(ChunkManager $worldIn, Random $rand, Vector3 $vectorPosition) : bool{
		$position = new Vector3($vectorPosition->getFloorX(), $vectorPosition->getFloorY(), $vectorPosition->getFloorZ());

		$i = $rand->nextBoundedInt(4) + 5;
		$flag = true;

		if($position->getY() >= 1 && $position->getY() + $i + 1 <= 256){
			for($j = $position->getY(); $j <= $position->getY() + 1 + $i; ++$j){
				$k = 1;

				if($j == $position->getY()){
					$k = 0;
				}

				if($j >= $position->getY() + 1 + $i - 2){
					$k = 3;
				}

				$pos2 = new Vector3;

				for($l = $position->getX() - $k; $l <= $position->getX() + $k && $flag; ++$l){
					for($i1 = $position->getZ() - $k; $i1 <= $position->getZ() + $k && $flag; ++$i1){
						if($j >= 0 && $j < 256){
							$pos2->setComponents($l, $j, $i1);
							if(!$this->canGrowInto($worldIn->getBlockIdAt((int) $pos2->x, (int) $pos2->y, (int) $pos2->z))){
								$flag = false;
							}
						}else{
							$flag = false;
						}
					}
				}
			}

			if(!$flag){
				return false;
			}else{
				$down = $position->down();
				$block = $worldIn->getBlockIdAt((int) $down->x, (int) $down->y, (int) $down->z);

				if(($block == Block::GRASS || $block == Block::DIRT) && $position->getY() < 256 - $i - 1){
					$this->setDirtAt($worldIn, $down);

					for($k1 = $position->getY() - 3 + $i; $k1 <= $position->getY() + $i; ++$k1){
						$j2 = $k1 - ($position->getY() + $i);
						$l2 = 2 - $j2 / 2;

						for($j3 = $position->getX() - $l2; $j3 <= $position->getX() + $l2; ++$j3){
							$k3 = $j3 - $position->getX();

							for($i4 = $position->getZ() - $l2; $i4 <= $position->getZ() + $l2; ++$i4){
								$j1 = $i4 - $position->getZ();

								if(abs($k3) != $l2 || abs($j1) != $l2 || $rand->nextBoundedInt(2) != 0 && $j2 != 0){
									$blockpos = new Vector3($j3, $k1, $i4);
									$id = $worldIn->getBlockIdAt((int) $blockpos->x, (int) $blockpos->y, (int) $blockpos->z);

									if($id == Block::AIR || $id == Block::LEAVES || $id == Block::VINE){
										$this->setBlockAndNotifyAdequately($worldIn, $blockpos, $this->metaLeaves);
									}
								}
							}
						}
					}

					for($l1 = 0; $l1 < $i; ++$l1){
						$up = $position->up($l1);
						$id = $worldIn->getBlockIdAt((int) $up->x, (int) $up->y, (int) $up->z);

						if($id == Block::AIR || $id == Block::LEAVES || $id == Block::WATER || $id == Block::STILL_WATER){
							$this->setBlockAndNotifyAdequately($worldIn, $up, $this->metaWood);
						}
					}

					for($i2 = $position->getY() - 3 + $i; $i2 <= $position->getY() + $i; ++$i2){
						$k2 = $i2 - ($position->getY() + $i);
						$i3 = 2 - $k2 / 2;
						$pos2 = new Vector3;

						for($l3 = $position->getX() - $i3; $l3 <= $position->getX() + $i3; ++$l3){
							for($j4 = $position->getZ() - $i3; $j4 <= $position->getZ() + $i3; ++$j4){
								$pos2->setComponents($l3, $i2, $j4);

								if($worldIn->getBlockIdAt((int) $pos2->x, (int) $pos2->y, (int) $pos2->z) == Block::LEAVES){
									$blockpos2 = $pos2->west();
									$blockpos3 = $pos2->east();
									$blockpos4 = $pos2->north();
									$blockpos1 = $pos2->south();

									if($rand->nextBoundedInt(4) == 0 && $worldIn->getBlockIdAt((int) $blockpos2->x, (int) $blockpos2->y, (int) $blockpos2->z) == Block::AIR){
										$this->addHangingVine($worldIn, $blockpos2, 8);
									}

									if($rand->nextBoundedInt(4) == 0 && $worldIn->getBlockIdAt((int) $blockpos3->x, (int) $blockpos3->y, (int) $blockpos3->z) == Block::AIR){
										$this->addHangingVine($worldIn, $blockpos3, 2);
									}

									if($rand->nextBoundedInt(4) == 0 && $worldIn->getBlockIdAt((int) $blockpos4->x, (int) $blockpos4->y, (int) $blockpos4->z) == Block::AIR){
										$this->addHangingVine($worldIn, $blockpos4, 1);
									}

									if($rand->nextBoundedInt(4) == 0 && $worldIn->getBlockIdAt((int) $blockpos1->x, (int) $blockpos1->y, (int) $blockpos1->z) == Block::AIR){
										$this->addHangingVine($worldIn, $blockpos1, 4);
									}
								}
							}
						}
					}
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Vector3      $pos
	 * @param int          $meta
	 */
	private function addVine(ChunkManager $worldIn, Vector3 $pos, int $meta) : void{
		$this->setBlockAndNotifyAdequately($worldIn, $pos, new Vine($meta));
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Vector3      $pos
	 * @param int          $meta
	 */
	private function addHangingVine(ChunkManager $worldIn, Vector3 $pos, int $meta) : void{
		$this->addVine($worldIn, $pos, $meta);
		$i = 4;

		for($pos = $pos->down(); $i > 0 && $worldIn->getBlockIdAt((int) $pos->x, (int) $pos->y, (int) $pos->z) == Block::AIR; --$i){
			$this->addVine($worldIn, $pos, $meta);
			$pos = $pos->down();
		}
	}
}