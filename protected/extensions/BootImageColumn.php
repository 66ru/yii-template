<?php

class BootImageColumn extends BootDataColumn
{
	public $type='raw';

	public $filter = false;

	public $htmlOptions = array('style'=>'width:120px');

	public $imageStyle = 'max-width:120px';

	public $thumbnailUrl = null;

	public function init()
	{
		if (empty($this->thumbnailUrl))
			$this->thumbnailUrl = '$data->'.$this->name;
		$this->value = '
			CHtml::link(
				CHtml::image('.$this->thumbnailUrl.',"", array("style"=>"'.$this->imageStyle.'")),
				$data->'.$this->name.',
				array("target" => "_blank")
			);';

		parent::init();
	}

}
