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

	// Methods.php

	/**
	 * Returns the plural form of the word in the string.
	 *
	 * Examples:
	 *   pluralize("post")             # => "posts"
	 *   pluralize("octopus")          # => "octopi"
	 *   pluralize("sheep")            # => "sheep"
	 *   pluralize("words")            # => "words"
	 *   pluralize("CamelOctopus")     # => "CamelOctopi"
	 *
	 * @param string $word
	 * @return string Pluralized $word
	 * @author Koen Punt
	 */
	public static function pluralize($word){
		return static::apply_inflections($word, static::inflections()->plurals);
	}

	/**
	 * The reverse of +pluralize+, returns the singular form of a word in a string.
	 *
	 * Examples:
	 *   singularize("posts")            # => "post"
	 *   singularize("octopi")           # => "octopus"
	 *   singularize("sheep")            # => "sheep"
	 *   singularize("word")             # => "word"
	 *   singularize("CamelOctopi")      # => "CamelOctopus"
	 *
	 * @param string $word
	 * @return string Singularized $word
	 * @author Koen Punt
	 */
	public static function singularize($word){
		return static::apply_inflections($word, static::inflections()->singulars);
	}

	/**
	 * By default, +camelize+ converts strings to UpperCamelCase. If the argument to +camelize+
	 * is set to <tt>lower</tt> then +camelize+ produces lowerCamelCase.
	 *
	 * +camelize+ will also convert '/' to '\' which is useful for converting paths to namespaces.
	 *
	 * Examples:
	 *   camelize("active_model"                # => "ActiveModel"
	 *   camelize("active_model", false)        # => "activeModel"
	 *   camelize("active_model/errors")        # => "ActiveModel::Errors"
	 *   camelize("active_model/errors", false) # => "activeModel::Errors"
	 *
	 * As a rule of thumb you can think of +camelize+ as the inverse of +underscore+,
	 * though there are cases where that does not hold:
	 *
	 *   camelize(underscore("SSLError")) # => "SslError"
	 *
	 * @param string $term
	 * @param boolean $uppercase_first_letter
	 * @return string Camelized $term
	 * @author Koen Punt
	 */
	public static function camelize($term, $uppercase_first_letter = true){
		$string = (string)$term;
		$inflections = static::inflections();
		if( $uppercase_first_letter ){
			$string = preg_replace_callback('/^[a-z\d]*/', function($matches) use ($inflections) {
				return isset($inflections->acronyms[$matches[0]]) ? $inflections->acronyms[$matches[0]] : ucfirst($matches[0]);
			}, $string, 1);
		}else{
			$acronym_regex = $inflections->acronym_regex;
			$string = preg_replace_callback("/^(?:{$acronym_regex}(?=\b|[A-Z_])|\w)/", function($matches) { return strtolower($matches[0]); }, $string);
		}
		return preg_replace_callback('/(?:_|(\/))([a-z\d]*)/i', function($matches){
			return str_replace('/', '\\', "{$matches[1]}" . (isset($inflections->acronyms[$matches[2]]) ? $inflections->acronyms[$matches[2]] : ucfirst($matches[2])));
		}, $string);
	}

	/**
	 * Makes an underscored, lowercase form from the expression in the string.
	 *
	 * Changes '\' to '/' to convert namespaces to paths.
	 *
	 * Examples:
	 *   underscore("ActiveRecord")         # => "active_record"
	 *   underscore("ActiveRecord\Errors")  # => "active_record/errors"
	 *
	 * As a rule of thumb you can think of +underscore+ as the inverse of +camelize+,
	 * though there are cases where that does not hold:
	 *
	 *   camelize(underscore("SSLError")) # => "SslError"
	 *
	 * @param string $camel_cased_word
	 * @return string Underscored $camel_cased_word
	 * @author Koen Punt
	 */
	public static function underscore($camel_cased_word){
		$word = $camel_cased_word;
		$word = preg_replace('/\\\/', '/', $word);
		$acronym_regex = static::inflections()->acronym_regex;
		$word = preg_replace_callback("/(?:([A-Za-z\d])|^)({$acronym_regex})(?=\b|[^a-z])/", function($matches){
			return "{$matches[1]}" . ($matches[1] ? '_' : '') . strtolower($matches[2]);
		}, $word);
		$word = preg_replace('/([A-Z\d]+)([A-Z][a-z])/','$1_$2', $word);
		$word = preg_replace('/([a-z\d])([A-Z])/','$1_$2', $word);
		$word = strtr($word, '-', '_');
		$word = strtolower($word);
		return $word;
	}

	/**
	 * Capitalizes the first word and turns underscores into spaces and strips a
	 * trailing "_id", if any. Like +titleize+, this is meant for creating pretty output.
	 *
	 * Examples:
	 *   titleize("employee_salary") # => "Employee salary"
	 *   titleize("author_id")       # => "Author"
	 *
	 * @param string $lower_case_and_underscored_word
	 * @return string Humanized $lower_case_and_underscored_word
	 * @author Koen Punt
	 */
	public static function humanize($lower_case_and_underscored_word){
		$result = $lower_case_and_underscored_word;
		$inflections = static::inflections();
		foreach($inflections->humans as $rule => $replacement){
			if(($result = preg_replace($rule, $replacement, $result, 1)))break;
		};
		$result = preg_replace('/_id$/', "", $result);
		$result = strtr($result, '_', ' ');
		return ucfirst(preg_replace_callback('/([a-z\d]*)/i', function($matches) use ($inflections) {
			return isset($inflections->acronyms[$matches[0]]) ? $inflections->acronyms[$matches[0]] : strtolower($matches[0]);
		}, $result));
	}

	/**
	 * Capitalizes all the words and replaces some characters in the string to create
	 * a nicer looking title. +titleize+ is meant for creating pretty output. It is not
	 * used in the Rails internals.
	 *
	 * +titleize+ is also aliased as as +titlecase+.
	 *
	 * Examples:
	 *   titleize("man from the boondocks")   # => "Man From The Boondocks"
	 *   titleize("x-men: the last stand")    # => "X Men: The Last Stand"
	 *   titleize("TheManWithoutAPast")       # => "The Man Without A Past"
	 *   titleize("raiders_of_the_lost_ark")  # => "Raiders Of The Lost Ark"
	 *
	 * @param string $word
	 * @return string Titleized $word
	 * @author Koen Punt
	 */
	public static function titleize($word){
		return preg_replace_callback("/\b(?<!['’`])[a-z]/", function($r1) use ($word){
			return ucfirst($r1[0]);
		}, static::humanize(static::underscore($word)));
	}

	/**
	 * Create the name of a table like Rails does for models to table names. This method
	 * uses the +pluralize+ method on the last word in the string.
	 *
	 * Examples
	 *   tableize("RawScaledScorer") # => "raw_scaled_scorers"
	 *   tableize("egg_and_ham")     # => "egg_and_hams"
	 *   tableize("fancyCategory")   # => "fancy_categories"
	 *
	 * @param string $class_name
	 * @return string Tablename for $class_name
	 * @author Koen Punt
	 */
	public static function tableize($class_name){
		return static::pluralize(static::underscore($class_name));
	}

	/**
	 * Create a class name from a plural table name like Rails does for table names to models.
	 * Note that this returns a string and not a Class. (To convert to an actual class
	 * follow +classify+ with +constantize+.)
	 *
	 * Examples:
	 *   classify("egg_and_hams") # => "EggAndHam"
	 *   classify("posts")        # => "Post"
	 *
	 * Singular names are not handled correctly:
	 *   classify("business")     # => "Busines"
	 *
	 * @param string $table_name
	 * @return string Classname for $table_name
	 * @author Koen Punt
	 */
	public static function classify($table_name){
		# strip out any leading schema name
		return static::camelize(static::singularize(preg_replace('/.*\./', '', $table_name, 1)));
	}

	/**
	 * Replaces underscores with dashes in the string.
	 *
	 * Example:
	 *   dasherize("puni_puni") # => "puni-puni"
	 *
	 * @param string $underscored_word
	 * @return string Dasherized $underscored_word
	 * @author Koen Punt
	 */
	public static function dasherize($underscored_word){
		return preg_replace('/_/', '-', $underscored_word);
	}

	/**
	 * Removes the namespace part from the expression in the string.
	 *
	 * Examples:
	 *   denamespace("ActiveRecord\CoreExtensions\String\Inflections") # => "Inflections"
	 *   denamespace("Inflections")                                    # => "Inflections"
	 *
	 * @param string $underscored_word
	 * @return string Denamespaced class name
	 * @author Koen Punt
	 */
	public static function denamespace($class_name_in_module){
		return preg_replace('/^.*\\\/', '', $class_name_in_module);
	}

	/**
	 * Creates a foreign key name from a class name.
	 * +separate_class_name_and_id_with_underscore+ sets whether
	 * the method should put '_' between the name and 'id'.
	 *
	 * Examples:
	 *   foreign_key("Message")        # => "message_id"
	 *   foreign_key("Message", false) # => "messageid"
	 *   foreign_key("Admin\Post")     # => "post_id"
	 *
	 * @param string $class_name
	 * @param boolean $separate_class_name_and_id_with_underscore
	 * @return string foreign key name from $class_name
	 * @author Koen Punt
	 */
	public static function foreign_key($class_name, $separate_class_name_and_id_with_underscore = true){
		return static::underscore(static::denamespace($class_name)) . ($separate_class_name_and_id_with_underscore ? "_id" : "id");
	}

	/**
	 * Turns a number into an ordinal string used to denote the position in an
	 * ordered sequence such as 1st, 2nd, 3rd, 4th.
	 *
	 * Examples:
	 *   ordinalize(1)     # => "1st"
	 *   ordinalize(2)     # => "2nd"
	 *   ordinalize(1002)  # => "1002nd"
	 *   ordinalize(1003)  # => "1003rd"
	 *   ordinalize(-11)   # => "-11th"
	 *   ordinalize(-1021) # => "-1021st"
	 *
	 * @param string $number
	 * @return string ordinalized number
	 * @author Koen Punt
	 */
	public static function ordinalize($number){
		$number_abs = abs($number);
		if(in_array($number_abs % 100, range(11, 13))){
			return "{$number}th";
		}else{
			switch($number_abs % 10){
				case 1:
					return "{$number}st";
				case 2:
					return "{$number}nd";
				case 3:
					return "{$number}rd";
				default:
					return "{$number}th";
			}
		}
	}

	/**
	 * Applies inflection rules for +singularize+ and +pluralize+.
	 *
	 * Examples:
	 *  apply_inflections("post", inflections()->plurals) # => "posts"
	 *  apply_inflections("posts", inflections()->singulars) # => "post"
	 *
	 * @param string $word
	 * @param array $rules
	 * @return string inflected $word
	 * @author Koen Punt
	 */
	private static function apply_inflections($word, $rules){
		$result = $word;
		preg_match('/\b\w+\Z/', strtolower($result), $matches);
		if( empty($word) || array_search($matches[0], static::inflections()->uncountables) !== false ){
			return $result;
		}else{
			foreach($rules as $rule_replacement){
				list($rule, $replacement) = $rule_replacement;
				$result = preg_replace($rule, $replacement, $result, -1, $count);
				if($count){
					break;
				}
			}
			return $result;
		}
	}

	// Transliterate.php

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
		if(!function_exists('normalizer_normalize') || !function_exists('iconv')){
			trigger_error('PHP Internationalization and/or iconv extension not installed.', E_USER_ERROR);
		}
		$roman = array('Sch','sch','Yo','Zh','Kh','Ts','Ch','Sh','Yu','ya','yo','zh','kh','ts','ch','sh','u','a','A','B','V','G','D','E','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','','Y','','E','a','b','v','g','d','e','z','i','y','k','l','m','n','o','p','r','s','t','u','f','','y','','e');
		$cyrillic = array('Щ','щ','Ё','Ж','Х','Ц','Ч','Ш','Ю','я','ё','ж','х','ц','ч','ш','ю','я','А','Б','В','Г','Д','Е','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Ь','Ы','Ъ','Э','а','б','в','г','д','е','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','ь','ы','ъ','э');
		$string = normalizer_normalize($string, \Normalizer::FORM_D);
		$string = strtr($string, array_combine($cyrillic, $roman));
		return iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $string);
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

require 'Inflections.php';