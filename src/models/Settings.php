<?php

namespace fork\altify\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use fork\altify\connectors\alttextgeneration\AltTextGeneratorInterface;
use fork\altify\connectors\alttextgeneration\HuggingFaceBlipBaseAltTextGenerator;
use fork\altify\connectors\alttextgeneration\HuggingFaceBlipLargeAltTextGenerator;
use fork\altify\connectors\translation\DeeplTranslator;
use fork\altify\connectors\translation\HuggingFaceOpusMtEnDeTranslator;
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
    public const GENERATOR_HUGGING_FACE_BLIP_LARGE = 'BLIP large model (Hugging Face)';
    public const GENERATOR_HUGGING_FACE_BLIP_BASE = 'BLIP base model (Hugging Face)';
    private const GENERATOR_MAPPING = [
        self::GENERATOR_HUGGING_FACE_BLIP_LARGE => HuggingFaceBlipLargeAltTextGenerator::class,
        self::GENERATOR_HUGGING_FACE_BLIP_BASE => HuggingFaceBlipBaseAltTextGenerator::class
    ];
    public const TRANSLATOR_DEEPL = 'DeepL';
    public const TRANSLATOR_HUGGING_FACE_OPUS_MT = 'OPUS MT En -> De';
    public const TRANSLATOR_HUGGING_FACE_T5_SMALL = 'T5 small En -> De';
    private const TRANSLATOR_MAPPING = [
        self::TRANSLATOR_DEEPL => DeeplTranslator::class,
        self::TRANSLATOR_HUGGING_FACE_OPUS_MT => HuggingFaceOpusMtEnDeTranslator::class,
        self::TRANSLATOR_HUGGING_FACE_T5_SMALL => HuggingFaceT5SmallTranslator::class,
    ];

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

    public function getGeneratorSuggestions(): array
    {
        $data = [];

        foreach (self::GENERATOR_MAPPING as $name => $hint) {
            $data[] = [
                'name' => $name,
                'hint' => $hint
            ];
        }

        return [[
            'label' => Craft::t('altify', 'Generators'),
            'data' => $data
        ]];
    }

    public function getTranslatorSuggestions(): array
    {
        $data = [];

        foreach (self::TRANSLATOR_MAPPING as $name => $hint) {
            $data[] = [
                'name' => $name,
                'hint' => $hint
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
        if (class_exists($altTextGenerator)) {
            $className = $altTextGenerator;
        } else {
            $className = self::GENERATOR_MAPPING[$this->altTextGenerator ?? self::GENERATOR_HUGGING_FACE_BLIP_LARGE];
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
        if (class_exists($altTextTranslator)) {
            $className = $altTextTranslator;
        } else {
            $className = self::TRANSLATOR_MAPPING[$this->altTextTranslator ?? self::TRANSLATOR_HUGGING_FACE_T5_SMALL];
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
