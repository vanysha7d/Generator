<?php

declare(strict_types=1);

namespace generator\populator;

use pocketmine\block\Block;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PopulatorGroundCover extends Populator{

	/**
	 * @param ChunkManager $level
	 * @param int          $chunkX
	 * @param int          $chunkZ
	 * @param Random       $random
	 */
	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$chunk = $level->getChunk($chunkX, $chunkZ);
		for($x = 0; $x < 16; ++$x){
			for($z = 0; $z < 16; ++$z){
				$biome = Biome::getBiome($chunk->getBiomeId($x, $z));
				$cover = $biome->getGroundCover();
				if($cover != null && count($cover) > 0){
					$diffY = 0;
					if(!$cover[0]->isSolid()){
						$diffY = 1;
					}

					$height = $chunk->getHeightMap($x, $z);
					if($height == 0 || $height == 255){
						$height = 126;
					}

					for($y = $height + 1; $y > 0; --$y){
						$fullId = $chunk->getFullBlock($x, $y, $z);
						if($fullId != 0 && !Block::get($fullId >> 4)->isTransparent()){
							break;
						}
					}
					$startY = min(127, $y + $diffY);
					$endY = $startY - count($cover);
					for($y = $startY; $y > $endY && $y >= 0; --$y){
						$b = $cover[$startY - $y];
						$blockId = $chunk->getBlockId($x, $y, $z);
						if($blockId == 0 && $b->isSolid()){
							break;
						}
						if($b->getDamage() == 0){
							$chunk->setBlockId($x, $y, $z, $b->getId());
						}else{
							$chunk->setBlock($x, $y, $z, $b->getId(), $b->getDamage());
						}
					}
				}
			}
		}
	}
}