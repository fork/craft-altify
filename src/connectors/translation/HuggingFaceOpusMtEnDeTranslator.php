<?php

namespace fork\altify\connectors\translation;

class HuggingFaceOpusMtEnDeTranslator extends AbstractHuggingFaceTranslator
{
    protected string $name = 'OPUS MT En -> De';
    protected string $handle = 'opusMtEnDe';
    protected string $modelPath = '/models/Helsinki-NLP/opus-mt-en-de';
}
