<?php

declare(strict_types=1);

namespace generator\biome;

interface CaveBiome{

	public function getStoneBlock() : int;

	public function getSurfaceBlock() : int;

	public function getGroundBlock() : int;
}