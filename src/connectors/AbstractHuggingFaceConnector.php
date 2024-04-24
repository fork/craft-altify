<?php

namespace fork\altify\connectors;

use fork\altify\Plugin;
use GuzzleHttp\Client;

class AbstractHuggingFaceConnector implements ConnectorInterface
{
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
