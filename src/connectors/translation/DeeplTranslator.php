<?php

namespace fork\altify\connectors\translation;

use craft\elements\Asset;
use DeepL\DeepLException;
use DeepL\Translator;
use fork\altify\Plugin;

class DeeplTranslator implements TranslatorInterface
{
    protected string $name = 'DeepL';
    protected string $handle = 'deepl';

    public function getName(): string
    {
        return $this->name;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

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
