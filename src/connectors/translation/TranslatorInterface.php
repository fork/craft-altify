<?php

namespace fork\alt\connectors\translation;

use craft\elements\Asset;
use fork\alt\connectors\ConnectorInterface;

interface TranslatorInterface extends ConnectorInterface
{
    public function translateAltTextForImage(Asset $image): ?string;
}
