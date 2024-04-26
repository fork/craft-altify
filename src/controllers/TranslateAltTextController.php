<?php

namespace fork\altify\controllers;

use Craft;
use craft\errors\MissingComponentException;
use craft\web\Controller;
use fork\altify\Plugin;
use Throwable;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Translation Controller
 */
class TranslateAltTextController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_NEVER;

    /**
     * altify/generate-alt-text action
     * @return Response
     * @throws BadRequestHttpException
     * @throws MissingComponentException
     */
    public function actionIndex(): Response
    {
        $this->requireLogin();
        $this->requireCpRequest();
        $assetId = Craft::$app->request->getBodyParam('assetId');

        try {
            Plugin::getInstance()->translator->translateAltTextForImage($assetId);
        } catch (Exception|Throwable $e) {
            Craft::$app->getSession()->setError($e->getMessage());
        }

        return $this->asSuccess();
    }
}
