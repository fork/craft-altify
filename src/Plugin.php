<?php

namespace fork\alt;

use Craft;
use craft\base\Element;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin as BasePlugin;
use craft\elements\Asset;
use craft\events\DefineHtmlEvent;
use craft\events\ModelEvent;
use craft\helpers\Html;
use fork\alt\jobs\GenerateAltText;
use fork\alt\models\Settings;
use fork\alt\services\AltTextGeneration;
use fork\alt\services\Translation;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * alt plugin
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

        return Craft::$app->view->renderTemplate('alt/_settings.twig', [
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

        Event::on(
            Asset::class,
            Element::EVENT_DEFINE_ADDITIONAL_BUTTONS,
            function (DefineHtmlEvent $event) {
                /** @see Asset */
                $event->html = Html::beginTag('div', ['class' => 'btngroup']);
                $event->html .= Html::button(Craft::t('alt', 'Generate alt text'), [
                    'id' => 'generateAltText-btn',
                    'class' => 'btn',
                    'data' => [
                        'icon' => 'wand',
                    ],
                    'aria' => [
                        'label' => Craft::t('alt', 'Generate alt text'),
                    ],
                ]);
                $js = <<<JS
                $('#generateAltText-btn').on('click', () => {
                        let id = document.querySelector("input[name='elementId']").value;
                        const \$form = Craft.createForm().appendTo(Garnish.\$bod);
                        \$form.append(Craft.getCsrfInput());
                        $('<input/>', {type: 'hidden', name: 'action', value: 'alt/generate-alt-text'}).appendTo(\$form);
                        $('<input/>', {type: 'hidden', name: 'assetId', value: id}).appendTo(\$form);
                        $('<input/>', {type: 'submit', value: 'Submit'}).appendTo(\$form);
                        \$form.submit();
                        \$form.remove();
                    });
                JS;
                Craft::$app->getView()->registerJs($js);

                $event->html .= Html::endTag('div');
            }
        );
    }
}
