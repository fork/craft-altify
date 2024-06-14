<?php

namespace fork\altify\events;

use yii\base\Event;

class RegisterGeneratorsEvent extends Event
{
    public array $generators = [];
}
