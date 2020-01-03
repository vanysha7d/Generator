<?php

declare(strict_types=1);

namespace generator;

use generator\biome\{BeachBiome,
	BiomeSelector,
	DarkForestBiome,
	DarkForestHillsBiome,
	DesertBiome,
	ForestBiome,
	JungleBiome,
	MountainEdgeBiome,
	MountainsBiome,
	MushroomFieldsBiome,
	OceanBiome,
	PlainBiome,
	RiverBiome,
	SavannaBiome,
	SnowyTundraBiome,
	SwampBiome,
	TaigaBiome};
use generator\object\ore\OreType;
use generator\populator\{PopulatorCaves, PopulatorGroundCover, PopulatorOre, PopulatorRavines};
use generator\utils\JRandom;
use pocketmine\block\{Block, BlockFactory, Stone};
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use ReflectionMethod;

class Normale extends \pocketmine\level\generator\Generator{

	/** @var ChunkManager */
	protected $level;
	/** @var Random */
	protected $random;
	/** @var float */
	protected $rainfall = 0.5;
	/** @var float */
	protected $temperature = 0.5;
	/** @var Populator[] */
	private $populators = [];
	/** @var JRandom */
	private $JRandom;
	/** @var int */
	private $localSeed1;
	/** @var int */
	private $localSeed2;
	/** @var Populator[] */
	private $generationPopulators = [];
	/** @var Simplex */
	private $noiseSeaFloor;
	/** @var Simplex */
	private $noiseLand;
	/** @var Simplex */
	private $noiseMountains;
	/** @var Simplex */
	private $noiseBaseGround;
	/** @var Simplex */
	private $noiseRiver;
	/** @var BiomeSelector */
	private $selector;
	/** @var int */
	private $heightOffset;
	/** @var int */
	private $seaHeight = 64;
	/** @var int */
	private $seaFloorHeight = 48;
	/** @var int */
	private $beathStartHeight = 60;
	/** @var int */
	private $beathStopHeight = 64;
	/** @var int */
	private $bedrockDepth = 5;
	/** @var int */
	private $seaFloorGenerateRange = 5;
	/** @var int */
	private $landHeightRange = 18;
	/** @var int */
	private $mountainHeight = 13;
	/** @var int */
	private $basegroundHeight = 3;

	public function __construct(array $settings = []){
		$reflectionMethod = new ReflectionMethod(Biome::class, "register");
		$reflectionMethod->setAccessible(true);

		$reflectionMethod->invoke(null, Generator::OCEAN, new OceanBiome);
		$reflectionMethod->invoke(null, Generator::PLAINS, new PlainBiome);
		$reflectionMethod->invoke(null, Generator::DESERT, new DesertBiome);
		$reflectionMethod->invoke(null, Generator::MOUNTAINS, new MountainsBiome);
		$reflectionMethod->invoke(null, Generator::FOREST, new ForestBiome);
		$reflectionMethod->invoke(null, Generator::TAIGA, new TaigaBiome);
		$reflectionMethod->invoke(null, Generator::SWAMP, new SwampBiome);
		$reflectionMethod->invoke(null, Generator::RIVER, new RiverBiome);
		$reflectionMethod->invoke(null, Generator::SNOWY_TUNDRA, new SnowyTundraBiome);
		$reflectionMethod->invoke(null, Generator::MUSHROOM_FIELDS, new MushroomFieldsBiome);
		$reflectionMethod->invoke(null, Generator::BEACH, new BeachBiome);
		$reflectionMethod->invoke(null, Generator::MOUNTAIN_EDGE, new MountainEdgeBiome);
		$reflectionMethod->invoke(null, Generator::JUNGLE, new JungleBiome);
		$reflectionMethod->invoke(null, Generator::BIRCH_FOREST, new ForestBiome(ForestBiome::TYPE_BIRCH));
		$reflectionMethod->invoke(null, Generator::DARK_FOREST, new DarkForestBiome);
		$reflectionMethod->invoke(null, Generator::SAVANNA, new SavannaBiome);
		$reflectionMethod->invoke(null, Generator::DARK_FOREST_HILLS, new DarkForestHillsBiome);
	}

	public function getName() : string{
		return "normale";
	}

	public function getSettings() : array{
		return [];
	}

