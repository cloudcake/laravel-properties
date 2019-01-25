<?php

namespace Properties\Tests\Unit;

use Properties\Tests\TestCase;
use Properties\Models\Property;
use Properties\Tests\Models\Person;

class PropertiesTest extends TestCase
{
    public function testPropertyCanBeCreated()
    {
        $this->assertTrue(!is_null(Property::create([
          'key' => 'EYE_COLOUR',
          'type' => 'STRING',
          'targets' => [],
          'default' => 'Unknown'
        ])));
    }

    public function testPropertiesAreAssignable()
    {
        $this->assertTrue(!is_null(Property::create([
          'key' => 'EYE_COLOUR',
          'type' => 'STRING',
          'targets' => [],
          'default' => 'Unknown'
        ])));

        $this->assertTrue(!is_null(Property::create([
          'key' => 'LIMB_COUNT',
          'type' => 'INT',
          'targets' => [],
          'default' => 2,
        ])));

        $john = Person::first();
        $john->attachProperty('LIMB_COUNT', 48);
        $john->attachProperty('EYE_COLOUR', 'Blue');

        $this->assertTrue($john->properties()->find('LIMB_COUNT')->value == 48);
        $this->assertTrue($john->properties()->find('EYE_COLOUR')->value == 'Blue');
    }

    public function testPropertyDefaultsAreSetWhenNotProvided()
    {
        $this->assertTrue(!is_null(Property::create([
          'key' => 'EYE_COLOUR',
          'type' => 'STRING',
          'targets' => [],
          'default' => 'Unknown'
        ])));

        $this->assertTrue(!is_null(Property::create([
          'key' => 'LIMB_COUNT',
          'type' => 'INT',
          'targets' => [],
          'default' => 2222,
        ])));

        $john = Person::first();
        $john->attachProperty('LIMB_COUNT');
        $john->attachProperty('EYE_COLOUR');

        $this->assertTrue($john->properties()->find('LIMB_COUNT')->value == 2222);
        $this->assertTrue($john->properties()->find('EYE_COLOUR')->value == 'Unknown');
    }

    public function testValuesAreCorrectlyCast()
    {
        $this->assertTrue(!is_null(Property::create([
          'key' => 'EYE_COLOUR',
          'type' => 'STRING',
          'targets' => [],
          'default' => 'Unknown'
        ])));

        $this->assertTrue(!is_null(Property::create([
          'key' => 'LIMB_COUNT',
          'type' => 'INT',
          'targets' => [],
          'default' => '2222',
        ])));

        $this->assertTrue(!is_null(Property::create([
          'key' => 'CONFIG',
          'type' => 'JSON',
          'targets' => [],
          'default' => ['test' => 'something'],
        ])));

        $this->assertTrue(!is_null(Property::create([
          'key' => 'IS_TALL_PERSON',
          'type' => 'BOOL',
          'targets' => [],
          'default' => true,
        ])));

        $john = Person::first();
        $john->attachProperty('EYE_COLOUR', 'Blue');
        $john->attachProperty('LIMB_COUNT', 700);
        $john->attachProperty('CONFIG', ['bouncing' => 'ball', 'heavy' => 'egg']);
        $john->attachProperty('IS_TALL_PERSON', true);

        $this->assertTrue(is_int($john->properties()->find('LIMB_COUNT')->value));
        $this->assertTrue(is_string($john->properties()->find('EYE_COLOUR')->value));
        $this->assertTrue(is_object($john->properties()->find('CONFIG')->value));
        $this->assertTrue($john->properties()->find('IS_TALL_PERSON')->value === true);
    }
}
