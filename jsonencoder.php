<?php
require_once('typo3conf/ext/wildsidemvc/JSON.php');
class jsonencoder {
		private $json;
		
		public function __construct() {
			
		}
		
		/**
		 * Method for encoding a string to be used in a json response. This is required for each textelement in the array, which 
		 * is to be encoded, if these textelements contain any characters not in standard ascii table.  
		 * @return 
		 * @param $str String object
		 */
		public function encodeForJson($str) {
			return $this->convertUnicodeToEntities(utf8_encode($str));
		}
		
		/**
		 * Returns a json-encoded string. 
		 * @return string	json encoded string. 
		 * @param $input Object 
		 */
		public function encodeJson($input) {
                      $json = new Services_JSON();
                      $rval = $json->encode($input);
                      return $rval;
		}
		
		/**
		 * Parses a datarow from a database call, and substitutes all string fields as entities with ascii preserved. 
		 * @return 
		 * @param $datarow Object		The data row object from a database call. 
		 * @param $stringFields Object	An array of fieldnames which should be parsed. 
		 */
		public function parseDataRow($datarow, $stringFields) {
			foreach($stringFields as $stringField  ) {
				$datarow[$stringField] = $this->encodeForJson($datarow[$stringField]);
			}
			return $datarow;
		}

		/**
		 * Converts utf8 string to unicode. 
		 * @return 
		 * @param $str string	A unicode string
		 */		
		private function utf8_to_unicode( $str ) {
		  $unicode = array();        
		  $values = array();
		  $lookingFor = 1;
		  for ($i = 0; $i < strlen( $str ); $i++ ) {
		    $thisValue = ord( $str[ $i ] );
		    if ( $thisValue < 128 ) $unicode[] = $thisValue;
		    else {
		      if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
		      $values[] = $thisValue;
		      if ( count( $values ) == $lookingFor ) {
			$number = ( $lookingFor == 3 ) ?
			  ( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
			  ( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );
			$unicode[] = $number;
			$values = array();
			$lookingFor = 1;
		      }
		    }
		  }
		  return $unicode;
		}
		
		/**
		 * Converts a unicode string to entities preserving any ascii characters.
		 * @return 
		 * @param $unicode string	A unicode string
		 */
		private function unicode_to_entities_preserving_ascii( $unicode ) {
		  $entities = '';
		  foreach( $unicode as $value ) {
		    $entities .= ( $value > 127 ) ? '&#' . $value . ';' : chr( $value );
		  }
		  return $entities;
		} 
		
		/**
		 * Converts a unicode string to entities. 
		 * @return 
		 * @param $unicode string	Unicode string
		 */
		private function unicode_to_entities( $unicode ) {
		  $entities = '';
		  foreach( $unicode as $value ) $entities .= '&#' . $value . ';';
		  return $entities;
		}
		
		/**
		 * Converts utf8 encoded string to a entity string with ascii characters preserved. 
		 * @return 
		 * @param $unicode_str Object
		 */
		private function convertUnicodeToEntities($unicode_str){
		  $unicode = $this->utf8_to_unicode($unicode_str);
		  return $this->unicode_to_entities_preserving_ascii($unicode);
		}
}
?>
