<?php

declare(strict_types=1);

namespace generator;

use pocketmine\level\generator\GeneratorManager;
use pocketmine\plugin\PluginBase;

class Generator extends PluginBase{

	public const OCEAN = 0;
	public const PLAINS = 1;
	public const DESERT = 2;
	public const MOUNTAINS = 3;
	public const FOREST = 4;
	public const TAIGA = 5;
	public const SWAMP = 6;
	public const RIVER = 7;
	public const SNOWY_TUNDRA = 12;
	public const MUSHROOM_FIELDS = 14;
	public const BEACH = 16;
	public const MOUNTAIN_EDGE = 20;
	public const JUNGLE = 21;
	public const BIRCH_FOREST = 27;
	public const DARK_FOREST = 29;
	public const SAVANNA = 35;
	public const DARK_FOREST_HILLS = 157;

	public function onLoad() : void{
		GeneratorManager::addGenerator(Normale::class, "normale");
	}
}