<?php

namespace fork\altify\services;

use Craft;
use craft\elements\Asset;
use craft\errors\ElementNotFoundException;
use fork\altify\connectors\translation\DeeplTranslator;
use fork\altify\connectors\translation\HuggingFaceOpusMtEnDeTranslator;
use fork\altify\connectors\translation\HuggingFaceT5SmallTranslator;
use fork\altify\events\RegisterTranslatorsEvent;
use fork\altify\exception\ImageNotSavedException;
use fork\altify\exception\NotAnImageException;
use fork\altify\helpers\AssetHelper;
use fork\altify\Plugin;
use Throwable;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Translation service
 *
 * @property-read array $availableTranslators
 */
class Translator extends AbstractConnectorService
{
    public const EVENT_REGISTER_TRANSLATORS = 'registerTranslators';
    private const TRANSLATORS = [
        DeeplTranslator::class,
        HuggingFaceOpusMtEnDeTranslator::class,
        HuggingFaceT5SmallTranslator::class,
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
    public function translateAltTextForImage(int $assetId): void
    {
        $image = Craft::$app->elements->getElementById($assetId);
        AssetHelper::validateImage($image);
        /** @var Asset $image */
        $image->alt = $this->translateAltText($image);

        if (!Craft::$app->elements->saveElement($image)) {
            throw new ImageNotSavedException(
                "Image could not be saved, reasons: " . json_encode($image->getErrors(), JSON_PRETTY_PRINT)
            );
        }
    }

    /**
     * @throws InvalidConfigException
     */
    public function translateAltText(Asset $image): string
    {
        return Plugin::getInstance()->getSettings()->getAltTextTranslator()->translateAltTextForImage($image);
    }

    /**
     * @return array
     */
    public function getAvailableTranslators(): array
    {
        $translators = self::buildConnectorArray(self::TRANSLATORS);
        $registerTranslatorsEvent = new RegisterTranslatorsEvent(['translators' => $translators]);
        $this->trigger(self::EVENT_REGISTER_TRANSLATORS, $registerTranslatorsEvent);

        return $registerTranslatorsEvent->translators;
    }
}
