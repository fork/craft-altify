<?php

namespace fork\alt\services;

use Craft;
use craft\elements\Asset;
use craft\errors\ElementNotFoundException;
use fork\alt\exception\ImageNotSavedException;
use fork\alt\exception\NotAnImageException;
use fork\alt\helpers\AssetHelper;
use fork\alt\Plugin;
use Throwable;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Translation service
 */
class Translation extends Component
{
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
}
