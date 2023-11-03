<?php

namespace fork\alter\connectors\alttextgeneration;

class HuggingFaceBlipLargeAltTextGenerator extends AbstractHuggingFaceAltTextGenerator
{
    protected string $modelPath = '/models/Salesforce/blip-image-captioning-large';
}
