<?php

namespace fork\altify\connectors\translation;

class HuggingFaceT5SmallTranslator extends AbstractHuggingFaceTranslator
{
    protected string $name = 'T5 small En -> De';
    protected string $handle = 't5SmallEnDe';
    protected string $modelPath = '/models/google-t5/t5-small';
}
