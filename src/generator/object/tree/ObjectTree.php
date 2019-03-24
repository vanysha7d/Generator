<?php

declare(strict_types=1);

namespace generator\object\tree;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Sapling;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

abstract class ObjectTree{

	/**
	 * @param int $id
	 * @return bool
	 */
	private function overridable(int $id) : bool{
		switch($id){
			case Block::AIR:
			case Block::SAPLING:
			case Block::LOG:
			case Block::LEAVES:
			case Block::SNOW_LAYER:
			case Block::LOG2:
			case Block::LEAVES2:
				return true;
			default:
				return false;
		}
	}

	/**
	 * @return int
	 */
	public function getType() : int{
		return 0;
	}

	/**
	 * @return int
	 */
	public function getTrunkBlock() : int{
		return Block::LOG;
	}

	/**
	 * @return int
	 */
	public function getLeafBlock() : int{
		return Block::LEAVES;
	}

	/**
	 * @return int
	 */
	public function getTreeHeight() : int{
		return 7;
	}

	/**
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param Random       $random
	 * @param int          $type
	 */
	public static function growTree(ChunkManager $level, int $x, int $y, int $z, Random $random, int $type = 0) : void{
		switch($type){
			case Sapling::SPRUCE:
				if($random->nextBoundedInt(39) == 0){
					$tree = new ObjectSpruceTree;
				}else{
					$tree = new ObjectSpruceTree;
				}
				break;
			case Sapling::BIRCH:
				if($random->nextBoundedInt(39) == 0){
					$tree = new ObjectTallBirchTree;
				}else{
					$tree = new ObjectBirchTree;
				}
				break;
			case Sapling::JUNGLE:
				$tree = new ObjectJungleTree;
				break;
			case Sapling::OAK:
			default:
				$tree = new ObjectOakTree;
				break;
		}

		if($tree->canPlaceObject($level, $x, $y, $z, $random)){
			$tree->placeObject($level, $x, $y, $z, $random);
		}
	}

	/**
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @return bool
	 */
	public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z) : bool{
		$radiusToCheck = 0;
		for($yy = 0; $yy < $this->getTreeHeight() + 3; ++$yy){
			if($yy == 1 || $yy == $this->getTreeHeight()){
				++$radiusToCheck;
			}
			for($xx = -$radiusToCheck; $xx < ($radiusToCheck + 1); ++$xx){
				for($zz = -$radiusToCheck; $zz < ($radiusToCheck + 1); ++$zz){
					if(!$this->overridable($level->getBlockIdAt($x + $xx, $y + $yy, $z + $zz))){
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param Random       $random
	 */
	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) : void{
		$this->placeTrunk($level, $x, $y, $z, $this->getTreeHeight() - 1);

		for($yy = $y - 3 + $this->getTreeHeight(); $yy <= $y + $this->getTreeHeight(); ++$yy){
			$yOff = $yy - ($y + $this->getTreeHeight());
			$mid = (int) (1 - $yOff / 2);
			for($xx = $x - $mid; $xx <= $x + $mid; ++$xx){
				$xOff = abs($xx - $x);
				for($zz = $z - $mid; $zz <= $z + $mid; ++$zz){
					$zOff = abs($zz - $z);
					if($xOff == $mid && $zOff == $mid && ($yOff == 0 || $random->nextBoundedInt(2) == 0)){
						continue;
					}
					if(!BlockFactory::$solid[$level->getBlockIdAt($xx, $yy, $zz)]){
						$level->setBlockIdAt($xx, $yy, $zz, $this->getLeafBlock());
						$level->setBlockDataAt($xx, $yy, $zz, $this->getType());
					}
				}
			}
		}
	}

	/**
	 * @param ChunkManager $level
	 * @param int          $x
	 * @param int          $y
	 * @param int          $z
	 * @param int          $trunkHeight
	 */
	protected function placeTrunk(ChunkManager $level, int $x, int $y, int $z, int $trunkHeight) : void{
		$level->setBlockIdAt($x, $y - 1, $z, Block::DIRT);

		for($yy = 0; $yy < $trunkHeight; ++$yy){
			$blockId = $level->getBlockIdAt($x, $y + $yy, $z);
			if($this->overridable($blockId)){
				$level->setBlockIdAt($x, $y + $yy, $z, $this->getTrunkBlock());
				$level->setBlockDataAt($x, $y + $yy, $z, $this->getType());
			}
		}
	}
}