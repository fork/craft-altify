<?php

namespace fork\altify\events;

use yii\base\Event;

class RegisterTranslatorsEvent extends Event
{
    public array $translators = [];
}
