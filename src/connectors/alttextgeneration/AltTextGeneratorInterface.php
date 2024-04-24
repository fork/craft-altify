<?php

namespace fork\altify\connectors\alttextgeneration;

use craft\elements\Asset;
use fork\altify\connectors\ConnectorInterface;

interface AltTextGeneratorInterface extends ConnectorInterface
{
    public function generateAltTextForImage(Asset $image): ?string;
}
