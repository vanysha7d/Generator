<?php

declare(strict_types=1);

namespace generator\math;

use pocketmine\utils\Random;

class Math{

	public static function randomRange(Random $random, int $start = 0, int $end = 0x7fffffff) : int{
		return $start + ($random->nextInt() % ($end + 1 - $start));
	}
}