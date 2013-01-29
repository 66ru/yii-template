<?php
/**
 * ValidXml class file.
 * fetch file from url from attribute and check it for valid xml inside
 *
 * @author Andrew Putilov <me@aputilov.ru>
 * @link https://github.com/m8rge/yii-template
 * @license BSD
 */

class ValidXml extends CValidator
{
    public $useCache = true;

    private static $filesCache = array();

    function __construct()
    {
        Yii::app()->attachEventHandler('onEndRequest', array('ValidXml', 'removeTemporaryFiles'));
    }

    public static function removeTemporaryFiles()
    {
        foreach (self::$filesCache as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Validates a single attribute.
     * This method should be overridden by child classes.
     * @param CModel $object the data object being validated
     * @param string $attribute the name of the attribute to be validated.
     */
    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;

        $xml = false;
        if ($this->useCache && !empty(self::$filesCache[$value])) {
            $xmlFile = self::$filesCache[$value];
        } else {
            $xmlFile = tempnam(Yii::app()->getRuntimePath(), 'xml');
            try {
                CurlHelper::downloadToFile($value, $xmlFile);
                self::$filesCache[$value] = $xmlFile;
            } catch (Exception $e) {
            }
        }

        if (file_exists($xmlFile)) {
            $xml = @simplexml_load_file($xmlFile);
        }

        if ($xml === false) {
            $message = $this->message !== null ? $this->message : Yii::t('ValidXml.app', '{attribute} doesn\'t contain valid XML.');
            $this->addError($object, $attribute, $message);
        }
    }

    public function clientValidateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        $message = $this->message !== null ? $this->message : Yii::t('ValidXml.app', '{attribute} doesn\'t contain valid XML.');
        $message = json_encode($message);
        $jsxmlvali = "
/*
 * @Author Denis Khripkov | denisx@ya.ru | www.denisx.ru
 */
var oXmlValidator = {
	oTab: new RegExp(/[\\n\\t\\r]+/g),
	oCommentAndCdata: new RegExp(/<!(?:--(?:[^-]|-[^-])*--|\\[CDATA\\[(?:[^\\]]|\\][^\\]]|\\]+[^\\>\\]])*]{2,})>/g),
	oInstruction: new RegExp(/<\\?.*?\\?>/),
	oDocType: new RegExp(/<\\!DocType.*?>/i),
	oOutTagTextBegin: new RegExp(/^\\s*[^<\\s]+/),
	oEntityFull: new RegExp(/&(?:#(?:x[a-f\\d]{1,4}|\\d{2,5})|[a-z][\\w\\-]*);/gi),
	oAttribute: new RegExp(/(<[a-z_][\\w:-]*)((?:\\s+[a-z_][\\w:-]*\\s*=\\s*(?:'[^<>']*'|\"[^<>\"]*\"))*)\\s*(\\/?>)/gi),
	oAttributeUnique: new RegExp(/([a-z_][\\w:-]*)\\s*=\\s*(?:'[^<>']*'|\"[^<>\"]*\")/gi),
	oAttributeMatch: new RegExp(/[a-z_][\\w:-]*/gi),
	oSingleTag: new RegExp(/<[a-z_][\\w:-]*\\/>/gi),
	oDoubleTag: new RegExp(/<([a-zA-Z_][\\w:-]*)>[^<]*<\\/\\1\\s*>/g)
};
oXmlValidator.Object = function(sValue){
	this.sValue = sValue;
	this.nCode = 0;
	this.nBugPlace = 0;
	this.hParams = {
		bFragment: false // true - проверяемый код не целый xml, а только его часть
	}
};
oXmlValidator.Object.prototype = {
	valid: function(){
		var sValue = this.sValue;
		var hParams = this.hParams;
		if ( sValue ){
			// вырезаем табуляцию и переносы строк
			sValue = sValue.replace( oXmlValidator.oTab, ' ' );
			// вырезаем комменты и CDATA
			sValue = sValue.replace( oXmlValidator.oCommentAndCdata, '' );
			if ( sValue.indexOf( '<!--' ) != -1 ) {
				this.nCode = 2; return false;
			}
			if ( sValue.indexOf( ']]>' ) != -1 ) {
				this.nCode = 3; return false;
			}
			// вырезаем инструкции
			if ( !hParams.bFragment )
				sValue = sValue.replace( oXmlValidator.oInstruction, '' );
				if ( sValue.search( oXmlValidator.oInstruction ) != -1 ) {
					this.nCode = 4; return false;
				}
			// вырезаем DocType
			if ( !hParams.bFragment )
				sValue = sValue.replace( oXmlValidator.oDocType, '' );
			if ( sValue.search( oXmlValidator.oDocType ) != -1 ) {
				this.nCode = 5; return false;
			}
			// ищем текст в начале и в конце строки, выходящий за пределы тегов
			if ( !hParams.bFragment ) {
				if ( sValue.search( oXmlValidator.oOutTagTextBegin ) != -1 ) {
					this.nCode = 6; return false;
				}
				// конец строки.
				var nValueLength = sValue.length;
				var bIsSpace = true;
				do {
					nValueLength--;
					if ( sValue.charAt( nValueLength ) != ' ' ) bIsSpace = false;
				} while ( bIsSpace && nValueLength > 0 )
				if ( !bIsSpace && sValue.charAt( nValueLength ) != '>' ){
					this.nCode = 7; return false;
				}
				else if ( nValueLength == 0 ){
					this.nCode = 1; return false;
				}
			}
			// вырезаем Entities
			sValue = sValue.replace( oXmlValidator.oEntityFull, '' );
			if ( sValue.indexOf( '&' ) != -1 ){
				this.nCode = 8; return false;
			}
			// вырезаем аттрибуты и проверяем на дублирование
			var bAttributeUnique = true;
			sValue = sValue.replace( oXmlValidator.oAttribute,
				function a($0, $1, $2 ,$3){
					$2 = $2.replace( oXmlValidator.oAttributeUnique, '$1' );
					var aAttribute = $2.match( oXmlValidator.oAttributeMatch );
					if ( aAttribute ){
						var nMatchCount = aAttribute.length;
						if ( nMatchCount > 1 ){
							var i = 0; var j;
							while ( bAttributeUnique && i < nMatchCount-1 ){
								j = i + 1;
								while ( bAttributeUnique && j < nMatchCount ){
									if ( aAttribute[i] != aAttribute[j] ){
										j++;
									}else{
										bAttributeUnique = false;
									}
								}
								i++;
							}
						}
					}
					return $1 + $3;
				});
			if ( !bAttributeUnique ){
				this.nCode = 11; return false;
			}
			// параметр для вырезания тэгов
			var sTagReplaceTo = '';
			if ( !hParams.bFragment ) sTagReplaceTo = '&';
			// вырезаем одинарные тэги
			sValue = sValue.replace( oXmlValidator.oSingleTag, sTagReplaceTo );
			// вырезаем двойные тэги
			var nPrevLen; var nLen = 0;
			do {
				nPrevLen = nLen;
				sValue = sValue.replace( oXmlValidator.oDoubleTag, sTagReplaceTo );
				nLen = sValue.length;
			} while ( nLen != nPrevLen );
			if ( !hParams.bFragment ) {
				if ( sValue.indexOf(sTagReplaceTo) != sValue.lastIndexOf(sTagReplaceTo) ) {
					this.nCode = 9; return false;
				}
			}
			if( sValue.indexOf( '<' ) != -1 ){
				this.nCode = 10; return false;
			}
			this.nCode = 0; return true;
		} else { // пустая строка
			if ( !hParams.bFragment ){
				this.nCode = 1; return false;
			}
			else {
				this.nCode = 0; return true;
			}
		}
	}
};
";
        $script = "
$.get('$value', function(data){
	var my_oXmlValidator = new oXmlValidator.Object(data);
	if (!my_oXmlValidator.valid())
		messages.push($message);
});
";

        return $jsxmlvali . $script;
    }

}
