<?php

namespace Tests\Packages\Ngtfkx\LaradeckAddressGenerator;

use Ngtfkx\LaradeckAddressGenerator\Generator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddressTest extends TestCase
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

    public function testLocalityProperty()
    {
        $property = $this->generator->getRandomAddress()->getLocality();

        $this->assertNotEmpty($property);
    }

    public function testStreetProperty()
    {
        $property = $this->generator->getRandomAddress()->getStreet();

        $this->assertNotEmpty($property);
    }

    public function testBuildingProperty()
    {
        $property = $this->generator->getRandomAddress()->getBuilding();

        $this->assertNotEmpty($property);
    }

    public function testGetFullMethod()
    {
        $value = $this->generator->getRandomAddress()->getFull();

        $this->assertNotEmpty($value);
    }

    public function testGetInsideLocalityMethod()
    {
        $value = $this->generator->getRandomAddress()->getInsideLocality();

        $this->assertNotEmpty($value);
    }

    public function testGetAddressForCity()
    {
        $this->generator->setCities('tomsk');

        $address = $this->generator->getRandomAddress();

        $this->assertEquals('Томск', $address->getLocality());
    }

    public function testGetAddressForCityAsParam()
    {
        $this->generator->setCities('tomsk');

        $address = $this->generator->getRandomAddress('Новосибирск');

        $this->assertEquals('Новосибирск', $address->getLocality());
    }

    public function testAddressHasCityName()
    {
        $this->generator->setCities('Новосибирск');

        $address = $this->generator->getRandomAddress();

        $this->assertContains('Новосибирск', $address->getFull());
    }

    public function testAddressHasNotCityName()
    {
        $this->generator->setCities('Новосибирск');

        $address = $this->generator->getRandomAddress();

        $this->assertNotContains('Новосибирск', $address->getInsideLocality());
    }
}
