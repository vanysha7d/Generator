<?php

declare(strict_types=1);

namespace generator\biome;

class MountainEdgeBiome extends MountainsBiome{

	public function __construct(){
		parent::__construct();

		$this->setElevation(63, 97);
	}

	public function getName() : string{
		return "Mountain Edge";
	}
}