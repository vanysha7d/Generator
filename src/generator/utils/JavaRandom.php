<?php

declare(strict_types=1);

namespace generator\utils;

use http\Exception\InvalidArgumentException;

class JavaRandom{

	/** @var bool */
	private $haveNextNextGaussian;

	/** @var float */
	private $nextNextGaussian;

	/** @var int */
	private $seed;

	/**
	 * Random constructor.
	 * @param int $seed
	 */
	public function __construct(int $seed = -1){
		if($seed === -1){
			$seed = time();
		}

		$this->setSeed($seed);
	}

	/**
	 * @param int $seed
	 */
	public function setSeed(int $seed) : void{
		$this->seed = ($seed ^ 0x5DEECE66D) & ((1 << 48) - 1);
		$this->haveNextNextGaussian = false;
	}

	/**
	 * @param int $bits
	 * @return int
	 */
	protected function next(int $bits) : int{
		$this->seed = ($this->seed * 0x5DEECE66D + 0xB) & ((1 << 48) - 1);
		return (int) ($this->seed >> (48 - $bits));
	}

	/**
	 * @param array $bytes
	 */
	public function nextBytes(array $bytes) : void{
		$max = count($bytes) & ~0x3;
		for($i = 0; $i < $max; $i += 4){
			$random = $this->next(32);
			$bytes[$i] = $random;
			$bytes[$i + 1] = ($random >> 8);
			$bytes[$i + 2] = ($random >> 16);
			$bytes[$i + 3] = ($random >> 24);
		}
		if($max < count($bytes)){
			$random = $this->next(32);
			for($j = $max; $j < count($bytes); $j++){
				$bytes[$j] = $random;
				$random >>= 8;
			}
		}
	}

	/**
	 * @param int  $n
	 * @param bool $arg
	 * @return int
	 */
	public function nextInt(int $n, bool $arg = true) : int{
		if(!$arg){
			$this->next(32);
		}

		if($n <= 0){
			throw new InvalidArgumentException("n must be positive");
		}
		if(($n & -$n) == $n) // i.e., n is a power of 2
		{
			return (int) (($n * (int) $this->next(31)) >> 31);
		}
		do{
			$bits = $this->next(31);
			$val = $bits % $n;
		}while($bits - $val + ($n - 1) < 0);
		return $val;
	}

	/**
	 * @return int
	 */
	public function nextLong() : int{
		return ((int) $this->next(32) << 32) + $this->next(32);
	}

	/**
	 * @return bool
	 */
	public function nextBoolean() : bool{
		return $this->next(1) != 0;
	}

	/**
	 * @return float
	 */
	public function nextFloat() : float{
		return $this->next(24) / (float) (1 << 24);
	}

	/**
	 * @return float
	 */
	public function nextDouble() : float{
		return (((float) $this->next(26) << 27) + $this->next(27)) / (float) (1 << 53);
	}

	/**
	 * @return float
	 */
	public function nextGaussian() : float{
		if($this->haveNextNextGaussian){
			$this->haveNextNextGaussian = false;
			return $this->nextNextGaussian;
		}
		do{
			$v1 = 2 * $this->nextDouble() - 1;
			$v2 = 2 * $this->nextDouble() - 1;
			$s = $v1 * $v1 + $v2 * $v2;
		}while($s >= 1);
		$norm = sqrt(-2 * log($s) / $s);
		$this->nextNextGaussian = $v2 * $norm;
		$this->haveNextNextGaussian = true;
		return $v1 * $norm;
	}
}