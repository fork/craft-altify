<?php

namespace fork\alt\connectors\alttextgeneration;

use Craft;
use craft\elements\Asset;
use craft\fs\Local;
use fork\alt\connectors\AbstractHuggingFaceConnector;
use fork\alt\exception\NotALocalFileException;
use fork\alt\Plugin;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Utils;
use yii\base\InvalidConfigException;

abstract class AbstractHuggingFaceAltTextGenerator extends AbstractHuggingFaceConnector
    implements AltTextGeneratorInterface
{
    protected string $modelPath = '';

    /**
     * @throws GuzzleException
     * @throws InvalidConfigException
     * @throws NotALocalFileException
     */
    public function generateAltTextForImage(Asset $image): ?string
    {
        $fs = $image->getVolume()->getFs();
        if (!($fs instanceof Local)) {
            throw new NotALocalFileException("Image is not a local file");
        }

        $fsPath = Craft::getAlias($fs->path);
        $absPath = $fsPath . DIRECTORY_SEPARATOR . $image->getPath();

        $client = $this->getClient();

        $body = Utils::tryFopen($absPath, 'r');
        $response = $client->post(
            $this->modelPath,
            ['body' => $body]
        );

        $body = $response->getBody();
        $decoded = json_decode($body, true);
        $first = !empty($decoded) ? $decoded[0] : null;

        return $first ? $this->filterWords($first['generated_text']) : null;
    }

    protected function filterWords(string $text): string
    {
        foreach (Plugin::getInstance()->getSettings()->wordsBlackList as $word) {
            $text = str_replace($word, '', $text);
        }

        return trim($text);
    }
}
