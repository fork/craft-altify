<?php

namespace fork\altify\connectors\translation;

class HuggingFaceT5SmallTranslator extends AbstractHuggingFaceTranslator
{
    protected string $modelPath = '/models/google-t5/t5-small';
}