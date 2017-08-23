<?php

namespace Ngtfkx\LaradeckAddressGenerator;


class Address
{
    /**
     * @var string $locality Населенный пункт
     */
    protected $locality;

    /**
     * @var string $street Улица
     */
    protected $street;

    /**
     * @var string $building Номер дома
     */
    protected $building;

    /**
     * @var float $latitude Широта
     */
    protected $latitude;

    /**
     * @var float $longitude Долгота
     */
    protected $longitude;

    /**
     * Address constructor.
     * @param string $locality
     * @param string $street
     * @param string $building
     * @param float|null $latitude
     * @param float|null  $longitude
     */
    public function __construct($locality, $street, $building, $latitude = null, $longitude = null)
    {
        $this->locality = $locality;
        $this->street = $street;
        $this->building = $building;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * Получить полный адрес (нас. пункт, улица, номер дома)
     *
     * @return string
     */
    public function getFull(): string
    {
        $parts = [
            $this->getLocality(),
            $this->getStreet(),
            $this->getBuilding(),
        ];

        return $this->join($parts);
    }

    /**
     * Получить адрес внутри нас.пункта (только улица и номер дома)
     *
     * @return string
     */
    public function getInsideLocality(): string
    {
        $parts = [
            $this->getStreet(),
            $this->getBuilding(),
        ];

        return $this->join($parts);
    }

    /**
     * Получить название населенного пункта
     *
     * @return string
     */
    public function getLocality(): string
    {
        return $this->locality;
    }

    /**
     * Получить название улицы
     *
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Получить номер дома
     *
     * @return string
     */
    public function getBuilding(): string
    {
        return $this->building;
    }

    /**
     * Получить широту точки на карте соответсвующую адресу
     *
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * Получить долготу точки на карте соответсвующую адресу
     *
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * Преобразовать адрес из частей в строку
     *
     * @param $parts
     * @return string
     */
    protected function join($parts): string {
        $address = implode(', ', $parts);

        return $address;
    }
}