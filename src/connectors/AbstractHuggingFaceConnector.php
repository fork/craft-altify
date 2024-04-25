<?php

namespace fork\altify\connectors;

use fork\altify\Plugin;
use GuzzleHttp\Client;

abstract class AbstractHuggingFaceConnector implements ConnectorInterface
{
    protected string $name = '';
    protected string $handle = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    protected function getClient(): Client
    {
        return new Client([
            'base_uri' => "https://api-inference.huggingface.co",
            'headers' => [
                'Authorization' => 'Bearer ' . Plugin::getInstance()->getSettings()->getHuggingFaceApiToken(),
            ],
        ]);
    }
}
