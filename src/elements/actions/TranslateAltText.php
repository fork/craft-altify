<?php

namespace fork\altify\elements\actions;

use Craft;
use craft\base\ElementAction;
use craft\errors\MissingComponentException;
use fork\altify\Plugin;
use Throwable;
use yii\base\Exception;

/**
 * Translate Alt Text element action
 */
class TranslateAltText extends ElementAction
{
    public static function displayName(): string
    {
        return Craft::t('altify', 'Translate alt text');
    }

    public function getTriggerHtml(): ?string
    {
        Craft::$app->getView()->registerJsWithVars(fn($type) => <<<JS
            (() => {
                new Craft.ElementActionTrigger({
                    type: $type,

                    // Whether this action should be available when multiple elements are selected
                    bulk: true,

                    // Return whether the action should be available depending on which elements are selected
                    validateSelection: (selectedItems) {
                      return true;
                    },

                    // Uncomment if the action should be handled by JavaScript:
                    // activate: () => {
                    //   Craft.elementIndex.setIndexBusy();
                    //   const ids = Craft.elementIndex.getSelectedElementIds();
                    //   // ...
                    //   Craft.elementIndex.setIndexAvailable();
                    // },
                });
            })();
        JS, [static::class]);

        return null;
    }

    /**
     * @param Craft\elements\db\ElementQueryInterface $query
     * @return bool
     * @throws MissingComponentException
     */
    public function performAction(Craft\elements\db\ElementQueryInterface $query): bool
    {
        $elements = $query->all();
        foreach ($elements as $element) {
            try {
                Plugin::getInstance()->translator->translateAltTextForImage($element->id);
            } catch (Exception|Throwable $e) {
                Craft::$app->getSession()->setError($e->getMessage());
            }
        }

        return true;
    }
}
