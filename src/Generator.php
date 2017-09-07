<?php

namespace Ngtfkx\Laradeck\AddressGenerator;


use Illuminate\Support\Collection;
use Ngtfkx\Laradeck\AddressGenerator\Exceptions\CityDataFileNotFound;
use Ngtfkx\Laradeck\AddressGenerator\Exceptions\CityListFileNotFound;
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
     * @var Collection Список городов, для которых сгенерированы адреса
     */
    protected $usedCities;

    /**
     * Generator constructor.
     */
    public function __construct()
    {
        $this->cities = new Collection();

        $this->usedCities = new Collection();

        $this->availableAddresses = new Collection();

        $this->makeCities();
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

        $forCity = $this->prepare($forCity);

        $cityId = $this->getCityIdByName($forCity);

        if ($this->usedCities->contains($cityId) === false) {
            $this->makeAddresses($cityId);
        }

        $address = $this->availableAddresses->get($cityId)->random();

        return $address;
    }

    /**
     * Получить все сгенированные адреса
     *
     * @return Collection
     */
    public function getAllAddresses(): Collection
    {
        return $this->availableAddresses;
    }

    /**
     * Получить несколько случайных адресов
     *
     * @param int $count Кол-во требуемых адресов
     * @param null $forCity Имя города, для которого генерировать адрес. По умолчанию null - из любого установленного
     * @return Collection
     */
    public function getRandomAddresses(int $count, $forCity = null): Collection
    {
        $addresses = new Collection();

        for ($i = 1; $i <= $count; $i++) {
            $addresses->push($this->getRandomAddress($forCity));
        }

        return $addresses;
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
            $this->makeAddresses($key);
        }

        return $this;
    }

    /**
     * Добавить несколько городо в список, для которого будем генерировать адреса
     *
     * @param array ...$cities
     * @return Generator
     */
    public function addCities(): Generator
    {
        $cities = func_get_args();

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
    public function setCities(): Generator
    {
        $cities = func_get_args();

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

        $this->clearAddresses();

        return $this;
    }

    /**
     * Очистить список сгенерированных адресов
     *
     * @return Generator
     */
    public function clearAddresses(): Generator
    {
        $this->availableAddresses = new Collection();

        $this->usedCities = new Collection();

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
        $key = $this->searchableCityNames->get($this->prepare($name));

        if (empty($key)) {
            throw new CityNotFound();
        }

        return $key;
    }

    /**
     * Генерируем коллекцию всех доступных адресов для указанного города
     *
     * @param int $cityId
     */
    protected function makeAddresses(int $cityId)
    {
        $city = $this->availableCities->get($cityId);

        $addresses = new Collection();

        foreach ($this->loadData($cityId) as $street => $buildings) {
            foreach ($buildings as $building) {
                $address = new Address($city, $street, $building);
                $addresses->push($address);
            }
        }

        $this->usedCities->push($cityId);

        $this->availableAddresses->put($cityId, $addresses);
    }

    /**
     * Загрузка сырых данных по городу
     *
     * @param int $cityId
     * @return array
     * @throws CityDataFileNotFound
     */
    protected function loadData(int $cityId): array
    {
        $file = __DIR__ . '/data/ru/' . $cityId . '.php';

        if (!file_exists($file)) {
            throw new CityDataFileNotFound();
        }

        $rawData = require($file);

        return $rawData;
    }

    /**
     * Загрузка списка городов с алиасами
     *
     * @return array
     * @throws CityListFileNotFound
     */
    protected function loadCities(): array
    {
        $file = __DIR__ . '/data/cities.php';

        if (!file_exists($file)) {
            throw new CityListFileNotFound();
        }

        $rawData = require($file);

        return $rawData;
    }

    /**
     * Генерирум коллекцию всех доступных городов
     */
    protected function makeCities()
    {
        $this->availableCities = new Collection();

        $this->searchableCityNames = new Collection();

        foreach ($this->loadCities() as $key => $items) {

            $this->availableCities->put($key, $items[0]);

            foreach ($items as $item) {
                $item = $this->prepare($item);

                if (!$this->searchableCityNames->contains($item)) {
                    $this->searchableCityNames->put($item, $key);
                }
            }
        }
    }

    /**
     * Приведение разных вариантов написания наименования города к общему виду
     *
     * @param $string
     * @return string
     */
    protected function prepare($string): string
    {
        $string = str_replace([' ', '-', '_'], '', mb_strtolower($string));

        return $string;
    }
}