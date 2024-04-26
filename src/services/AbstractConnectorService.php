<?php

namespace fork\altify\services;

use craft\base\Component;
use fork\altify\connectors\ConnectorInterface;

abstract class AbstractConnectorService extends Component
{
    protected static function buildConnectorArray(array $classnames): array
    {
        $data = [];

        foreach ($classnames as $classname) {
            /** @var ConnectorInterface $obj */
            $obj = new $classname();

            $data[$obj->getHandle()] = $classname;
        }

        return $data;
    }
}
