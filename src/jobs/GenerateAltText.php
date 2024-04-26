<?php

namespace fork\altify\jobs;

use craft\errors\ElementNotFoundException;
use craft\queue\BaseJob;
use fork\altify\exception\ImageNotSavedException;
use fork\altify\exception\NotAnImageException;
use fork\altify\Plugin;
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
        Plugin::getInstance()->generator->generateAltTextForImage($this->assetId);
    }

    protected function defaultDescription(): ?string
    {
        return "Generate alt text";
    }
}
