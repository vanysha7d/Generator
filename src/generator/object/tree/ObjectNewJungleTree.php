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

class ObjectNewJungleTree extends TreeGenerator{

	/** @var int */
	private $minTreeHeight;

	/** @var Block */
	private $metaWood;

	/** @var Block */
	private $metaLeaves;

	public function __construct(int $minTreeHeight){
		$this->minTreeHeight = $minTreeHeight;

		$this->metaWood = new Wood(Wood::JUNGLE);
		$this->metaLeaves = new Leaves(Leaves::JUNGLE);
	}

	public function generate(ChunkManager $worldIn, Random $rand, Vector3 $vectorPosition) : bool{
		$position = new Vector3($vectorPosition->getFloorX(), $vectorPosition->getFloorY(), $vectorPosition->getFloorZ());

		$i = $rand->nextBoundedInt(3) + $this->minTreeHeight;
		$flag = true;

		if($position->getY() >= 1 && $position->getY() + $i + 1 <= 256){
			for($j = $position->getY(); $j <= $position->getY() + 1 + $i; ++$j){
				$k = 1;

				if($j == $position->getY()){
					$k = 0;
				}

				if($j >= $position->getY() + 1 + $i - 2){
					$k = 2;
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

				if(($block == Block::GRASS || $block == Block::DIRT || $block == Block::FARMLAND) && $position->getY() < 256 - $i - 1){
					$this->setDirtAt($worldIn, $down);

					for($i3 = $position->getY() - 3 + $i; $i3 <= $position->getY() + $i; ++$i3){
						$i4 = $i3 - ($position->getY() + $i);
						$j1 = 1 - $i4 / 2;

						for($k1 = $position->getX() - $j1; $k1 <= $position->getX() + $j1; ++$k1){
							$l1 = $k1 - $position->getX();

							for($i2 = $position->getZ() - $j1; $i2 <= $position->getZ() + $j1; ++$i2){
								$j2 = $i2 - $position->getZ();

								if(abs($l1) != $j1 || abs($j2) != $j1 || $rand->nextBoundedInt(2) != 0 && $i4 != 0){
									$blockpos = new Vector3($k1, $i3, $i2);
									$id = $worldIn->getBlockIdAt((int) $blockpos->x, (int) $blockpos->y, (int) $blockpos->z);

									if($id == Block::AIR || $id == Block::LEAVES || $id == Block::VINE){
										$this->setBlockAndNotifyAdequately($worldIn, $blockpos, $this->metaLeaves);
									}
								}
							}
						}
					}

					for($j3 = 0; $j3 < $i; ++$j3){
						$up = $position->up($j3);
						$id = $worldIn->getBlockIdAt((int) $up->x, (int) $up->y, (int) $up->z);

						if($id == Block::AIR || $id == Block::LEAVES || $id == Block::VINE){
							$this->setBlockAndNotifyAdequately($worldIn, $up, $this->metaWood);

							if($j3 > 0){
								if($rand->nextBoundedInt(3) > 0 && $this->isAirBlock($worldIn, $position->add(-1, $j3, 0))){
									$this->addVine($worldIn, $position->add(-1, $j3, 0), 8);
								}

								if($rand->nextBoundedInt(3) > 0 && $this->isAirBlock($worldIn, $position->add(1, $j3, 0))){
									$this->addVine($worldIn, $position->add(1, $j3, 0), 2);
								}

								if($rand->nextBoundedInt(3) > 0 && $this->isAirBlock($worldIn, $position->add(0, $j3, -1))){
									$this->addVine($worldIn, $position->add(0, $j3, -1), 1);
								}

								if($rand->nextBoundedInt(3) > 0 && $this->isAirBlock($worldIn, $position->add(0, $j3, 1))){
									$this->addVine($worldIn, $position->add(0, $j3, 1), 4);
								}
							}
						}
					}

					for($k3 = $position->getY() - 3 + $i; $k3 <= $position->getY() + $i; ++$k3){
						$j4 = $k3 - ($position->getY() + $i);
						$k4 = 2 - $j4 / 2;
						$pos2 = new Vector3;

						for($l4 = $position->getX() - $k4; $l4 <= $position->getX() + $k4; ++$l4){
							for($i5 = $position->getZ() - $k4; $i5 <= $position->getZ() + $k4; ++$i5){
								$pos2->setComponents($l4, $k3, $i5);

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

	private function isAirBlock(ChunkManager $level, Vector3 $v) : bool{
		return $level->getBlockIdAt((int) $v->x, (int) $v->y, (int) $v->z) == 0;
	}

	private function addVine(ChunkManager $worldIn, Vector3 $pos, int $meta) : void{
		$this->setBlockAndNotifyAdequately($worldIn, $pos, new Vine($meta));
	}

	private function addHangingVine(ChunkManager $worldIn, Vector3 $pos, int $meta) : void{
		$this->addVine($worldIn, $pos, $meta);
		$i = 4;

		for($pos = $pos->down(); $i > 0 && $worldIn->getBlockIdAt((int) $pos->x, (int) $pos->y, (int) $pos->z) == Block::AIR; --$i){
			$this->addVine($worldIn, $pos, $meta);
			$pos = $pos->down();
		}
	}
}