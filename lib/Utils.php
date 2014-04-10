<?php

namespace PhpInflector;

class Utils{

	/**
	 * Make multidimensional array flat
	 *
	 * @param array $array
	 * @return array
	 * @author Koen Punt
	 */
	public static function array_flatten(array $array){
		$index = 0;
		$count = count($array);

		while ($index < $count) {
			if (is_array($array[$index])) {
				array_splice($array, $index, 1, $array[$index]);
			} else {
				++$index;
			}
			$count = count($array);
		}
		return $array;
	}

	/**
	 * Deletes entry from array and return its value
	 *
	 * @param array $data
	 * @param string $key
	 * @return mixed
	 * @author Koen Punt
	 */
	public static function array_delete(array &$data, $key){
		if(array_key_exists($key, $data)){
			$value = $data[$key];
			unset($data[$key]);
			return $value;
		}
	}
}