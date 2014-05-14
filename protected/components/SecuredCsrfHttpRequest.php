<?php

class SecuredCsrfHttpRequest extends CHttpRequest
{
    public $noCsrfValidationRoutes = array();

    protected function normalizeRequest()
    {
        //attach event handlers for CSRFin the parent
        parent::normalizeRequest();
        //remove the event handler CSRF if this is a route we want skipped
        if ($this->enableCsrfValidation && !Yii::app()->errorHandler->error) {
            $route = Yii::app()->getUrlManager()->parseUrl($this);
            foreach ($this->noCsrfValidationRoutes as $noValidateRoute) {
                if (strpos($route, $noValidateRoute) === 0) {
                    Yii::app()->detachEventHandler('onBeginRequest', array($this, 'validateCsrfToken'));
                }
            }
        }
    }

    protected function createCsrfCookie()
    {
        $cookie = parent::createCsrfCookie();
        $cookie->httpOnly = true;
        return $cookie;
    }
}