<?php

declare(strict_types=1);

namespace generator\populator;

use generator\object\ore\ObjectOre;
use generator\object\ore\OreType;
use pocketmine\block\Block;
use pocketmine\block\Glowstone;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PopulatorGlowStone extends Populator{

	/** @var ChunkManager */
	private $level;
	/** @var OreType */
    private $type;

	/**
	 * @param ChunkManager $level
	 * @param int          $chunkX
	 * @param int          $chunkZ
	 * @param Random       $random
	 */
    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void {
        $this->level = $level;
        $this->type = new OreType(new Glowstone(), 1, 20, 128, 10);
        $chunk = $level->getChunk($chunkX, $chunkZ);
        $bx = $chunkX << 4;
        $bz = $chunkZ << 4;
        $ore = new ObjectOre($random, $this->type, Block::AIR);
        for ($i = 0; $i < $ore->type->clusterCount; ++$i) {
            $x = $random->nextRange(0, 15);
            $z = $random->nextRange(0, 15);
            $y = $this->getHighestWorkableBlock($chunk, $x, $z);
            if ($y != -1) {
                $ore->placeObject($level, $bx + $x, $y, $bz + $z);
            }
        }
    }

	/**
	 * @param Chunk $chunk
	 * @param int   $x
	 * @param int   $z
	 * @return int
	 */
    private function getHighestWorkableBlock(Chunk $chunk, int $x, int $z) : int {
        for ($y = 127; $y >= 0; $y--) {
            $b = $chunk->getBlockId($x, $y, $z);
            if ($b == Block::AIR) {
                break;
            }
        }
        return $y == 0 ? -1 : $y;
    }
}