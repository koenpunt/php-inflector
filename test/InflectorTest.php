<?php

use PhpInflector\Inflector;

class InflectorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers PhpInflector\Inflector::pluralize
	 * @covers PhpInflector\Inflector::apply_inflections
	 */
	public function testPluralize()
	{
		$this->assertEquals('books', Inflector::pluralize('book'));
	}

	/**
	 * @covers PhpInflector\Inflector::pluralize
	 * @covers PhpInflector\Inflector::apply_inflections
	 */
	public function testPluralizeWithUncountables()
	{
		$this->assertEquals('information', Inflector::pluralize('information'));
		$this->assertEquals('equipment', Inflector::pluralize('equipment'));
		$this->assertEquals('rice', Inflector::pluralize('rice'));
	}

	/**
	 * @covers PhpInflector\Inflector::singularize
	 * @covers PhpInflector\Inflector::apply_inflections
	 */
	public function testSingularize()
	{
		$this->assertEquals('book', Inflector::singularize('books'));
	}

	/**
	 * @covers PhpInflector\Inflector::camelize
	 */
	public function testCamelize()
	{
		$this->assertEquals("ActiveModel", Inflector::camelize("active_model"));
		$this->assertEquals("activeModel", Inflector::camelize("active_model", false));
		$this->assertEquals("ActiveModel\Errors", Inflector::camelize("active_model/errors"));
		$this->assertEquals("activeModel\Errors", Inflector::camelize("active_model/errors", false));
	}

	/**
	 * @covers PhpInflector\Inflector::underscore
	 */
	public function testUnderscore()
	{
		$inflections = Inflector::inflections(function($inflect){
			$inflect->acronym('SSL');
		});
		
		$this->assertEquals("active_record/errors", Inflector::underscore("ActiveRecord\Errors"));
		$this->assertEquals("active_record", Inflector::underscore("ActiveRecord"));
		$this->assertEquals("ssl_error", Inflector::underscore("SSLError"));
	}

	/**
	 * @covers PhpInflector\Inflector::humanize
	 */
	public function testHumanize()
	{
		$inflections = Inflector::inflections(function($inflect){
			$inflect->human('/_cnt$/i', '$1_count');
		});
		$this->assertEquals("Like count", Inflector::humanize("like_cnt"));
		
		$this->assertEquals("Author", Inflector::humanize("author_id"));
		$this->assertEquals("Employee salary", Inflector::humanize("employee_salary"));
	}

	/**
	 * @covers PhpInflector\Inflector::titleize
	 */
	public function testTitleize()
	{
		$this->assertEquals("Man From The Boondocks", Inflector::titleize("man from the boondocks"));
		$this->assertEquals("X Men: The Last Stand", Inflector::titleize("x-men: the last stand"));
		$this->assertEquals("The Man Without A Past", Inflector::titleize("TheManWithoutAPast"));
		$this->assertEquals("Raiders Of The Lost Ark", Inflector::titleize("raiders_of_the_lost_ark"));
	}

	/**
	 * @covers PhpInflector\Inflector::tableize
	 */
	public function testTableize()
	{
		$this->assertEquals("raw_scaled_scorers", Inflector::tableize("RawScaledScorer"));
		$this->assertEquals("egg_and_hams", Inflector::tableize("egg_and_ham"));
		$this->assertEquals("fancy_categories", Inflector::tableize("fancyCategory"));
	}

	/**
	 * @covers PhpInflector\Inflector::classify
	 */
	public function testClassify()
	{
		$this->assertEquals("EggAndHam", Inflector::classify("egg_and_hams"));
		$this->assertEquals("Post", Inflector::classify("posts"));
	}

	/**
	 * @covers PhpInflector\Inflector::dasherize
	 */
	public function testDasherize()
	{
		$this->assertEquals("puni-puni", Inflector::dasherize("puni_puni"));
	}

	/**
	 * @covers PhpInflector\Inflector::denamespace
	 */
	public function testDenamespace()
	{
		$this->assertEquals("Inflections", Inflector::denamespace("ActiveRecord\CoreExtensions\String\Inflections"));
		$this->assertEquals("Inflections", Inflector::denamespace("Inflections"));
	}

	/**
	 * @covers PhpInflector\Inflector::foreign_key
	 */
	public function testForeign_key()
	{
		$this->assertEquals("message_id", Inflector::foreign_key("Message"));
		$this->assertEquals("messageid", Inflector::foreign_key("Message", false));
		$this->assertEquals("post_id", Inflector::foreign_key("Admin\Post"));
	}

	/**
	 * @covers PhpInflector\Inflector::ordinalize
	 */
	public function testOrdinalize()
	{
		$this->assertEquals("1st", Inflector::ordinalize(1));
		$this->assertEquals("2nd", Inflector::ordinalize(2));
		$this->assertEquals("1002nd", Inflector::ordinalize(1002));
		$this->assertEquals("1003rd", Inflector::ordinalize(1003));
		$this->assertEquals("-11th", Inflector::ordinalize(-11));
		$this->assertEquals("-10210th", Inflector::ordinalize(-10210));
	}

	/**
	 * @covers PhpInflector\Inflector::transliterate
	 */
	public function testTransliterate()
	{
		$this->assertEquals("AEroskobing", Inflector::transliterate("Ærøskøbing"));
		$this->assertEquals("A ae Ubermensch pa hoyeste niva! I a lublu PHP! fi", Inflector::transliterate("A æ Übérmensch på høyeste nivå! И я люблю PHP! ﬁ"));
	}

	/**
	 * @covers PhpInflector\Inflector::parameterize
	 */
	public function testParameterize()
	{
		$this->assertEquals("donald-e-knuth", Inflector::parameterize("Donald E. Knuth"));
		$this->assertEquals("aeroskobing-on-water", Inflector::parameterize("Ærøskøbing on Water"));
	}

	/**
	 * @covers PhpInflector\Inflector::inflections
	 */
	public function testInflections()
	{
		Inflector::inflections(function($inflect){
			$inflect->acronym('RESTful');
		});
		
		$this->assertEquals('RESTful Controller', Inflector::titleize('RESTfulController'));
	}
}