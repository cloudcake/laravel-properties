<?php

namespace Properties\Tests\Unit;

use Properties\Models\Property;
use Properties\Tests\Models\Person;
use Properties\Tests\TestCase;

class PropertiesTest extends TestCase
{
    public function testPropertyCanBeCreated()
    {
        $property = Property::make('EYE_COLOUR', 'STRING', 'Unknown');

        $this->assertTrue(!is_null($property));
    }

    public function testPropertiesAreAssignable()
    {
        $eyeColour = Property::make('EYE_COLOUR', 'STRING', 'Unknown');
        $limbCount = Property::make('LIMB_COUNT', 'STRING', 'Unknown');

        $john = Person::first();
        $john->attachProperty($limbCount, 48);
        $john->attachProperty($eyeColour, 'Blue');

        $this->assertTrue($john->property('LIMB_COUNT') == 48);
        $this->assertTrue($john->property('EYE_COLOUR') == 'Blue');
    }

    public function testPropertyDefaultsAreSetWhenNotProvided()
    {
        $eyeColour = Property::make('EYE_COLOUR', 'STRING', 'Unknown');
        $limbCount = Property::make('LIMB_COUNT', 'STRING', 'Unknown');

        $john = Person::first();
        $john->attachProperty('EYE_COLOUR');
        $john->attachProperty('LIMB_COUNT');

        $this->assertTrue($john->property('LIMB_COUNT') == 'Unknown');
        $this->assertTrue($john->property('EYE_COLOUR') == 'Unknown');
    }

    public function testValuesAreCorrectlyCast()
    {
        Property::make('EYE_COLOUR', 'STRING', 'Unknown');
        Property::make('LIMB_COUNT', 'INT', 2);
        Property::make('CONFIG', 'JSON', ['username' => 'JohnDoe']);
        Property::make('IS_WORKING', 'BOOL', true);

        $john = Person::first();
        $john->attachProperty('EYE_COLOUR', 'Blue');
        $john->attachProperty('LIMB_COUNT', 700);
        $john->attachProperty('CONFIG', ['bouncing' => 'ball', 'heavy' => 'egg']);
        $john->attachProperty('IS_WORKING', false);

        $this->assertTrue(is_int($john->property('LIMB_COUNT')));
        $this->assertTrue(is_string($john->property('EYE_COLOUR')));
        $this->assertTrue(is_bool($john->property('IS_WORKING')));
    }
}
