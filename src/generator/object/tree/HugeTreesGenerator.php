<?php

declare(strict_types=1);

namespace generator\object\tree;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

abstract class HugeTreesGenerator extends TreeGenerator{

	/** @var int */
	protected $baseHeight;

	/** @var Block */
	protected $woodMetadata;

	/** @var Block */
	protected $leavesMetadata;

	/** @var int */
	protected $extraRandomHeight;

	/**
	 * HugeTreesGenerator constructor.
	 * @param int   $baseHeightIn
	 * @param int   $extraRandomHeightIn
	 * @param Block $woodMetadataIn
	 * @param Block $leavesMetadataIn
	 */
	public function __construct(int $baseHeightIn, int $extraRandomHeightIn, Block $woodMetadataIn, Block $leavesMetadataIn){
		$this->baseHeight = $baseHeightIn;
		$this->extraRandomHeight = $extraRandomHeightIn;
		$this->woodMetadata = $woodMetadataIn;
		$this->leavesMetadata = $leavesMetadataIn;
	}

	/**
	 * @param Random $random
	 * @return int
	 */
	protected function getHeight(Random $random) : int{
		$i = $random->nextBoundedInt(3) + $this->baseHeight;

		if($this->extraRandomHeight > 1){
			$i += $random->nextBoundedInt($this->extraRandomHeight);
		}

		return $i;
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Vector3      $leavesPos
	 * @param int          $height
	 * @return bool
	 */
	private function isSpaceAt(ChunkManager $worldIn, Vector3 $leavesPos, int $height) : bool{
		$flag = true;

		if($leavesPos->getY() >= 1 && $leavesPos->getY() + $height + 1 <= 256){
			for($i = 0; $i <= 1 + $height; ++$i){
				$j = 2;

				if($i == 0){
					$j = 1;
				}else{
					if($i >= 1 + $height - 2){
						$j = 2;
					}
				}

				for($k = -$j; $k <= $j && $flag; ++$k){
					for($l = -$j; $l <= $j && $flag; ++$l){
						$blockPos = $leavesPos->add($k, $i, $l);
						if($leavesPos->getY() + $i < 0 || $leavesPos->getY() + $i >= 256 || !$this->canGrowInto($worldIn->getBlockIdAt((int) $blockPos->x, (int) $blockPos->y, (int) $blockPos->z))){
							$flag = false;
						}
					}
				}
			}

			return $flag;
		}else{
			return false;
		}
	}

	/**
	 * @param Vector3      $pos
	 * @param ChunkManager $worldIn
	 * @return bool
	 */
	private function ensureDirtsUnderneath(Vector3 $pos, ChunkManager $worldIn) : bool{
		$blockpos = $pos->down();
		$block = $worldIn->getBlockIdAt((int) $blockpos->x, (int) $blockpos->y, (int) $blockpos->z);

		if(($block == Block::GRASS || $block == Block::DIRT) && $pos->getY() >= 2){
			$this->setDirtAt($worldIn, $blockpos);
			$this->setDirtAt($worldIn, $blockpos->east());
			$this->setDirtAt($worldIn, $blockpos->south());
			$this->setDirtAt($worldIn, $blockpos->south()->east());
			return true;
		}else{
			return false;
		}
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Vector3      $treePos
	 * @param int          $p_175929_4_
	 * @return bool
	 */
	protected function ensureGrowable(ChunkManager $worldIn, Vector3 $treePos, int $p_175929_4_) : bool{
		return $this->isSpaceAt($worldIn, $treePos, $p_175929_4_) && $this->ensureDirtsUnderneath($treePos, $worldIn);
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Vector3      $layerCenter
	 * @param int          $width
	 */
	protected function growLeavesLayerStrict(ChunkManager $worldIn, Vector3 $layerCenter, int $width) : void{
		$i = $width * $width;

		for($j = -$width; $j <= $width + 1; ++$j){
			for($k = -$width; $k <= $width + 1; ++$k){
				$l = $j - 1;
				$i1 = $k - 1;

				if($j * $j + $k * $k <= $i || $l * $l + $i1 * $i1 <= $i || $j * $j + $i1 * $i1 <= $i || $l * $l + $k * $k <= $i){
					$blockpos = $layerCenter->add($j, 0, $k);
					$id = $worldIn->getBlockIdAt((int) $blockpos->x, (int) $blockpos->y, (int) $blockpos->z);

					if($id == Block::AIR || $id == Block::LEAVES){
						$this->setBlockAndNotifyAdequately($worldIn, $blockpos, $this->leavesMetadata);
					}
				}
			}
		}
	}

	/**
	 * @param ChunkManager $worldIn
	 * @param Vector3      $layerCenter
	 * @param int          $width
	 */
	protected function growLeavesLayer(ChunkManager $worldIn, Vector3 $layerCenter, int $width) : void{
		$i = $width * $width;

		for($j = -$width; $j <= $width; ++$j){
			for($k = -$width; $k <= $width; ++$k){
				if($j * $j + $k * $k <= $i){
					$blockpos = $layerCenter->add($j, 0, $k);
					$id = $worldIn->getBlockIdAt((int) $blockpos->x, (int) $blockpos->y, (int) $blockpos->z);

					if($id == Block::AIR || $id == Block::LEAVES){
						$this->setBlockAndNotifyAdequately($worldIn, $blockpos, $this->leavesMetadata);
					}
				}
			}
		}
	}
}