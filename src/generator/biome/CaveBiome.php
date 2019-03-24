<?php

declare(strict_types=1);

namespace generator\biome;

interface CaveBiome{

	/**
	 * @return int
	 */
	public function getStoneBlock() : int;

	/**
	 * @return int
	 */
	public function getSurfaceBlock() : int;

	/**
	 * @return int
	 */
	public function getGroundBlock() : int;
}