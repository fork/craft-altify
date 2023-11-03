<?php

namespace fork\alter\jobs;

use craft\errors\ElementNotFoundException;
use craft\queue\BaseJob;
use fork\alter\exception\ImageNotSavedException;
use fork\alter\exception\NotAnImageException;
use fork\alter\Plugin;
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
