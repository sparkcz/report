<?php

namespace Tlapnet\Report\Model\Utils;

use Tlapnet\Report\Exceptions\Logic\InvalidArgumentException;
use Tlapnet\Report\Utils\Suggestions;

class Metadata
{

	/** @var array */
	private $data = [];

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key, $default = NULL)
	{
		if ($this->has($key)) {
			return $this->data[$key];
		}

		if (func_num_args() < 2) {
			$hint = Suggestions::getSuggestion(array_keys($this->data), $key);
			throw new InvalidArgumentException("Unknown key '$key'" . ($hint ? ", did you mean '$hint'?" : '.'));
		}

		return $default;
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		return isset($this->data[$key]);
	}

}
