<?php

namespace DGtal\Xintel;

use DGtal\Xintel\Adapter\ConnectorInterface;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Str;

/**
 * Class XintelManager
 */
class XintelManager
{
    /**
     * @var
     */
    protected $config;

    /**
     * @var ConnectorInterface
     */
    protected $client;

    /**
     * Xintel constructor.
     * @param array $config
     * @param ConnectorInterface $client
     */
    public function __construct(array $config, ConnectorInterface $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * @return ConnectorInterface
     */
    public function connection()
    {
        return $this->client->connect($this->getConfig());
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    protected function execute(string $method, $parameters = [])
    {
        $parameters['json'] = Str::snake($method, '.');

        try {
            $response = $this->connection()->post('/', [
                'form_params' => array_merge($parameters, $this->connection()->getConfig()['form_params']),
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (ClientException $ex) {
            $response = json_decode($ex->getResponse()->getBody()->getContents(), true);
            return $response;
        }
    }

    /**
     * Get the config array.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call(string $method, $parameters)
    {
        return $this->execute($method, ...$parameters);
    }
}
