<?php

namespace Ngtfkx\Laradeck\AddressGenerator;


use Illuminate\Support\Collection;
use Ngtfkx\Laradeck\AddressGenerator\Exceptions\CityNotFound;

class Generator
{
    /**
     * @var Collection Список городов для которых будем генерировать адреса
     */
    protected $cities;

    /**
     * @var Collection Список доступных для поиска имен городов городов
     */
    protected $searchableCityNames;

    /**
     * @var Collection Список достпупных гордов
     */
    protected $availableCities;

    /**
     * @var Collection Список достпупных адресов
     */
    protected $availableAddresses;

    /**
     * Generator constructor.
     */
    public function __construct()
    {
        $this->cities = new Collection();

        $this->makeCities();

        $this->makeAddresses();
    }

    /**
     * Получить случайный адрес
     *
     * @param string|null $forCity Имя города, для которого генерировать адрес. По умолчанию null - из любого установленного
     * @return Address
     * @throws CityNotFound Город не найден в списке доступных
     */
    public function getRandomAddress($forCity = null): Address
    {
        $forCity = $forCity ?: $this->getCities()->random();

        $forCity = Helper::prepare($forCity);

        $cityId = $this->getCityIdByName($forCity);

        $address = $this->availableAddresses->get($cityId)->random();

        return $address;
    }

    /**
     * Добавить город в список, для которого будем генерировать адреса
     *
     * @param string $city
     * @return Generator
     * @throws CityNotFound Город не найден в списке доступных
     */
    public function addCity(string $city): Generator
    {
        $key = $this->getCityIdByName($city);

        if (!$this->cities->contains($city)) {
            $this->cities->put($key, $city);
        }

        return $this;
    }

    /**
     * Добавить несколько городо в список, для которого будем генерировать адреса
     *
     * @param array ...$cities
     * @return Generator
     */
    public function addCities(...$cities): Generator
    {
        foreach ($cities as $city) {
            if (!is_array($city)) {
                $city = [$city];
            }
            foreach ($city as $item) {
                $this->addCity($item);
            }
        }

        return $this;
    }

    /**
     * Установить список городов, для которого будем генерировать адреса
     *
     * @param array ...$cities
     * @return Generator
     */
    public function setCities(...$cities): Generator
    {
        $this->clearCities();

        if (sizeof($cities) === 1 && is_array($cities[0])) {
            $cities = $cities[0];
        }

        $this->addCities($cities);

        return $this;
    }

    /**
     * Очистить список гордов, для которых можно генерировать адреса
     *
     * @return Generator
     */
    public function clearCities(): Generator
    {
        $this->cities = new Collection();

        return $this;
    }

    /**
     * Получить коллекцию городов, для которых можно получать адреса.
     *
     * Если города заданы, то их. Если нет, то все доступные города.
     *
     * @return Collection
     */
    public function getCities(): Collection
    {
        return $this->cities->isEmpty() ? $this->availableCities : $this->cities;
    }

    /**
     * Получить имена городов, установленные для которых можно получать адреса
     *
     * @return Collection
     */
    public function getCityNames(): Collection
    {
        return $this->cities->values();
    }

    /**
     * Получить ID города по его имени
     *
     * @param string $name
     * @return int
     * @throws CityNotFound
     */
    protected function getCityIdByName(string $name): int
    {
        $key = $this->searchableCityNames->get(Helper::prepare($name));

        if (empty($key)) {
            throw new CityNotFound();
        }

        return $key;
    }

    /**
     * Генерируем коллекцию всех доступных адресов
     */
    private function makeAddresses(): void
    {
        $this->availableAddresses = new Collection();

        foreach ($this->availableCities as $key => $city) {
            $rawData = include('data/ru/' . $key . '.php');
            $addresses = new Collection();

            foreach ($rawData as $street => $buildings) {
                foreach ($buildings as $building) {
                    $address = new Address($city, $street, $building);
                    $addresses->push($address);
                }
            }

            $this->availableAddresses->put($key, $addresses);
        }
    }

    /**
     * Генерирум коллекцию всех доступных городов
     */
    private function makeCities(): void
    {
        $this->availableCities = new Collection();

        $this->searchableCityNames = new Collection();

        foreach (include('data/cities.php') as $key => $items) {

            $this->availableCities->put($key, $items[0]);

            foreach ($items as $item) {
                $item = Helper::prepare($item);

                if (!$this->searchableCityNames->contains($item)) {
                    $this->searchableCityNames->put($item, $key);
                }
            }
        }
    }
}