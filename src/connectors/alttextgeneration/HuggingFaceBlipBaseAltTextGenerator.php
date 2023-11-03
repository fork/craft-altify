<?php

namespace fork\alt\connectors\alttextgeneration;

class HuggingFaceBlipBaseAltTextGenerator extends AbstractHuggingFaceAltTextGenerator
{
    protected string $modelPath = '/models/Salesforce/blip-image-captioning-base';
}
