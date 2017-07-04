<?php
namespace SkydiveMarius\HWM\Server\Src;

use InfluxDB\Client;
use InfluxDB\Database;
use InfluxDB\Point;

/**
 * Class InfluxDbRepository
 *
 * @package SkydiveMarius\HWM\Server\Src
 */
class InfluxDbRepository
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Database
     */
    private $database;

    /**
     * InfluxDbRepository constructor.
     *
     * @param Client      $client
     * @param string|null $database
     */
    public function __construct(Client $client = null, string $database = null)
    {
        $this->client = $client ?: new Client(getenv('INFLUXDB_HOST'), getenv('INFLUXDB_PORT'), getenv('INFLUXDB_USERNAME'), getenv('INFLUXDB_PASSWORD'));
        $this->database = $this->client->selectDB($database ?: getenv('INFLUXDB_DB'));
    }

    /**
     * @param float $value
     */
    public function add(float $value)
    {
        $this->database->writePoints([new Point('distance', $value)]);
    }
}