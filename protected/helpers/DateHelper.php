<?php

class DateHelper 
{
    const dateRangeRegEx = '/(\d{1,2}\.\d{1,2}\.\d{4})\s?-\s?(\d{1,2}\.\d{1,2}\.\d{4})/';

    /**
     * @param string $inputTime
     * @param string $inputFormat
     * @param string $outputFormat
     * @return bool|string
     */
    public static function getMysqlDate($inputTime, $inputFormat = 'dd.MM.yyyy', $outputFormat = 'Y-m-d')
    {
        return date($outputFormat, CDateTimeParser::parse($inputTime, $inputFormat));
    }

    /**
     * @param string $inputTime
     * @param string $inputFormat
     * @param string $outputFormat
     * @return bool|string
     */
    public static function getMysqlDateTime($inputTime, $inputFormat = 'dd.MM.yyyy, H:mm:ss', $outputFormat = 'Y-m-d H:i:s')
    {
        return self::getMysqlDate($inputTime, $inputFormat, $outputFormat);
    }

    /**
     * @param string $dateRange
     * @param string $fieldName
     * @param CDbCriteria $criteria
     */
    public static function applyDateRangeFilter($dateRange, $fieldName, &$criteria)
    {
        if (preg_match(DateHelper::dateRangeRegEx, $dateRange, $matches)) {
            $criteria->addCondition("$fieldName >= :startDate AND $fieldName < DATE_ADD(:endDate, INTERVAL 1 day)");
            $criteria->params[':startDate'] = DateHelper::getMysqlDate($matches[1]);
            $criteria->params[':endDate'] = DateHelper::getMysqlDate($matches[2]);
        }
    }


    /**
     * @param CModel $model
     * @param string $attributeName
     * @return array array(stringHtml, stringJs)
     */
    public static function getDateFilter($model, $attributeName)
    {
        $widgetHtml = Yii::app()->controller->widget(
            'bootstrap.widgets.TbDateRangePicker',
            array(
                'model' => $model,
                'attribute' => $attributeName,
                'options' => array(
                    'locale' => array(
                        'applyLabel' => 'Фильтр',
                        'cancelLabel' => 'Отмена',
                        'fromLabel' => 'От',
                        'toLabel' => 'До',
                        'firstDay' => 1,
                        'daysOfWeek' => Yii::app()->locale->getWeekDayNames('abbreviated', true),
                    ),
                    'format' => 'DD.MM.YYYY',
                    'opens'=> 'left',
                )
            ),
            true
        );
        $clientScript = Yii::app()->clientScript;
        $widgetJs = end($clientScript->scripts[$clientScript->defaultScriptPosition]);
        return array($widgetHtml, $widgetJs);
    }

    /**
     * @param string $birthday
     * @return int
     */
    public static function getAge($birthday)
    {
        $now = new DateTime();
        $diff = $now->diff( new DateTime($birthday) );
        return $diff->y;
    }

    /**
     * @param int $year
     * @return string
     */
    public static function pluralYear($year)
    {
        $year = abs( $year % 100 );
        if (11 > $year || $year > 19) {
            switch( $year % 10 ) {
                case 1:                 return 'год';
                case 2: case 3: case 4: return 'года';
            }
        }
        return 'лет';
    }
}