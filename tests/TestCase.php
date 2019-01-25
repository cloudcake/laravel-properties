<?php

namespace Properties\Tests;

use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Properties\Tests\Models\Person;

abstract class TestCase extends BaseTestCase
{
    public function setup()
    {
        parent::setup();

        $this->app->setBasePath(__DIR__.'/../');

        $this->artisan('migrate');

        Schema::create('people', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Person::create(['name' => 'John Doe']);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Properties\PropertiesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}
