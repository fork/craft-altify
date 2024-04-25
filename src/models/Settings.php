<?php

namespace fork\altify\models;

use Craft;
use craft\base\Model;
use craft\helpers\App;
use fork\altify\connectors\alttextgeneration\AltTextGeneratorInterface;
use fork\altify\connectors\alttextgeneration\HuggingFaceBlipBaseAltTextGenerator;
use fork\altify\connectors\alttextgeneration\HuggingFaceBlipLargeAltTextGenerator;
use fork\altify\connectors\ConnectorInterface;
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
    private const GENERATORS = [
        HuggingFaceBlipLargeAltTextGenerator::class,
        HuggingFaceBlipBaseAltTextGenerator::class
    ];

    private const TRANSLATORS = [
        DeeplTranslator::class,
        HuggingFaceOpusMtEnDeTranslator::class,
        HuggingFaceT5SmallTranslator::class,
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

    /**
     * @return array[]
     * @noinspection PhpUnused
     */
    public function getGeneratorSuggestions(): array
    {
        $data = [];

        foreach (self::getAvailableGenerators() as $handle => $classname) {
            /** @var AltTextGeneratorInterface $obj */
            $obj = new $classname();

            $data[] = [
                'name' => $obj->getName(),
                'hint' => $handle
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

        foreach (self::getAvailableTranslators() as $handle => $classname) {
            /** @var TranslatorInterface $obj */
            $obj = new $classname();

            $data[] = [
                'name' => $obj->getName(),
                'hint' => $handle
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
        $generators = $this->getAvailableGenerators();

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
        $translators = self::getAvailableTranslators();

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

    private static function getAvailableGenerators(): array
    {
        return self::buildConnectorArray(self::GENERATORS);
    }

    private static function getAvailableTranslators(): array
    {
        return self::buildConnectorArray(self::TRANSLATORS);
    }

    private static function buildConnectorArray(array $classnames): array
    {
        $data = [];

        foreach ($classnames as $classname) {
            /** @var ConnectorInterface $obj */
            $obj = new $classname();

            $data[$obj->getHandle()] = $classname;
        }

        return $data;
    }
}
