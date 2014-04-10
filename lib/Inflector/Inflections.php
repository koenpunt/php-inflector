<?php

namespace PhpInflector\Inflector;

use PhpInflector\Utils;

/**
 * A singleton instance of this class is yielded by Inflector::inflections(), which can then be used to specify additional
 * inflection rules. Examples:
 *
 *   PhpInflector\Inflector::inflections(function($inflect){
 *     $inflect->plural('/^(ox)$/i', '$1$2en');
 *     $inflect->singular('/^(ox)en/i', '$1');
 *
 *     $inflect->irregular('octopus', 'octopi');
 *
 *     $inflect->uncountable("equipment");
 *   });
 *
 * New rules are added at the top. So in the example above, the irregular rule for octopus will now be the first of the
 * pluralization and singularization rules that is runs. This guarantees that your rules run before any of the rules that may
 * already have been loaded.
 *
 * @package default
 * @author Koen Punt
 */
class Inflections{
	
	protected static $_instance = null;
	
	public static function instance(){
		if(is_null(static::$_instance)){
			static::$_instance = new Inflections();
		}
		return static::$_instance;
	}

	public $plurals, $singulars, $uncountables, $humans, $acronyms, $acronym_regex;

	public function __construct(){
		$this->plurals = array();
		$this->singulars = array();
		$this->uncountables = array();
		$this->humans = array();

		$this->acronyms = array();
		$this->acronym_regex = '(?=a)b';
	}

	/**
	 * Specifies a new acronym. An acronym must be specified as it will appear in a camelized string.  An underscore
	 * string that contains the acronym will retain the acronym when passed to `camelize`, `humanize`, or `titleize`.
	 * A camelized string that contains the acronym will maintain the acronym when titleized or humanized, and will
	 * convert the acronym into a non-delimited single lowercase word when passed to +underscore+.
	 *
	 * Examples:
	 *   acronym('HTML')
	 *   titleize('html') #=> 'HTML'
	 *   camelize('html') #=> 'HTML'
	 *   underscore('MyHTML') #=> 'my_html'
	 *
	 * The acronym, however, must occur as a delimited unit and not be part of another word for conversions to recognize it:
	 *
	 *   acronym('HTTP')
	 *   camelize('my_http_delimited') #=> 'MyHTTPDelimited'
	 *   camelize('https') #=> 'Https', not 'HTTPs'
	 *   underscore('HTTPS') #=> 'http_s', not 'https'
	 *
	 *   acronym('HTTPS')
	 *   camelize('https') #=> 'HTTPS'
	 *   underscore('HTTPS') #=> 'https'
	 *
	 * Note: Acronyms that are passed to `pluralize` will no longer be recognized, since the acronym will not occur as
	 * a delimited unit in the pluralized result. To work around this, you must specify the pluralized form as an
	 * acronym as well:
	 *
	 *    acronym('API')
	 *    camelize(pluralize('api')) #=> 'Apis'
	 *
	 *    acronym('APIs')
	 *    camelize(pluralize('api')) #=> 'APIs'
	 *
	 * `acronym` may be used to specify any word that contains an acronym or otherwise needs to maintain a non-standard
	 * capitalization. The only restriction is that the word must begin with a capital letter.
	 *
	 * Examples:
	 *   acronym('RESTful')
	 *   underscore('RESTful') #=> 'restful'
	 *   underscore('RESTfulController') #=> 'restful_controller'
	 *   titleize('RESTfulController') #=> 'RESTful Controller'
	 *   camelize('restful') #=> 'RESTful'
	 *   camelize('restful_controller') #=> 'RESTfulController'
	 *
	 *   acronym('McDonald')
	 *   underscore('McDonald') #=> 'mcdonald'
	 *   camelize('mcdonald') #=> 'McDonald'
	 *
	 * @param string $word
	 * @return void
	 * @author Koen Punt
	 */
	public function acronym($word){
		$this->acronyms[strtolower($word)] = $word;
		$this->acronym_regex = implode('|', $this->acronyms);
	}

	/**
	 * Specifies a new pluralization rule and its replacement. The rule can either be a string or a regular expression.
	 * The replacement should always be a string that may include references to the matched data from the rule.
	 *
	 * @param string $rule
	 * @param string $replacement
	 * @return void
	 * @author Koen Punt
	 */
	public function plural($rule, $replacement){
		if(is_string($rule)){
			Utils::array_delete($this->uncountables, $rule);
		}
		Utils::array_delete($this->uncountables, $replacement);
		array_unshift($this->plurals, array($rule, $replacement));
	}

