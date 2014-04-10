<?php

use PhpInflector\Inflector\Inflections;

class TransientInflections extends Inflections{
	protected static $_instance = null;
}

class InflectionsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Inflections
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->object = new TransientInflections;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	/**
	 * @covers PhpInflector\Inflector\Inflections::instance
	 * @covers PhpInflector\Inflector\Inflections::__construct
	 */
	public function testInstance()
	{
		$this->assertInstanceOf('PhpInflector\Inflector\Inflections', TransientInflections::instance());
		$this->assertSame(TransientInflections::instance(), TransientInflections::instance());
	}

	/**
	 * @covers PhpInflector\Inflector\Inflections::acronym
	 */
	public function testAcronym()
	{
		$acronym = 'McDonald';
		$this->object->acronym($acronym);
		$this->assertArrayHasKey(strtolower($acronym), $this->object->acronyms);
		$this->assertEquals($acronym, $this->object->acronyms[strtolower($acronym)]);
	}

	/**
	 * @covers PhpInflector\Inflector\Inflections::plural
	 * @todo   Implement testPlural().
	 */
	public function testPlural()
	{
		$this->object->plural('fish', 'fishies');
		$this->assertNotContains('fish', $this->object->uncountables);
		$this->assertEquals(array('fish', 'fishies'), array_shift($this->object->plurals));
	}

	/**
	 * @covers PhpInflector\Inflector\Inflections::singular
	 */
	public function testSingular()
	{
		$this->object->singular('fish', 'one fish');
		$this->assertNotContains('fish', $this->object->uncountables);
		$this->assertEquals(array('fish', 'one fish'), array_shift($this->object->singulars));
		
	}

	/**
	 * @covers PhpInflector\Inflector\Inflections::irregular
	 * @todo   Test if correct singular/plural regexes are saved
	 */
	public function testIrregular()
	{
		$this->object->irregular('person', 'people');
		$this->assertNotContains('person', $this->object->uncountables);
		$this->assertNotContains('people', $this->object->uncountables);

		$this->object->clear();

		$this->object->irregular('cow', 'kine');
		$this->assertNotContains('cow', $this->object->uncountables);
		$this->assertNotContains('kine', $this->object->uncountables);
	}

	/**
	 * @covers PhpInflector\Inflector\Inflections::uncountable
	 */
	public function testUncountable()
	{
		$uncountables = $this->object->uncountable(array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep', 'jeans', 'police'));
		$this->assertEquals(array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep', 'jeans', 'police'), $this->object->uncountables);
		$this->assertEquals(array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep', 'jeans', 'police'), $uncountables);

		$this->object->clear();

		$uncountables = $this->object->uncountable('equipment', 'information', array('rice', 'money', 'species'));
		$this->assertEquals(array('equipment', 'information', 'rice', 'money', 'species'), $this->object->uncountables);
		$this->assertEquals(array('equipment', 'information', 'rice', 'money', 'species'), $uncountables);
	}

	/**
	 * @covers PhpInflector\Inflector\Inflections::human
	 */
	public function testHuman()
	{
		$humans = $this->object->human("legacy_col_person_name", "Name");
		$this->assertEquals(array('legacy_col_person_name' => "Name"), $this->object->humans);
		$this->assertEquals(array('legacy_col_person_name' => "Name"), $humans);

		$this->object->clear();

		$humans = $this->object->human('/_cnt$/i', '$1_count');
		$this->assertEquals(array('/_cnt$/i' => '$1_count'), $this->object->humans);
		$this->assertEquals(array('/_cnt$/i' => '$1_count'), $humans);
	}

	/**
	 * @covers PhpInflector\Inflector\Inflections::clear
	 */
	public function testClear()
	{
		$this->assertEmpty($this->object->plurals);
		$this->assertEmpty($this->object->singulars);
		$this->assertEmpty($this->object->uncountables);
		$this->assertEmpty($this->object->humans);

		$this->object->human("legacy_col_person_name", "Name");
		$this->object->irregular('cow', 'kine');

		$this->assertNotEmpty($this->object->plurals);
		$this->assertNotEmpty($this->object->singulars);
		$this->assertNotEmpty($this->object->humans);

		$this->object->clear();

		$this->assertEmpty($this->object->plurals);
		$this->assertEmpty($this->object->singulars);
		$this->assertEmpty($this->object->uncountables);
		$this->assertEmpty($this->object->humans);

		$this->object->uncountable('information');

		$this->assertNotEmpty($this->object->uncountables);

		$this->object->clear('uncountables');

		$this->assertEmpty($this->object->uncountables);
	}
}
