<?php

class CommonAdminController extends MAdminController
{
    public $adminLayout = 'views/layouts/admin.twig';

    public function init()
    {
        parent::init();

        /** @var $app CWebApplication */
        $app = Yii::app();

        $fancyboxUrl = $app->assetManager->publish(Yii::getPathOfAlias('lib') . '/fancybox');

        $app->clientScript->registerScriptFile($fancyboxUrl . '/jquery.fancybox-1.3.4.pack.js');
        $app->clientScript->registerCssFile($fancyboxUrl . '/jquery.fancybox-1.3.4.css');
        $app->clientScript->registerScript(
            'fancyBoxEnable',
            '$(".fancybox").fancybox();
            $(".fancyboxIframe").fancybox({
                type: "iframe",
                width: "90%",
                height: "100%"
            });
            $(".fancyboxVideo").fancybox({
                type: "iframe"
            });'
        );

        $app->clientScript->registerScript('pseudo-link.toggle' , "
        $(document).on('click', '.toggle.pseudo-link', function() {
            $(this).parent().next().slideToggle();

            return false;
        })");
    }

    public function actionList()
    {
        $tooltipSelector = Yii::app()->bootstrap->tooltipSelector;
        $this->additionalViewVariables['gridOptions']['afterAjaxUpdate'] =
            "js:function() {
                var multiplySelects = jQuery('select[multiple]');
                if ('select2' in multiplySelects)
                    multiplySelects.select2();
                jQuery('$tooltipSelector').tooltip();
                jQuery('.fancybox').fancybox();
                jQuery('.fancyboxIframe').fancybox({
                    type: 'iframe',
                    width: '90%',
                    height: '100%'
                });
                jQuery('.fancyboxVideo').fancybox({
                    type: 'iframe'
                });
            }";
        parent::actionList();
    }

} 