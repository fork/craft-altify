<?php

namespace fork\alter;

use Craft;
use craft\base\Element;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\elements\Asset;
use craft\events\ModelEvent;
use fork\alter\jobs\GenerateAltText;
use fork\alter\models\Settings;
use fork\alter\services\AltTextGeneration;
use fork\alter\services\Translation;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * alter plugin
 *
 * @method static Plugin getInstance()
 * @method Settings getSettings()
 * @author Fork <obj@fork.de>
 * @copyright Fork
 * @license MIT
 * @property-read AltTextGeneration $altTextGeneration
 * @property-read Settings $settings
 * @property-read Translation $translation
 */
class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [
                'altTextGeneration' => AltTextGeneration::class,
                'translation' => Translation::class
            ],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
            // ...
        });
    }

    /**
     * @throws InvalidConfigException
     */
    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    /**
     * @throws SyntaxError
     * @throws Exception
     * @throws RuntimeError
     * @throws LoaderError
     */
    protected function settingsHtml(): ?string
    {
        $settings = $this->getSettings();

        return Craft::$app->view->renderTemplate('alter/_settings.twig', [
            'plugin' => $this,
            'settings' => $settings,
            'altTextGeneratorSuggestions' => $settings->getGeneratorSuggestions()
        ]);
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            Asset::class,
            Element::EVENT_AFTER_SAVE,
            function (ModelEvent $event) {
                $asset = $event->sender;

                if ($asset->firstSave) {
                    Craft::$app->getQueue()->push(new GenerateAltText(['assetId' => $asset->id]));
                }
            }
        );
    }
}
