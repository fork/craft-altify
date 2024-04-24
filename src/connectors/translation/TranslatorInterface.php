<?php

namespace fork\altify\connectors\translation;

use craft\elements\Asset;
use fork\altify\connectors\ConnectorInterface;

interface TranslatorInterface extends ConnectorInterface
{
    public function translateAltTextForImage(Asset $image): ?string;
}
