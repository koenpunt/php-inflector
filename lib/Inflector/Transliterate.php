<?php

namespace PhpInflector\Inflector;

trait Transliterate{

	/**
	 * Replaces non-ASCII characters with an ASCII approximation, or if none
	 * exists, a replacement character which defaults to "?".
	 *
	 *    transliterate("Ærøskøbing")  # => "AEroskobing"
	 *
	 * @param string $string
	 * @param string $replacement
	 * @return string Transliterated $string
	 * @author Koen Punt
	 */
	public static function transliterate($string, $replacement = "?"){
		return transliterator_transliterate('Any-Latin; Latin-ASCII', $string);
	}

	/**
	 * Replaces special characters in a string so that it may be used as part of a 'pretty' URL.
	 *
	 * ==== Examples
	 *
	 *   parameterize("Donald E. Knuth") # => "donald-e-knuth"
	 *
	 * @param string $string
	 * @param string $sep
	 * @return string $string with special characters replaced
	 * @author Koen Punt
	 */
	public static function parameterize($string, $sep = '-'){
		# replace accented chars with their ascii equivalents
		$parameterized_string = static::transliterate($string);
		# Turn unwanted chars into the separator
		$parameterized_string = preg_replace('/[^a-z0-9\-_]+/i', $sep, $parameterized_string);
		if(!(is_null($sep) || empty($sep))){
			$re_sep = preg_quote($sep); # CoreExt\Regexp::escape
			# No more than one of the separator in a row.
			$parameterized_string = preg_replace("/{$re_sep}{2,}/", $sep, $parameterized_string);
			# Remove leading/trailing separator.
			$parameterized_string = preg_replace("/^{$re_sep}|{$re_sep}$/i", '', $parameterized_string);
		}
		return strtolower($parameterized_string);
	}
	
}