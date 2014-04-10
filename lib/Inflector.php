<?php 

namespace PhpInflector;

use PhpInflector\Inflector\Inflections;

/**
 * The Inflector transforms words from singular to plural, class names
 * to table names, modularized class names to ones without, and class names
 * to foreign keys. The default inflections for pluralization, singularization,
 * and uncountable words are kept in Inflections.php.
 *
 * @author Koen Punt
 */
class Inflector{
	use Inflector\Transliterate, Inflector\Methods;

	/**
	 * Yields a singleton instance of Inflections so you can specify additional
	 * inflector rules.
	 *
	 * Example:
	 *   Inflector::inflections(function($inflect){
	 *     $inflect->uncountable("rails");
	 *   });
	 *
	 * @param callable $block
	 * @return Inflections
	 * @author Koen Punt
	 */
	public static function inflections($block = false){
		if($block){
			return call_user_func($block, Inflections::instance());
		}else{
			return Inflections::instance();
		}
	}

}

require 'Inflections.php';