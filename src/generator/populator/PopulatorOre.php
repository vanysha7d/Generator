<?php

declare(strict_types=1);

namespace generator\populator;

use generator\math\Math;
use generator\object\ore\ObjectOre;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PopulatorOre extends Populator{

	/** @var int */
	private $replaceId;
	/** @var array */
	private $oreTypes = [];

	public function __construct(int $id = Block::STONE){
		$this->replaceId = $id;
	}

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		foreach($this->oreTypes as $type){
			$ore = new ObjectOre($random, $type, $this->replaceId);
			for($i = 0; $i < $ore->type->clusterCount; ++$i){
				$x = Math::randomRange($random, $chunkX << 4, ($chunkX << 4) + 15);
				$y = Math::randomRange($random, $ore->type->minHeight, $ore->type->maxHeight);
				$z = Math::randomRange($random, $chunkZ << 4, ($chunkZ << 4) + 15);
				if($ore->canPlaceObject($level, $x, $y, $z)){
					$ore->placeObject($level, $x, $y, $z);
				}
			}
		}
	}

	public function setOreTypes(array $oreTypes){
		$this->oreTypes = $oreTypes;
	}
}