<?php

/**
 * Image column for TbGridView
 *
 * @see AdminController::getTableColumns
 */
class TbImageColumn extends TbDataColumn
{
    public $type = 'raw';

    public $filter = false;

    public $htmlOptions = array('style' => 'width:120px');

    /**
     * @var string additional image css
     */
    public $imageStyle = 'max-width:120px';

    /**
     * @var null|string URL to thumbnail image. If empty, used fullsize image
     */
    public $thumbnailUrl = null;

    public function init()
    {
        if (empty($this->thumbnailUrl)) {
            $this->thumbnailUrl = '$data->' . $this->name;
        }

        $this->value = '
			CHtml::link(
				CHtml::image(' . $this->thumbnailUrl . ',"", array("style"=>"' . addslashes($this->imageStyle) . '")),
				$data->' . $this->name . ',
				array("target" => "_blank")
			);';

        parent::init();
    }
}
