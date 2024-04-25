<?php

namespace fork\altify\connectors;

interface ConnectorInterface {
    public function getName(): string;
    public function getHandle(): string;
}
