<?php

declare(strict_types=1);

namespace generator\utils;

class JRandom{

	/** @var bool */
	private $haveNextNextGaussian;

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
	 * @param int  $n
	 * @param bool $arg
	 * @return int
	 */
	public function nextInt(int $n, bool $arg = true) : int{
		if(!$arg){
			$this->next(32);
		}

		if($n <= 0){
			throw new \InvalidArgumentException("n must be positive");
		}
		if(($n & -$n) == $n)
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
}