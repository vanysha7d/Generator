<?php

declare(strict_types=1);

namespace generator\populator;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PopulatorGroundFire extends Populator{

	/** @var ChunkManager */
	private $level;
	/** @var int */
	private $randomAmount;
	/** @var int */
	private $baseAmount;

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
		$chunk = $level->getChunk($chunkX, $chunkZ);
		$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange(0, 15);
			$z = $random->nextRange(0, 15);
			$y = $this->getHighestWorkableBlock($chunk, $x, $z);
			if($y != -1 && $this->canGroundFireStay($chunk, $x, $y, $z)){
				$chunk->setBlock($x, $y, $z, Block::FIRE);
				$chunk->setBlockLight($x, $y, $z, BlockFactory::$light[Block::FIRE]);
			}
		}
	}

	/**
	 * @param Chunk $chunk
	 * @param int   $x
	 * @param int   $y
	 * @param int   $z
	 * @return bool
	 */
	private function canGroundFireStay(Chunk $chunk, int $x, int $y, int $z) : bool{
		$b = $chunk->getBlockId($x, $y, $z);
		return ($b == Block::AIR) && $chunk->getBlockId($x, $y - 1, $z) == Block::NETHERRACK;
	}

	/**
	 * @param Chunk $chunk
	 * @param int   $x
	 * @param int   $z
	 * @return int
	 */
	private function getHighestWorkableBlock(Chunk $chunk, int $x, int $z) : int{
		for($y = 0; $y <= 127; ++$y){
			$b = $chunk->getBlockId($x, $y, $z);
			if($b == Block::AIR){
				break;
			}
		}
		return $y == 0 ? -1 : $y;
	}
}