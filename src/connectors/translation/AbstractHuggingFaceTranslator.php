<?php

namespace fork\alt\connectors\translation;

use craft\elements\Asset;
use fork\alt\connectors\AbstractHuggingFaceConnector;
use GuzzleHttp\Exception\GuzzleException;

class AbstractHuggingFaceTranslator extends AbstractHuggingFaceConnector implements TranslatorInterface
{
    protected string $modelPath = '/models/Helsinki-NLP/opus-mt-en-de';

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
