<?php

declare(strict_types=1);

namespace generator\objects\trees;

use generator\object\tree\TreeGenerator;
use generator\utils\JRandom;
use pocketmine\block\Block;
use pocketmine\block\Dirt;
use pocketmine\block\Leaves2;
use pocketmine\block\Wood2;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class AcaciaTree extends TreeGenerator{

	/**
	 * @param ChunkManager $level
	 * @param Vector3      $v3
	 * @return bool
	 */
	public function canPlaceOn(ChunkManager $level, Vector3 $v3) : bool{
		return $level->getBlockIdAt($v3->x, $v3->y, $v3->z) == Block::GRASS || $level->getBlockIdAt($v3->x, $v3->y + 1, $v3->z) == Block::AIR ? true : false;
	}

	/**
	 * @param ChunkManager $level
	 * @param Random       $random
	 * @param Vector3      $position
	 * @return bool
	 */
	public function generate(ChunkManager $level, Random $random, Vector3 $position) : bool{
		$JRandom = new JRandom;
		$height = $JRandom->nextInt(3) + $JRandom->nextInt(3) + 5;
		if(!$this->canPlaceOn($level, $position)){
			return false;
		}
		$d = (float) ($JRandom->nextFloat() * M_PI * 2.0);
		$dx = (int) (cos($d) + 1.5) - 1;
		$dz = (int) (sin($d) + 1.5) - 1;
		if(abs($dx) > 0 && abs($dz) > 0){
			if($JRandom->nextBoolean()){
				$dx = 0;
			}else{
				$dz = 0;
			}
		}
		$twistHeight = $height - 1 - $JRandom->nextInt(4);
		$twistCount = $JRandom->nextInt(3) + 1;
		$centerX = $position->getFloorX();
		$centerZ = $position->getFloorZ();
		$trunkTopY = 0;
		for($y = 0; $y < $height; $y++){
			if($twistCount > 0 && $y >= $twistHeight){
				$centerX += $dx;
				$centerZ += $dz;
				$twistCount--;
			}

			$material = $level->getBlockIdAt($centerX, $position->getFloorY() + $y, $centerZ);
			if($material == Block::AIR || $material == Block::LEAVES){
				$trunkTopY = $position->getFloorY() + $y;
				$this->setBlockAndNotifyAdequately($level, new Vector3($centerX, $position->getFloorY() + $y, $centerZ), new Wood2(Wood2::ACACIA));
			}
		}

		for($x = -3; $x <= 3; $x++){
			for($z = -3; $z <= 3; $z++){
				if(abs($x) < 3 || abs($z) < 3){
					$this->setLeaves($centerX + $x, $trunkTopY, $centerZ + $z, $level);
				}
				if(abs($x) < 2 && abs($z) < 2){
					$this->setLeaves($centerX + $x, $trunkTopY + 1, $centerZ + $z, $level);
				}
				if(abs($x) == 2 && abs($z) == 0 || abs($x) == 0 && abs($z) == 2){
					$this->setLeaves($centerX + $x, $trunkTopY + 1, $centerZ + $z, $level);
				}
			}
		}

		$d = (float) ($JRandom->nextFloat() * M_PI * 2.0);
		$dxB = (int) (cos($d) + 1.5) - 1;
		$dzB = (int) (sin($d) + 1.5) - 1;
		if(abs($dxB) > 0 && abs($dzB) > 0){
			if($JRandom->nextBoolean()){
				$dxB = 0;
			}else{
				$dzB = 0;
			}
		}
		if($dx != $dxB || $dz != $dzB){
			$centerX = $position->getFloorX();
			$centerZ = $position->getFloorZ();
			$branchHeight = $twistHeight - 1 - $JRandom->nextInt(2);
			$twistCount = $JRandom->nextInt(3) + 1;
			$trunkTopY = 0;

			for($y = $branchHeight + 1; $y < $height; $y++){
				if($twistCount > 0){
					$centerX += $dxB;
					$centerZ += $dzB;
					$material = $level->getBlockIdAt($centerX, $position->getFloorY() + $y, $centerZ);
					if($material == Block::AIR || $material == Block::LEAVES){
						$trunkTopY = $position->getFloorY() + $y;
						$this->setBlockAndNotifyAdequately($level, new Vector3($centerX, $position->getFloorY() + $y, $centerZ), new Wood2(Wood2::ACACIA));
					}
					$twistCount--;
				}
			}

			if($trunkTopY > 0){
				for($x = -2; $x <= 2; $x++){
					for($z = -2; $z <= 2; $z++){
						if(abs($x) < 2 || abs($z) < 2){
							$this->setLeaves($centerX + $x, $trunkTopY, $centerZ + $z, $level);
						}
					}
				}
				for($x = -1; $x <= 1; $x++){
					for($z = -1; $z <= 1; $z++){
						$this->setLeaves($centerX + $x, $trunkTopY + 1, $centerZ + $z, $level);
					}
				}
			}
		}

		$this->setBlockAndNotifyAdequately($level, new Vector3($centerX, $position->getFloorY() - 1, $centerZ), new Dirt);
		return true;
	}

	/**
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param ChunkManager $world
	 */
	private function setLeaves(int $x, int $y, int $z, ChunkManager $world){
		if($world->getBlockIdAt($x, $y + 1, $z) == Block::AIR){
			$this->setBlockAndNotifyAdequately($world, new Vector3($x, $y, $z), new Leaves2(Leaves2::ACACIA));
		}
	}
}