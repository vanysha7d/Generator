<?php

declare(strict_types=1);

namespace generator\object\ore;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\VectorMath;
use pocketmine\utils\Random;

class ObjectOre{

	/** @var OreType */
	public $type;
	/** @var Random */
	private $random;
	/** @var int */
	private $replaceId;

	public function __construct(Random $random, OreType $type, int $replaceId = Block::STONE){
		$this->type = $type;
		$this->random = $random;
		$this->replaceId = $replaceId;
	}

	public function getType() : OreType{
		return $this->type;
	}

	public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z) : bool{
		return ($level->getBlockIdAt($x, $y, $z) == $this->replaceId);
	}

	public function placeObject(ChunkManager $level, int $x, int $y, int $z) : void{
		$clusterSize = $this->type->clusterSize;
		$angle = $this->random->nextFloat() * M_PI;
		$offset = VectorMath::getDirection2D($angle)->multiply($clusterSize)->divide(8);
		$x1 = $x + 8 + $offset->x;
		$x2 = $x + 8 - $offset->x;
		$z1 = $z + 8 + $offset->y;
		$z2 = $z + 8 - $offset->y;
		$y1 = $y + $this->random->nextBoundedInt(3) + 2;
		$y2 = $y + $this->random->nextBoundedInt(3) + 2;
		for($count = 0; $count <= $clusterSize; ++$count){
			$seedX = $x1 + ($x2 - $x1) * $count / $clusterSize;
			$seedY = $y1 + ($y2 - $y1) * $count / $clusterSize;
			$seedZ = $z1 + ($z2 - $z1) * $count / $clusterSize;
			$size = ((sin($count * (M_PI / $clusterSize)) + 1) * $this->random->nextFloat() * $clusterSize / 16 + 1) / 2;

			$startX = (int) ($seedX - $size);
			$startY = (int) ($seedY - $size);
			$startZ = (int) ($seedZ - $size);
			$endX = (int) ($seedX + $size);
			$endY = (int) ($seedY + $size);
			$endZ = (int) ($seedZ + $size);

			for($x = $startX; $x <= $endX; ++$x){
				$sizeX = ($x + 0.5 - $seedX) / $size;
				$sizeX *= $sizeX;

				if($sizeX < 1){
					for($y = $startY; $y <= $endY; ++$y){
						$sizeY = ($y + 0.5 - $seedY) / $size;
						$sizeY *= $sizeY;

						if($y > 0 && ($sizeX + $sizeY) < 1){
							for($z = $startZ; $z <= $endZ; ++$z){
								$sizeZ = ($z + 0.5 - $seedZ) / $size;
								$sizeZ *= $sizeZ;

								if(($sizeX + $sizeY + $sizeZ) < 1 && $level->getBlockIdAt($x, $y, $z) == $this->replaceId){
									$level->setBlockIdAt($x, $y, $z, $this->type->material->getId());
									if($this->type->material->getDamage() != 0){
										$level->setBlockDataAt($x, $y, $z, $this->type->material->getDamage());
									}
								}
							}
						}
					}
				}
			}
		}
	}
}