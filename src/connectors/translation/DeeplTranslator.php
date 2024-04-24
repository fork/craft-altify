<?php

namespace fork\alt\connectors\translation;

use craft\elements\Asset;
use DeepL\DeepLException;
use DeepL\Translator;
use fork\alt\Plugin;

class DeeplTranslator implements TranslatorInterface
{
    /**
     * @param Asset $image
     * @return string|null
     * @throws DeepLException
     */
    public function translateAltTextForImage(Asset $image): ?string
    {
        $altText = $image->alt;

        if (!empty($altText)) {
            $translator = new Translator(Plugin::getInstance()->getSettings()->getDeeplApiKey());
            $result = $translator->translateText($altText, null, $image->site->language);
            $altText = $result->text;
        }

        return $altText;
    }
}
