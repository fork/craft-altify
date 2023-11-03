<?php

namespace fork\alter\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use fork\alter\connectors\alttextgeneration\AltTextGeneratorInterface;
use fork\alter\connectors\alttextgeneration\HuggingFaceBlipBaseAltTextGenerator;
use fork\alter\connectors\alttextgeneration\HuggingFaceBlipLargeAltTextGenerator;
use fork\alter\Plugin;
use yii\base\InvalidConfigException;

/**
 * alter settings
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
            'label' => Craft::t('alter', 'Generators'),
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
                'alter',
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
