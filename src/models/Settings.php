<?php

namespace fork\alt\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use fork\alt\connectors\alttextgeneration\AltTextGeneratorInterface;
use fork\alt\connectors\alttextgeneration\HuggingFaceBlipBaseAltTextGenerator;
use fork\alt\connectors\alttextgeneration\HuggingFaceBlipLargeAltTextGenerator;
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

    public ?string $altTextGenerator = null;
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
}
