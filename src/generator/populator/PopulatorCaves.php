<?php

declare(strict_types=1);

namespace generator\populator;

use generator\biome\CaveBiome;
use generator\utils\JRandom;
use pocketmine\block\Block;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class PopulatorCaves extends Populator{

	/** @var int */
	public $caveRarity = 7;
	public $caveFrequency = 40;
	/** @var int */
	public $caveMinAltitude = 8;
	/** @var int */
	public $caveMaxAltitude = 128;
	/** @var int */
	public $individualCaveRarity = 25;
	/** @var int */
	public $caveSystemFrequency = 1;
	/** @var int */
	public $caveSystemPocketChance = 0;
	/** @var int */
	public $caveSystemPocketMinSize = 0;
	/** @var int */
	public $caveSystemPocketMaxSize = 4;
	/** @var bool */
	public $evenCaveDistribution = false;
	/** @var int */
	public $worldHeightCap = 128;
	/** @var ChunkManager */
	public $chunk;
	protected $checkAreaSize = 8;
	/** @var JRandom */
	private $random;

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) : void{
		$this->random = new JRandom;
		$this->random->setSeed($level->getSeed());
		$worldLong1 = $this->random->nextLong();
		$worldLong2 = $this->random->nextLong();

		$size = $this->checkAreaSize;

		for($x = $chunkX - $size; $x <= $chunkX + $size; $x++){
			for($z = $chunkZ - $size; $z <= $chunkZ + $size; $z++){
				$randomX = $x * $worldLong1;
				$randomZ = $z * $worldLong2;
				$this->random->setSeed($randomX ^ $randomZ ^ $level->getSeed());
				$this->generateChunk($x, $z, $level->getChunk($chunkX, $chunkZ));
			}
		}
	}

	protected function generateChunk(int $chunkX, int $chunkZ, Chunk $generatingChunkBuffer) : void{
		$i = $this->random->nextInt($this->random->nextInt($this->random->nextInt($this->caveFrequency) + 1) + 1);
		if($this->evenCaveDistribution){
			$i = $this->caveFrequency;
		}
		if($this->random->nextInt(100) >= $this->caveRarity){
			$i = 0;
		}

		for($j = 0; $j < $i; $j++){
			$x = $chunkX * 16 + $this->random->nextInt(16);

			if($this->evenCaveDistribution){
				$y = self::numberInRange($this->random, $this->caveMinAltitude, $this->caveMaxAltitude);
			}else{
				$y = $this->random->nextInt($this->random->nextInt($this->caveMaxAltitude - $this->caveMinAltitude + 1) + 1) + $this->caveMinAltitude;
			}

			$z = $chunkZ * 16 + $this->random->nextInt(16);

			$count = $this->caveSystemFrequency;
			$largeCaveSpawned = false;
			if($this->random->nextInt(100) <= $this->individualCaveRarity){
				$this->generateLargeCaveNode($this->random->nextLong(), $generatingChunkBuffer, $x, $y, $z);
				$largeCaveSpawned = true;
			}

			if(($largeCaveSpawned) || ($this->random->nextInt(100) <= $this->caveSystemPocketChance - 1)){
				$count += self::numberInRange($this->random, $this->caveSystemPocketMinSize, $this->caveSystemPocketMaxSize);
			}
			while($count > 0){
				$count--;
				$f1 = $this->random->nextFloat() * 3.141593 * 2.0;
				$f2 = ($this->random->nextFloat() - 0.5) * 2.0 / 8.0;
				$f3 = $this->random->nextFloat() * 2.0 + $this->random->nextFloat();

				$this->generateCaveNode($this->random->nextLong(), $generatingChunkBuffer, $x, $y, $z, $f3, $f1, $f2, 0, 0, 1.0);
			}
		}
	}

	public static function numberInRange(JRandom $random, int $min, int $max) : int{
		return $min + $random->nextInt($max - $min + 1);
	}

	protected function generateLargeCaveNode(int $seed, Chunk $chunk, float $x, float $y, float $z) : void{
		$this->generateCaveNode($seed, $chunk, $x, $y, $z, 1.0 + $this->random->nextFloat() * 6.0, 0.0, 0.0, -1, -1, 0.5);
	}

	protected function generateCaveNode(int $seed, Chunk $chunk, float $x, float $y, float $z, float $radius, float $angelOffset, float $angel, int $angle, int $maxAngle, float $scale) : void{
		$chunkX = $chunk->getX();
		$chunkZ = $chunk->getZ();

		$realX = $chunkX * 16 + 8;
		$realZ = $chunkZ * 16 + 8;

		$f1 = 0.0;
		$f2 = 0.0;

		$localRandom = new JRandom($seed);

		if($maxAngle <= 0){
			$checkAreaSize = $this->checkAreaSize * 16 - 16;
			$maxAngle = $checkAreaSize - $localRandom->nextInt($checkAreaSize / 4);
		}
		$isLargeCave = false;

		if($angle == -1){
			$angle = $maxAngle / 2;
			$isLargeCave = true;
		}

		$randomAngel = $localRandom->nextInt(intval($maxAngle / 2)) + $maxAngle / 4;
		$bigAngel = $localRandom->nextInt(6) == 0;

		for(; $angle < $maxAngle; $angle++){
			$offsetXZ = 1.5 + sin($angle * 3.141593 / $maxAngle) * $radius * 1.0;
			$offsetY = $offsetXZ * $scale;

			$cos = cos($angel);
			$sin = sin($angel);
			$x += cos($angelOffset) * $cos;
			$y += $sin;
			$z += sin($angelOffset) * $cos;

			if($bigAngel){
				$angel *= 0.92;
			}else{
				$angel *= 0.7;
			}
			$angel += $f2 * 0.1;
			$angelOffset += $f1 * 0.1;

			$f2 *= 0.9;
			$f1 *= 0.75;
			$f2 += ($localRandom->nextFloat() - $localRandom->nextFloat()) * $localRandom->nextFloat() * 2.0;
			$f1 += ($localRandom->nextFloat() - $localRandom->nextFloat()) * $localRandom->nextFloat() * 4.0;

			if((!$isLargeCave) && ($angle == $randomAngel) && ($radius > 1.0) && ($maxAngle > 0)){
				$this->generateCaveNode($localRandom->nextLong(), $chunk, $x, $y, $z, $localRandom->nextFloat() * 0.5 + 0.5, $angelOffset - 1.570796, $angel / 3.0, $angle, $maxAngle, 1.0);
				$this->generateCaveNode($localRandom->nextLong(), $chunk, $x, $y, $z, $localRandom->nextFloat() * 0.5 + 0.5, $angelOffset + 1.570796, $angel / 3.0, $angle, $maxAngle, 1.0);
				return;
			}
			if((!$isLargeCave) && ($localRandom->nextInt(4) == 0)){
				continue;
			}

			$distanceX = $x - $realX;
			$distanceZ = $z - $realZ;
			$angelDiff = $maxAngle - $angle;
			$newRadius = $radius + 2.0 + 16.0;
			if($distanceX * $distanceX + $distanceZ * $distanceZ - $angelDiff * $angelDiff > $newRadius * $newRadius){
				return;
			}

			if(($x < $realX - 16.0 - $offsetXZ * 2.0) || ($z < $realZ - 16.0 - $offsetXZ * 2.0) || ($x > $realX + 16.0 + $offsetXZ * 2.0) || ($z > $realZ + 16.0 + $offsetXZ * 2.0)){
				continue;
			}


			$xFrom = floor($x - $offsetXZ) - $chunkX * 16 - 1;
			$xTo = floor($x + $offsetXZ) - $chunkX * 16 + 1;

			$yFrom = floor($y - $offsetY) - 1;
			$yTo = floor($y + $offsetY) + 1;

			$zFrom = floor($z - $offsetXZ) - $chunkZ * 16 - 1;
			$zTo = floor($z + $offsetXZ) - $chunkZ * 16 + 1;

			if($xFrom < 0){
				$xFrom = 0;
			}
			if($xTo > 16){
				$xTo = 16;
			}

			if($yFrom < 1){
				$yFrom = 1;
			}
			if($yTo > $this->worldHeightCap - 8){
				$yTo = $this->worldHeightCap - 8;
			}
			if($zFrom < 0){
				$zFrom = 0;
			}
			if($zTo > 16){
				$zTo = 16;
			}

			$waterFound = false;
			for($xx = $xFrom; (!$waterFound) && ($xx < $xTo); $xx++){
				for($zz = $zFrom; (!$waterFound) && ($zz < $zTo); $zz++){
					for($yy = $yTo + 1; (!$waterFound) && ($yy >= $yFrom - 1); $yy--){
						if($yy >= 0 && $yy < $this->worldHeightCap){
							$block = $chunk->getBlockId((int) $xx, (int) $yy, (int) $zz);
							if($block == Block::WATER || $block == Block::STILL_WATER){
								$waterFound = true;
							}
							if(($yy != $yFrom - 1) && ($xx != $xFrom) && ($xx != $xTo - 1) && ($zz != $zFrom) && ($zz != $zTo - 1)){
								$yy = $yFrom;
							}
						}
					}
				}
			}

			if($waterFound){
				continue;
			}

			for($xx = $xFrom; $xx < $xTo; $xx++){
				$modX = ($xx + $chunkX * 16 + 0.5 - $x) / $offsetXZ;
				for($zz = $zFrom; $zz < $zTo; $zz++){
					$modZ = ($zz + $chunkZ * 16 + 0.5 - $z) / $offsetXZ;

					$grassFound = false;
					if($modX * $modX + $modZ * $modZ < 1.0){
						for($yy = $yTo; $yy > $yFrom; $yy--){
							$modY = (($yy - 1) + 0.5 - $y) / $offsetY;
							if(($modY > -0.7) && ($modX * $modX + $modY * $modY + $modZ * $modZ < 1.0)){
								$biome = Biome::getBiome($chunk->getBiomeId((int) $xx, (int) $zz));
								if(!($biome instanceof CaveBiome)){
									continue;
								}

								$material = $chunk->getBlockId((int) $xx, (int) $yy, (int) $zz);
								if($material == Block::GRASS || $material == Block::MYCELIUM){
									$grassFound = true;
								}

								if($yy - 1 < 10){
									$chunk->setBlock((int) $xx, (int) $yy, (int) $zz, Block::LAVA);
								}else{
									$chunk->setBlock((int) $xx, (int) $yy, (int) $zz, Block::AIR);

									if($grassFound && ($chunk->getBlockId((int) $xx, (int) $yy - 1, (int) $zz) == Block::DIRT)){
										$chunk->setBlock((int) $xx, (int) $yy - 1, (int) $zz, $biome->getSurfaceBlock());
									}
								}
							}
						}
					}
				}
			}

			if($isLargeCave){
				break;
			}
		}
	}
}