<?php

namespace fork\alt\helpers;

use craft\base\Element;
use craft\elements\Asset;
use craft\errors\ElementNotFoundException;
use fork\alt\exception\NotAnImageException;

class AssetHelper
{
    /**
     * @param ?Element $image
     * @return void
     * @throws ElementNotFoundException
     * @throws NotAnImageException
     */
    public static function validateImage(?Element $image): void
    {
        if (!$image) {
            throw new ElementNotFoundException("Image doesn't exist");
        }
        if (!($image instanceof Asset)) {
            throw new NotAnImageException("Element is not an asset");
        }
        if ($image->kind !== Asset::KIND_IMAGE) {
            throw new NotAnImageException("Asset is not an image");
        }
    }
}
