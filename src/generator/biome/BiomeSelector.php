<?php

declare(strict_types=1);

namespace generator\biome;

use generator\Generator;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\utils\Random;

class BiomeSelector{

	/** @var Biome */
	private $fallback;
	/** @var Simplex */
	private $temperature;
	/** @var Simplex */
	private $rainfall;

	/** @var array */
	private $biomes = [];

	/** @var null|\SplFixedArray */
	private $map = null;

	/**
	 * BiomeSelector constructor.
	 * @param Random $random
	 * @param Biome  $fallback
	 */
	public function __construct(Random $random, Biome $fallback){
		$this->fallback = $fallback;
		$this->temperature = new Simplex($random, 2.0, 1.0 / 8.0, 1.0 / 1024.0);
		$this->rainfall = new Simplex($random, 2.0, 1.0 / 8.0, 1.0 / 1024.0);
	}

	/**
	 * @param float $temperature
	 * @param float $rainfall
	 * @return int
	 */
	public function lookup(float $temperature, float $rainfall) : int{
		if($rainfall < 0.25){
			return Generator::SWAMP;
		}else{
			if($rainfall < 0.60){
				if($temperature < 0.25){
					return Generator::SNOWY_TUNDRA;
				}else{
					if($temperature < 0.75){
						return Generator::DESERT;
					}else{
						return Generator::SAVANNA;
					}
				}
			}else{
				if($rainfall < 0.80){
					if($temperature < 0.25){
						return Generator::TAIGA;
					}else{
						return Generator::FOREST;
					}
				}else{
					if($rainfall < 1.0){
						return Generator::JUNGLE;
					}
				}
			}
		}
		return Generator::PLAINS;
	}

	public function recalculate() : void{
		$this->map = new \SplFixedArray(64 * 64);
		for($i = 0; $i < 64; ++$i){
			for($j = 0; $j < 64; ++$j){
				$this->map[$i + ($j << 6)] = $this->lookup($i / 63.0, $j / 63.0);
			}
		}
	}

	/**
	 * @param Biome $biome
	 */
	public function addBiome(Biome $biome) : void{
		$this->biomes[$biome->getId()] = true;
	}

	/**
	 * @param float $x
	 * @param float $z
	 * @return float
	 */
	public function getTemperature(float $x, float $z) : float{
		return ($this->temperature->noise2D($x, $z, true) + 1) / 2;
	}

	/**
	 * @param float $x
	 * @param float $z
	 * @return float
	 */
	public function getRainfall(float $x, float $z) : float{
		return ($this->rainfall->noise2D($x, $z, true) + 1) / 2;
	}

	/**
	 * @param float $x
	 * @param float $z
	 * @return Biome
	 */
	public function pickBiome(float $x, float $z) : Biome{
		$temperature = (int) ($this->getTemperature($x, $z) * 63);
		$rainfall = (int) ($this->getRainfall($x, $z) * 63);

		$biomeId = $this->map[$temperature + ($rainfall << 6)];
		if(isset($this->biomes[$biomeId])){
			return Biome::getBiome($biomeId);
		}else{
			return $this->fallback;
		}
	}
}