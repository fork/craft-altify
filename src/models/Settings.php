<?php

namespace fork\altify\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use fork\altify\connectors\alttextgeneration\AltTextGeneratorInterface;
use fork\altify\connectors\alttextgeneration\HuggingFaceBlipLargeAltTextGenerator;
use fork\altify\connectors\translation\HuggingFaceT5SmallTranslator;
use fork\altify\connectors\translation\TranslatorInterface;
use fork\altify\Plugin;
use yii\base\InvalidConfigException;

/**
 * altify settings
 *
 * @property-read array[] $translatorSuggestions
 * @property-read array[] $generatorSuggestions
 */
class Settings extends Model
{
    public ?string $altTextGenerator = null;
    public ?string $altTextTranslator = null;
    public ?string $huggingFaceApiToken = null;
    public ?string $deeplApiKey = null;

    public array $wordsBlackList = [
        'arafed',
        'araffes',
        'araffe',
    ];

    public function getHuggingFaceApiToken(): ?string
    {
        return App::parseEnv($this->huggingFaceApiToken);
    }

    public function getDeeplApiKey(): ?string
    {
        return App::parseEnv($this->deeplApiKey);
    }

    /**
     * @return array[]
     * @noinspection PhpUnused
     */
    public function getGeneratorSuggestions(): array
    {
        $data = [];

        foreach (Plugin::getInstance()->generator->getAvailableGenerators() as $handle => $classname) {
            /** @var AltTextGeneratorInterface $obj */
            $obj = new $classname();

            $data[] = [
                'name' => $handle,
                'hint' => $obj->getName()
            ];
        }

        return [[
            'label' => Craft::t('altify', 'Generators'),
            'data' => $data
        ]];
    }

    /**
     * @return array[]
     * @noinspection PhpUnused
     */
    public function getTranslatorSuggestions(): array
    {
        $data = [];

        foreach (Plugin::getInstance()->translator->getAvailableTranslators() as $handle => $classname) {
            /** @var TranslatorInterface $obj */
            $obj = new $classname();

            $data[] = [
                'name' => $handle,
                'hint' => $obj->getName()
            ];
        }

        return [[
            'label' => Craft::t('altify', 'Translators'),
            'data' => $data
        ]];
    }

    /**
     * @throws InvalidConfigException
     */
    public function getAltTextGenerator(): AltTextGeneratorInterface
    {
        $altTextGenerator = App::parseEnv($this->altTextGenerator);
        $generators = Plugin::getInstance()->generator->getAvailableGenerators();

        if (key_exists($altTextGenerator, $generators)) {
            $className = $generators[$altTextGenerator];
        } else {
            $className = HuggingFaceBlipLargeAltTextGenerator::class;
        }
        if (!is_a($className, AltTextGeneratorInterface::class, true)) {
            throw new InvalidConfigException(Craft::t(
                'altify',
                '{class} must implement {interface}',
                [
                    'class' => Plugin::getInstance()->getSettings()->altTextGenerator,
                    'interface' => AltTextGeneratorInterface::class
                ]
            ));
        }

        return new $className;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getAltTextTranslator(): TranslatorInterface
    {
        $altTextTranslator = App::parseEnv($this->altTextTranslator);
        $translators = Plugin::getInstance()->translator->getAvailableTranslators();

        if (key_exists($altTextTranslator, $translators)) {
            $className = $translators[$altTextTranslator];
        } else {
            $className = HuggingFaceT5SmallTranslator::class;
        }
        if (!is_a($className, TranslatorInterface::class, true)) {
            throw new InvalidConfigException(Craft::t(
                'altify',
                '{class} must implement {interface}',
                [
                    'class' => Plugin::getInstance()->getSettings()->altTextTranslator,
                    'interface' => TranslatorInterface::class
                ]
            ));
        }

        return new $className;
    }
}