	public function init(ChunkManager $level, Random $random) : void{
		$this->level = $level;
		$this->random = $random;
		$this->JRandom = new JRandom;
		$this->random->setSeed($this->level->getSeed());
		$this->localSeed1 = $this->JRandom->nextLong();
		$this->localSeed2 = $this->JRandom->nextLong();
		$this->noiseSeaFloor = new Simplex($this->random, 1.0, 1.0 / 8.0, 1.0 / 64.0);
		$this->noiseLand = new Simplex($this->random, 2.0, 1.0 / 8.0, 1.0 / 512.0);
		$this->noiseMountains = new Simplex($this->random, 4.0, 1.0, 1.0 / 500.0);
		$this->noiseBaseGround = new Simplex($this->random, 4.0, 1.0 / 4.0, 1.0 / 64.0);
		$this->noiseRiver = new Simplex($this->random, 2.0, 1.0, 1.0 / 512.0);
		$this->random->setSeed($this->level->getSeed());
		$this->selector = new BiomeSelector($this->random, Biome::getBiome(Generator::FOREST));
		$this->heightOffset = $this->random->nextRange(-5, 3);

		$this->selector->addBiome(Biome::getBiome(Generator::OCEAN));
		$this->selector->addBiome(Biome::getBiome(Generator::PLAINS));
		$this->selector->addBiome(Biome::getBiome(Generator::DESERT));
		$this->selector->addBiome(Biome::getBiome(Generator::FOREST));
		$this->selector->addBiome(Biome::getBiome(Generator::TAIGA));
		$this->selector->addBiome(Biome::getBiome(Generator::SWAMP));
		$this->selector->addBiome(Biome::getBiome(Generator::RIVER));
		$this->selector->addBiome(Biome::getBiome(Generator::SNOWY_TUNDRA));
		$this->selector->addBiome(Biome::getBiome(Generator::MUSHROOM_FIELDS));
		$this->selector->addBiome(Biome::getBiome(Generator::JUNGLE));
		$this->selector->addBiome(Biome::getBiome(Generator::BIRCH_FOREST));
		$this->selector->addBiome(Biome::getBiome(Generator::DARK_FOREST));
		$this->selector->addBiome(Biome::getBiome(Generator::SAVANNA));
		$this->selector->addBiome(Biome::getBiome(Generator::DARK_FOREST_HILLS));

		$this->selector->recalculate();

		$caves = new PopulatorCaves;
		$this->populators[] = $caves;

		$ravines = new PopulatorRavines;
		$this->populators[] = $ravines;

		$cover = new PopulatorGroundCover;
		$this->generationPopulators[] = $cover;

		$ores = new PopulatorOre;
		$ores->setOreTypes([
			new OreType(BlockFactory::get(Block::COAL_ORE), 20, 17 - 2, 0, 128),
			new OreType(BlockFactory::get(Block::IRON_ORE), 20, 9 - 2, 0, 64),
			new OreType(BlockFactory::get(Block::REDSTONE_ORE), 8, 8 - 2, 0, 16),
			new OreType(BlockFactory::get(Block::LAPIS_ORE), 1, 7 - 2, 0, 16),
			new OreType(BlockFactory::get(Block::GOLD_ORE), 2, 9 - 2, 0, 32),
			new OreType(BlockFactory::get(Block::DIAMOND_ORE), 1, 8 - 2, 0, 16),
			new OreType(BlockFactory::get(Block::DIRT), 10, 33 - 2, 0, 128),
			new OreType(BlockFactory::get(Block::GRAVEL), 8, 33 - 2, 0, 128),
			new OreType(BlockFactory::get(Block::STONE, Stone::GRANITE), 10, 33 - 2, 0, 80),
			new OreType(BlockFactory::get(Block::STONE, Stone::DIORITE), 10, 33 - 2, 0, 80),
			new OreType(BlockFactory::get(Block::STONE, Stone::ANDESITE), 10, 33 - 2, 0, 80)
		]);
		$this->populators[] = $ores;
	}

