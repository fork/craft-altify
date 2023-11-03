<?php

namespace fork\alter\connectors\alttextgeneration;

use craft\elements\Asset;
use fork\alter\connectors\ConnectorInterface;

interface AltTextGeneratorInterface extends ConnectorInterface
{
    public function generateAltTextForImage(Asset $image): ?string;
}
