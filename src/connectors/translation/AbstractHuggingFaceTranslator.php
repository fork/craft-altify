<?php

namespace fork\altify\connectors\translation;

use craft\elements\Asset;
use fork\altify\connectors\AbstractHuggingFaceConnector;
use GuzzleHttp\Exception\GuzzleException;

abstract class AbstractHuggingFaceTranslator extends AbstractHuggingFaceConnector implements TranslatorInterface
{
    protected string $modelPath = '';

    /**
     * @throws GuzzleException
     */
    public function translateAltTextForImage(Asset $image): ?string
    {
        $altText = $image->alt;

        if (!empty($altText)) {
            $client = $this->getClient();

            $response = $client->post(
                $this->modelPath,
                ['json' => ['inputs' => $image->alt]]
            );

            $body = $response->getBody();
            $decoded = json_decode($body, true);
            $altText = !empty($decoded) ? $decoded[0]['translation_text'] : null;
        }

        return $altText;
    }
}