	public function generateChunk(int $chunkX, int $chunkZ) : void{
		$this->random->setSeed($chunkX * $this->localSeed1 ^ $chunkZ * $this->localSeed2 ^ $this->level->getSeed());

		$seaFloorNoise = $this->noiseSeaFloor->getFastNoise2D(16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
		$landNoise = $this->noiseLand->getFastNoise2D(16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
		$mountainNoise = $this->noiseMountains->getFastNoise2D(16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
		$baseNoise = $this->noiseBaseGround->getFastNoise2D(16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);
		$riverNoise = $this->noiseRiver->getFastNoise2D(16, 16, 4, $chunkX * 16, 0, $chunkZ * 16);

		$chunk = $this->level->getChunk($chunkX, $chunkZ);

		for($genx = 0; $genx < 16; $genx++){
			for($genz = 0; $genz < 16; $genz++){
				$canBaseGround = false;
				$canRiver = true;

				$landHeightNoise = $landNoise[$genx][$genz] + 1.0;
				$landHeightNoise *= 2.956;
				$landHeightNoise = $landHeightNoise * $landHeightNoise;
				$landHeightNoise = $landHeightNoise - 0.6;
				$landHeightNoise = $landHeightNoise > 0 ? $landHeightNoise : 0;

				$mountainHeightGenerate = $mountainNoise[$genx][$genz] - 0.2;
				$mountainHeightGenerate = $mountainHeightGenerate > 0 ? $mountainHeightGenerate : 0;
				$mountainGenerate = (int) ($this->mountainHeight * $mountainHeightGenerate);

				$landHeightGenerate = (int) ($this->landHeightRange * $landHeightNoise);
				if($landHeightGenerate > $this->landHeightRange){
					if($landHeightGenerate > $this->landHeightRange){
						$canBaseGround = true;
					}
					$landHeightGenerate = $this->landHeightRange;
				}

				$genyHeight = $this->seaFloorHeight + $landHeightGenerate;
				$genyHeight += $mountainGenerate;

				if($genyHeight < $this->beathStartHeight){
					if($genyHeight < $this->beathStartHeight - 5){
						$genyHeight += (int) ($this->seaFloorGenerateRange * $seaFloorNoise[$genx][$genz]);
					}
					$biome = Biome::getBiome(Generator::OCEAN);
					if($genyHeight < $this->seaFloorHeight - $this->seaFloorGenerateRange){
						$genyHeight = $this->seaFloorHeight;
					}
					$canRiver = false;
				}else{
					if($genyHeight <= $this->beathStopHeight && $genyHeight >= $this->beathStartHeight){
						$biome = Biome::getBiome(Generator::BEACH);
					}else{
						$biome = $this->pickBiome($chunkX * 16 + $genx, $chunkZ * 16 + $genz);
						if($canBaseGround){
							$baseGroundHeight = (int) ($this->landHeightRange * $landHeightNoise) - $this->landHeightRange;
							$baseGroundHeight2 = (int) ($this->basegroundHeight * ($baseNoise[$genx][$genz] + 1.0));
							if($baseGroundHeight2 > $baseGroundHeight){
								$baseGroundHeight2 = $baseGroundHeight;
							}
							if($baseGroundHeight2 > $mountainGenerate){
								$baseGroundHeight2 = $baseGroundHeight2 - $mountainGenerate;
							}else{
								$baseGroundHeight2 = 0;
							}
							$genyHeight += $baseGroundHeight2;
						}
					}
				}
				if($canRiver && $genyHeight <= $this->seaHeight - 5){
					$canRiver = false;
				}

				if($canRiver){
					$riverGenerate = $riverNoise[$genx][$genz];
					if($riverGenerate > -0.25 && $riverGenerate < 0.25){
						$riverGenerate = $riverGenerate > 0 ? $riverGenerate : -$riverGenerate;
						$riverGenerate = 0.25 - $riverGenerate;

						$riverGenerate = $riverGenerate * $riverGenerate * 4.0;

						$riverGenerate = $riverGenerate - 0.0000001;
						$riverGenerate = $riverGenerate > 0 ? $riverGenerate : 0;
						$genyHeight -= $riverGenerate * 64;
						if($genyHeight < $this->seaHeight){
							$biome = Biome::getBiome(Generator::RIVER);
							if($genyHeight <= $this->seaHeight - 8){
								$genyHeight1 = $this->seaHeight - 9 + (int) ($this->basegroundHeight * ($baseNoise[$genx][$genz] + 1.0));
								$genyHeight2 = $genyHeight < $this->seaHeight - 7 ? $this->seaHeight - 7 : $genyHeight;
								$genyHeight = $genyHeight1 > $genyHeight2 ? $genyHeight1 : $genyHeight2;
							}
						}
					}
				}
				$chunk->setBiomeId($genx, $genz, $biome->getId());

				$generateHeight = $genyHeight > $this->seaHeight ? $genyHeight : $this->seaHeight;
				for($geny = 0; $geny <= $generateHeight; $geny++){
					if($geny <= $this->bedrockDepth && ($geny == 0 || $this->random->nextRange(1, 5) == 1)){
						$chunk->setBlock($genx, $geny, $genz, Block::BEDROCK);
					}else{
						if($geny > $genyHeight){
							if(($biome->getId() == Generator::SNOWY_TUNDRA || $biome->getId() == Generator::TAIGA) && $geny == $this->seaHeight){
								$chunk->setBlock($genx, $geny, $genz, Block::ICE);
							}else{
								$chunk->setBlock($genx, $geny, $genz, Block::STILL_WATER);
							}
						}else{
							$chunk->setBlock($genx, $geny, $genz, Block::STONE);
						}
					}
				}
			}
		}

		foreach($this->generationPopulators as $generationPopulator){
			$generationPopulator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}

	public function pickBiome(int $x, int $z) : Biome{
		$hash = $x * 2345803 ^ $z * 9236449 ^ $this->level->getSeed();
		$hash *= $hash + 223;

		$xNoise = $hash >> 20 & 3;
		$zNoise = $hash >> 22 & 3;

		if($xNoise == 3){
			$xNoise = 1;
		}
		if($zNoise == 3){
			$zNoise = 1;
		}

		return $this->selector->pickBiome($x + $xNoise - 1, $z + $zNoise - 1);
	}

	public function populateChunk(int $chunkX, int $chunkZ) : void{
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
		foreach($this->populators as $populator){
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}

		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$biome = Biome::getBiome($chunk->getBiomeId(7, 7));
		$biome->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
	}

	public function getSpawn() : Vector3{
		return new Vector3(127.5, 256, 127.5);
	}
}