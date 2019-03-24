<?php

declare(strict_types=1);

namespace generator\populator\tree;

use generator\math\Math;
use generator\objects\trees\AcaciaTree;
use pocketmine\block\Block;
use pocketmine\block\Sapling;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class SavannaTreePopulator extends Populator{

	/** @var ChunkManager */
	private $level;
	/** @var int */
	private $randomAmount;
	/** @var int */
	private $baseAmount;

	/** @var int */
	private $type;

	public function __construct($type = Sapling::ACACIA){
		$this->type = $type;
	}

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
		$this->level = $level;
		$amount = $random->nextBoundedInt($this->randomAmount + 1) + $this->baseAmount;
		$v = new Vector3;

		for($i = 0; $i < $amount; ++$i){
			$x = Math::randomRange($random, $chunkX << 4, ($chunkX << 4) + 15);
			$z = Math::randomRange($random, $chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $this->getHighestWorkableBlock($x, $z);
			if($y == -1){
				continue;
			}
			//(new ObjectSavannaTree)->generate($level, $random, $v->setComponents($x, $y, $z));
			(new AcaciaTree)->generate($level, $random, $v->setComponents($x, $y, $z));
		}
	}

	/**
	 * @param int $x
	 * @param int $z
	 * @return int
	 */
	private function getHighestWorkableBlock(int $x, int $z) : int{
		for($y = 127; $y > 0; --$y){
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b == Block::DIRT || $b == Block::GRASS){
				break;
			}else{
				if($b != Block::AIR && $b != Block::SNOW_LAYER){
					return -1;
				}
			}
		}

		return ++$y;
	}
}