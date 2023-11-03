<?php

namespace fork\alter\connectors\alttextgeneration;

class HuggingFaceBlipBaseAltTextGenerator extends AbstractHuggingFaceAltTextGenerator
{
    protected string $modelPath = '/models/Salesforce/blip-image-captioning-base';
}
