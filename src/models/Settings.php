<?php

namespace fork\alt\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use fork\alt\connectors\alttextgeneration\AltTextGeneratorInterface;
use fork\alt\connectors\alttextgeneration\HuggingFaceBlipBaseAltTextGenerator;
use fork\alt\connectors\alttextgeneration\HuggingFaceBlipLargeAltTextGenerator;
use fork\alt\connectors\translation\HuggingFaceOpusMtEnDeTranslator;
use fork\alt\connectors\translation\TranslatorInterface;
use fork\alt\Plugin;
use yii\base\InvalidConfigException;

/**
 * alt settings
 */
class Settings extends Model
{
    public const GENERATOR_HUGGING_FACE_BLIP_LARGE = 'BLIP large model (Hugging Face)';
    public const GENERATOR_HUGGING_FACE_BLIP_BASE = 'BLIP base model (Hugging Face)';
    private const GENERATOR_MAPPING = [
        self::GENERATOR_HUGGING_FACE_BLIP_LARGE => HuggingFaceBlipLargeAltTextGenerator::class,
        self::GENERATOR_HUGGING_FACE_BLIP_BASE => HuggingFaceBlipBaseAltTextGenerator::class
    ];
    public const TRANSLATOR_HUGGING_FACE_OPUS_MT = 'OPUS MT En -> De';
    private const TRANSLATOR_MAPPING = [
        self::TRANSLATOR_HUGGING_FACE_OPUS_MT => HuggingFaceOpusMtEnDeTranslator::class,
    ];

    public ?string $altTextGenerator = null;
    public ?string $altTextTranslator = null;
    public ?string $apiToken = null;

    public array $wordsBlackList = [
        'arafed',
        'araffes',
        'araffe',
    ];

    public function getApiToken(): ?string
    {
        return App::parseEnv($this->apiToken);
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
            'label' => Craft::t('alt', 'Generators'),
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
                'alt',
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
            $className = self::TRANSLATOR_MAPPING[$this->altTextGenerator ?? self::TRANSLATOR_HUGGING_FACE_OPUS_MT];
        }
        if (!is_a($className, TranslatorInterface::class, true)) {
            throw new InvalidConfigException(Craft::t(
                'alt',
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
