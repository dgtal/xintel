<?php

namespace DGtal\Xintel\Adapter;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use InvalidArgumentException;

/**
 * Class GuzzleHttpAdapter
 */
class GuzzleHttpAdapter implements ConnectorInterface
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     * @return mixed
     */
    public function connect(array $config)
    {
        $this->config = $this->getConfig($config);

        return $this->getAdapter();
    }

    /**
     * @param $config
     * @return array|null
     * @throws \InvalidArgumentException
     */
    private function getConfig($config)
    {
        if (!array_key_exists('identifier', $config) || empty($config['identifier'])) {
            throw new InvalidArgumentException('The guzzlehttp connector requires configuration.');
        }

        if (!array_key_exists('secret', $config) || empty($config['secret'])) {
            throw new InvalidArgumentException('The guzzlehttp connector requires configuration.');
        }

        return $config;

        throw new InvalidArgumentException('Unsupported auth type');
    }

    /**
     * @return Client
     */
    private function getAdapter()
    {
        return new Client([
            'base_uri' => $this->config['apiurl'],
            'timeout' => 30,
            'form_params' => [
                'inm' => $this->config['identifier'],
                'apiK' => $this->config['secret'],
            ],
            'headers' => [
                'User-Agent' => 'Xintel API Interface',
            ]
        ]);
    }
}
