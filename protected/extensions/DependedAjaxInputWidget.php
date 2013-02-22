<?php

/**
 * Filling $dependedElementId element with data from $getDataUrl on $masterAttributeName change.
 * $masterAttributeName value will be appended to $getDataUrl
 */
class DependedAjaxInputWidget extends CInputWidget
{
    /**
     * internal
     * @var CActiveRecord
     */
    public $model;

    /**
     * internal
     * @var string not used
     */
    public $attribute;

    /**
     * internal
     * @var TbActiveForm
     */
    public $form;

    /**
     * @var null|string master attribute. Onchange event will bind on it
     */
    public $masterAttributeName = null;

    /**
     * @var null|string html data, loaded from url will be assigned to this attribute on page
     */
    public $dependedAttributeName = null;

    /**
     * @var null|string selected $masterAttributeName value will be appended to this url
     */
    public $getDataUrl = null;

    public function run()
    {
        if (!empty($this->masterAttributeName) &&
            !empty($this->dependedAttributeName) &&
            !empty($this->getDataUrl)
        ) {
            echo '
<script type="text/javascript">
	$(document).ready(function() {
		$("#' . EHtml::resolveId($this->model, $this->masterAttributeName) . '").bind("change", function() {
			var $dependObject = $("#' . EHtml::resolveId($this->model, $this->dependedAttributeName) . '");
			$dependObject.html("");
			if (!$(this).val()) {
				$dependObject.attr("disabled", "disabled");
			} else {
				$dependObject.removeAttr("disabled");
				$.get("' . $this->getDataUrl . '"+$(this).val(), function (data) {
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
