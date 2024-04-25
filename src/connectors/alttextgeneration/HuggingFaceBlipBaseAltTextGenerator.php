<?php

namespace fork\altify\connectors\alttextgeneration;

class HuggingFaceBlipBaseAltTextGenerator extends AbstractHuggingFaceAltTextGenerator
{
    protected string $name = 'BLIP base model (Hugging Face)';
    protected string $handle = 'hfBlipBase';
    protected string $modelPath = '/models/Salesforce/blip-image-captioning-base';
}
