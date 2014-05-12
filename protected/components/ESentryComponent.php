<?php

Yii::import('vendor.m8rge.yii-sentry-log.RSentryComponent');

class ESentryComponent extends RSentryComponent
{
    public $skip = array(404, 403);

    /**
     * @param CExceptionEvent $event
     */
    public function handleException($event)
    {
        if (!($event->exception instanceof CHttpException && in_array($event->exception->statusCode, $this->skip))) {
            parent::handleException($event);
        }
    }
}
