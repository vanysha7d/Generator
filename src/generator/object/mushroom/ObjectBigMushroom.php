<?php

declare(strict_types=1);

namespace generator\object\mushroom;

use generator\object\BasicGenerator;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BrownMushroomBlock;
use pocketmine\block\RedMushroomBlock;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class ObjectBigMushroom extends BasicGenerator{

	public const NORTH_WEST = 1;
	public const NORTH = 2;
	public const NORTH_EAST = 3;
	public const WEST = 4;
	public const CENTER = 5;
	public const EAST = 6;
	public const SOUTH_WEST = 7;
	public const SOUTH = 8;
	public const SOUTH_EAST = 9;
	public const STEM = 10;
	public const ALL_INSIDE = 0;
	public const ALL_OUTSIDE = 14;
	public const ALL_STEM = 15;

	public const BROWN = 0;
	public const RED = 1;

	/** @var int */
	private $mushroomType;

	/**
	 * BigMushroom constructor.
	 * @param int $mushroomType
	 */
	public function __construct(int $mushroomType = -1){
		$this->mushroomType = $mushroomType;
	}

	/**
	 * @param ChunkManager $level
	 * @param Random       $rand
	 * @param Vector3      $position
	 * @return bool
	 */
	public function generate(ChunkManager $level, Random $rand, Vector3 $position) : bool{
		$block = $this->mushroomType;
		if($block < 0){
			$block = $rand->nextBoolean() ? self::RED : self::BROWN;
		}

		$mushroom = $block == 0 ? new BrownMushroomBlock : new RedMushroomBlock;

		$i = $rand->nextBoundedInt(3) + 4;

		if($rand->nextBoundedInt(12) == 0){
			$i *= 2;
		}

		$flag = true;

		if($position->getY() >= 1 && $position->getY() + $i + 1 < 256){
			for($j = $position->getFloorY(); $j <= $position->getY() + 1 + $i; ++$j){
				$k = 3;

				if($j <= $position->getY() + 3){
					$k = 0;
				}

				$pos = new Vector3;

				for($l = $position->getFloorX() - $k; $l <= $position->getX() + $k && $flag; ++$l){
					for($i1 = $position->getFloorZ() - $k; $i1 <= $position->getZ() + $k && $flag; ++$i1){
						if($j >= 0 && $j < 256){
							$pos->setComponents($l, $j, $i1);
							$material = $level->getBlockIdAt((int) $pos->getFloorX(), (int) $pos->getFloorY(), (int) $pos->getFloorZ());

							if($material != Block::AIR && $material != Block::LEAVES){
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
				$pos2 = $position->down();
				$block1 = $level->getBlockIdAt((int) $pos2->getFloorX(), (int) $pos2->getFloorY(), (int) $pos2->getFloorZ());

				if($block1 != Block::DIRT && $block1 != Block::GRASS && $block1 != Block::MYCELIUM){
					return false;
				}else{
					$k2 = $position->getFloorY() + $i;

					if($block == self::RED){
						$k2 = $position->getFloorY() + $i - 3;
					}

					for($l2 = $k2; $l2 <= $position->getY() + $i; ++$l2){
						$j3 = 1;

						if($l2 < $position->getY() + $i){
							++$j3;
						}

						if($block == self::BROWN){
							$j3 = 3;
						}

						$k3 = $position->getFloorX() - $j3;
						$l3 = $position->getFloorX() + $j3;
						$j1 = $position->getFloorZ() - $j3;
						$k1 = $position->getFloorZ() + $j3;

						for($l1 = $k3; $l1 <= $l3; ++$l1){
							for($i2 = $j1; $i2 <= $k1; ++$i2){
								$j2 = 5;

								if($l1 == $k3){
									--$j2;
								}else{
									if($l1 == $l3){
										++$j2;
									}
								}

								if($i2 == $j1){
									$j2 -= 3;
								}else{
									if($i2 == $k1){
										$j2 += 3;
									}
								}

								$meta = $j2;

								if($block == self::BROWN || $l2 < $position->getY() + $i){
									if(($l1 == $k3 || $l1 == $l3) && ($i2 == $j1 || $i2 == $k1)){
										continue;
									}

									if($l1 == $position->getX() - ($j3 - 1) && $i2 == $j1){
										$meta = self::NORTH_WEST;
									}

									if($l1 == $k3 && $i2 == $position->getZ() - ($j3 - 1)){
										$meta = self::NORTH_WEST;
									}

									if($l1 == $position->getX() + ($j3 - 1) && $i2 == $j1){
										$meta = self::NORTH_EAST;
									}

									if($l1 == $l3 && $i2 == $position->getZ() - ($j3 - 1)){
										$meta = self::NORTH_EAST;
									}

									if($l1 == $position->getX() - ($j3 - 1) && $i2 == $k1){
										$meta = self::SOUTH_WEST;
									}

									if($l1 == $k3 && $i2 == $position->getZ() + ($j3 - 1)){
										$meta = self::SOUTH_WEST;
									}

									if($l1 == $position->getX() + ($j3 - 1) && $i2 == $k1){
										$meta = self::SOUTH_EAST;
									}

									if($l1 == $l3 && $i2 == $position->getZ() + ($j3 - 1)){
										$meta = self::SOUTH_EAST;
									}
								}

								if($meta == self::CENTER && $l2 < $position->getY() + $i){
									$meta = self::ALL_INSIDE;
								}

								if($position->getY() >= $position->getY() + $i - 1 || $meta != self::ALL_INSIDE){
									$blockPos = new Vector3($l1, $l2, $i2);

									if(!BlockFactory::$solid[$level->getBlockIdAt((int) $blockPos->getFloorX(), (int) $blockPos->getFloorY(), (int) $blockPos->getFloorZ())]){
										$mushroom->setDamage($meta);
										$this->setBlockAndNotifyAdequately($level, $blockPos, $mushroom);
									}
								}
							}
						}
					}

					for($i3 = 0; $i3 < $i; ++$i3){
						$pos = $position->up($i3);
						$id = $level->getBlockIdAt((int) $pos->getFloorX(), (int) $pos->getFloorY(), (int) $pos->getFloorZ());

						if(!BlockFactory::$solid[$id]){
							$mushroom->setDamage(self::STEM);
							$this->setBlockAndNotifyAdequately($level, $pos, $mushroom);
						}
					}

					return true;
				}
			}
		}else{
			return false;
		}
	}
}