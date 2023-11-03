<?php

namespace fork\alt\connectors\alttextgeneration;

use craft\elements\Asset;
use fork\alt\connectors\ConnectorInterface;

interface AltTextGeneratorInterface extends ConnectorInterface
{
    public function generateAltTextForImage(Asset $image): ?string;
}
