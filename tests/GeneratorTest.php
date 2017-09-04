<?php

namespace Tests\Packages\Ngtfkx\LaradeckAddressGenerator;

use Illuminate\Support\Collection;
use Ngtfkx\Laradeck\AddressGenerator\Address;
use Ngtfkx\Laradeck\AddressGenerator\Generator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GeneratorTest extends TestCase
{
    /**
     * @var Generator $generator
     */
    protected $generator;

    protected function setUp()
    {
        parent::setUp();

        $this->generator = new Generator();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->generator = null;
    }

    public function testEmptyCities()
    {
        $this->assertEquals($this->generator->getCityNames(), new Collection());
    }

    public function testAddOneCity()
    {
        $this->generator->addCity('Новосибирск');

        $this->assertEquals(new Collection('Новосибирск'), $this->generator->getCityNames());
    }

    public function testAddOneAndOneCity()
    {
        $this->generator->addCity('Новосибирск');
        $this->generator->addCity('Барнаул');

        $this->assertEquals(new Collection(['Новосибирск', 'Барнаул']), $this->generator->getCityNames());
    }

    public function testAddCitiesAsOne()
    {
        $this->generator->addCities('Новосибирск');

        $this->assertEquals(new Collection('Новосибирск'), $this->generator->getCityNames());
    }

    public function testAddCitiesAsSplat()
    {
        $this->generator->addCities('Новосибирск', 'Барнаул');

        $this->assertEquals(new Collection(['Новосибирск', 'Барнаул']), $this->generator->getCityNames());
    }

    public function testAddCitiesAsArray()
    {
        $this->generator->addCities(['Новосибирск', 'Барнаул']);

        $this->assertEquals(new Collection(['Новосибирск', 'Барнаул']), $this->generator->getCityNames());
    }

    public function testSetCitiesAsSplat()
    {
        $this->generator->addCity('Томск');

        $this->generator->setCities('Новосибирск', 'Барнаул');

        $this->assertEquals(new Collection(['Новосибирск', 'Барнаул']), $this->generator->getCityNames());
    }

    public function testSetCitiesAsArray()
    {
        $this->generator->addCity('Томск');

        $this->generator->setCities(['Новосибирск', 'Барнаул']);

        $this->assertEquals(new Collection(['Новосибирск', 'Барнаул']), $this->generator->getCityNames());
    }

    /**
     * @expectedException \Ngtfkx\Laradeck\AddressGenerator\Exceptions\CityNotFound
     */
    public function testCityNotFound()
    {
        $this->generator->addCity('Город Которого Нет');
    }

    public function testGetRandomAddress()
    {
        $this->assertInstanceOf(Address::class, $this->generator->getRandomAddress());
    }
}
