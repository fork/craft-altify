<?php

namespace fork\altify\connectors\alttextgeneration;

class HuggingFaceBlipLargeAltTextGenerator extends AbstractHuggingFaceAltTextGenerator
{
    protected string $name = 'BLIP large model (Hugging Face)';
    protected string $handle = 'hfBlipLarge';
    protected string $modelPath = '/models/Salesforce/blip-image-captioning-large';
}