	/**
	 * Specifies a new singularization rule and its replacement. The rule can either be a string or a regular expression.
	 * The replacement should always be a string that may include references to the matched data from the rule.
	 * @param string $rule
	 * @param string $replacement
	 * @return void
	 * @author Koen Punt
	 */
	public function singular($rule, $replacement){
		if(is_string($rule)){
			Utils::array_delete($this->uncountables, $rule);
		}
		Utils::array_delete($this->uncountables, $replacement);
		array_unshift($this->singulars, array($rule, $replacement));
	}

	/**
	 * Specifies a new irregular that applies to both pluralization and singularization at the same time. This can only be used
	 * for strings, not regular expressions. You simply pass the irregular in singular and plural form.
	 *
	 * Examples:
	 *   irregular('octopus', 'octopi')
	 *   irregular('person', 'people')
	 *
	 * @param string $singular
	 * @param string $plural
	 * @return void
	 * @author Koen Punt
	 */
	public function irregular($singular, $plural){
		Utils::array_delete($this->uncountables, $singular);
		Utils::array_delete($this->uncountables, $plural);
		
		$singular_char = substr($singular, 0, 1);
		$singular_char_upcase = strtoupper(substr($singular, 0, 1));
		$singular_char_downcase = strtolower(substr($singular, 0, 1));
		$singular_word = substr($singular, 1);
		
		$plural_char = substr($plural, 0, 1);
		$plural_char_upcase = strtoupper(substr($plural, 0, 1));
		$plural_char_downcase = strtolower(substr($plural, 0, 1));
		$plural_word = substr($plural, 1);
		
		
		if(strtoupper(substr($singular, 0, 1)) == strtoupper(substr($plural, 0, 1))){
			$this->plural("/({$singular_char}){$singular_word}$/i", '$1' . $plural_word);
			$this->plural("/({$plural_char}){$plural_word}$/i", '$1' . $plural_word);
			$this->singular("/({$plural_char}){$plural_word}$/i", '$1' . $singular_word);
		}else{
			$this->plural("/{$singular_char_upcase}(?i){$singular_word}$/", $plural_char_upcase . $plural_word);
			$this->plural("/{$singular_char_downcase}(?i){$singular_word}$/", $plural_char_downcase . $plural_word);
			$this->plural("/{$plural_char_upcase}(?i){$plural_word}$/", $plural_char_upcase . $plural_word);
			$this->plural("/{$plural_char_downcase}(?i){$plural_word}$/", $plural_char_downcase . $plural_word);
			$this->singular("/{$plural_char_upcase}(?i){$plural_word}$/", $singular_char_upcase + $singular_word);
			$this->singular("/{$plural_char_downcase}(?i){$plural_word}$/", $singular_char_downcase + $singular_word);
		}
	}

	/**
	 * Add uncountable words that shouldn't be attempted inflected.
	 *
	 * Examples:
	 *   uncountable("money")
	 *   uncountable("money", "information")
	 *   uncountable(array('money', 'information', 'rice'))
	 *
	 * @param array $words
	 * @return array $uncountables
	 * @author Koen Punt
	 */
	public function uncountable(/* *$words */){
		$words = func_get_args();
		array_push($this->uncountables, $words);
		$this->uncountables = Utils::array_flatten($this->uncountables);
		return $this->uncountables;
	}

	/**
	 * Specifies a humanized form of a string by a regular expression rule or by a string mapping.
	 * When using a regular expression based replacement, the normal humanize formatting is called after the replacement.
	 * When a string is used, the human form should be specified as desired (example: 'The name', not 'the_name')
	 *
	 * Examples:
	 *   human('/_cnt$/i', '\1_count')
	 *   human("legacy_col_person_name", "Name")
	 *
	 * @param string $rule
	 * @param string $replacement
	 * @return array $humans
	 * @author Koen Punt
	 */
	public function human($rule, $replacement){
		$this->humans = array_merge(array($rule => $replacement), $this->humans);
		return $this->humans;
	}

	/**
	 * Clears the loaded inflections within a given scope (default is <tt>all</tt>).
	 * Give the scope as a symbol of the inflection type, the options are: <tt>plurals</tt>,
	 * <tt>singulars</tt>, <tt>uncountables</tt>, <tt>humans</tt>.
	 *
	 * Examples:
	 *   clear('all')
	 *   clear('plurals')
	 *
	 * @param string $scope
	 * @return void
	 * @author Koen Punt
	 */
	public function clear($scope = 'all'){
		switch($scope){
			case 'all':
				$this->plurals = array();
				$this->singulars = array();
				$this->uncountables = array();
				$this->humans = array();
				break;
			default:
				$this->{$scope} = array();
		}
	}
}
