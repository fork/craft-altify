<?php

namespace fork\altify\services;

use Craft;
use craft\elements\Asset;
use craft\errors\ElementNotFoundException;
use fork\altify\connectors\alttextgeneration\HuggingFaceBlipBaseAltTextGenerator;
use fork\altify\connectors\alttextgeneration\HuggingFaceBlipLargeAltTextGenerator;
use fork\altify\events\RegisterGeneratorsEvent;
use fork\altify\exception\ImageNotSavedException;
use fork\altify\exception\NotAnImageException;
use fork\altify\helpers\AssetHelper;
use fork\altify\Plugin;
use Throwable;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Alt Text Generation service
 *
 * @property-read array $availableGenerators
 */
class Generator extends AbstractConnectorService
{
    public const EVENT_REGISTER_GENERATORS = 'registerGenerators';

    private const GENERATORS = [
        HuggingFaceBlipLargeAltTextGenerator::class,
        HuggingFaceBlipBaseAltTextGenerator::class
    ];

    /**
     * @param int $assetId
     * @throws ElementNotFoundException
     * @throws ImageNotSavedException
     * @throws InvalidConfigException
     * @throws NotAnImageException
     * @throws Throwable
     * @throws Exception
     */
    public function generateAltTextForImage(int $assetId): void
    {
        $image = Craft::$app->elements->getElementById($assetId);
        AssetHelper::validateImage($image);
        /** @var Asset $image */
        $image->alt = $this->generateAltText($image);

        if (!Craft::$app->elements->saveElement($image)) {
            throw new ImageNotSavedException(
                "Image could not be saved, reasons: " . json_encode($image->getErrors(), JSON_PRETTY_PRINT)
            );
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function generateAltText(Asset $image): string
    {
        return Plugin::getInstance()->getSettings()->getAltTextGenerator()->generateAltTextForImage($image);
    }

    /**
     * @return array
     */
    public function getAvailableGenerators(): array
    {
        $generators = self::buildConnectorArray(self::GENERATORS);
        $registerGeneratorsEvent = new RegisterGeneratorsEvent(['generators' => $generators]);
        $this->trigger(self::EVENT_REGISTER_GENERATORS, $registerGeneratorsEvent);

        return $registerGeneratorsEvent->generators;
    }
}
