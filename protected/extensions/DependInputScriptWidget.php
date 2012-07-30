<?php

class DependInputScriptWidget extends CWidget
{
	public $model;
	public $attributeName;
	public $form;

	/**
	 * @var null|string input element ID. Onchange event will bind on it
	 */
	public $masterElementId = null;

	/**
	 * @var null|string html data, loaded from url will assigned to this element
	 */
	public $dependedElementId = null;

	/**
	 * @var null|string value from master element will append to url
	 */
	public $getDataUrl = null;

	public function run() {
		if (!empty($this->masterElementId) &&
				!empty($this->dependedElementId) &&
				!empty($this->getDataUrl)) {
			echo '
<script type="text/javascript">
	$(document).ready(function() {
		$("#'.$this->masterElementId.'").bind("change", function() {
			var $dependObject = $("#'.$this->dependedElementId.'");
			$dependObject.html("");
			if (!$(this).val()) {
				$dependObject.attr("disabled", "disabled");
			} else {
				$dependObject.removeAttr("disabled");
				$.get("'.$this->getDataUrl.'"+$(this).val(), function (data) {
					$dependObject.html(data);
				});
			}
		});
	});
</script>
';
		}
	}
}
