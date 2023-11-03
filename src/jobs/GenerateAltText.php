<?php

namespace fork\alt\jobs;

use craft\errors\ElementNotFoundException;
use craft\queue\BaseJob;
use fork\alt\exception\ImageNotSavedException;
use fork\alt\exception\NotAnImageException;
use fork\alt\Plugin;
use Throwable;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Generate Alt Text queue job
 */
class GenerateAltText extends BaseJob
{
    public int $assetId;

    /**
     * @throws ImageNotSavedException
     * @throws ElementNotFoundException
     * @throws Throwable
     * @throws NotAnImageException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function execute($queue): void
    {
        Plugin::getInstance()->altTextGeneration->generateAltTextForImage($this->assetId);
    }

    protected function defaultDescription(): ?string
    {
        return "Generate alt text";
    }
}
