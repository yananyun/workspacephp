<?php
/**
 * xml处理功能类及函数
 */
class Xml extends DOMDocument {
	protected $root;
	function __construct($root = 'root') {
		parent::__construct ( '1.0', 'utf-8' );
		$this->formatOutput = true;
		$this->root = $this->appendChild ( $this->createElement ( $root ) );
	}
	/**
	 * 生成相应节点
	 * 
	 * @param type $arr        	
	 * @param type $node        	
	 */
	function createNode($arr, $node = null) {
		if (is_null ( $node )) {
			$node = $this->root;
		}
		foreach ( $arr as $key => $val ) {
			$key = is_numeric ( $key ) ? 'node' : $key;
			
			$child = $this->createElement ( $key );
			$node->appendChild ( $child );
			
			if (is_array ( $val )) {
				$this->createNode ( $val, $child );
			} else {
				$child->appendChild ( $this->createCDATASection ( $val ) );
			}
		}
	}
	
	/**
	 * xml解码
	 *
	 * @param unknown_type $xml        	
	 * @return unknown
	 */
	public static function decode($xml) {
		$values = array ();
		$index = array ();
		$array = array ();
		$parser = xml_parser_create ( 'utf-8' );
		xml_parser_set_option ( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parser_set_option ( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parse_into_struct ( $parser, $xml, $values, $index );
		xml_parser_free ( $parser );
		$i = 0;
		$name = $values [$i] ['tag'];
		$array [$name] = isset ( $values [$i] ['attributes'] ) ? $values [$i] ['attributes'] : '';
		$array [$name] = self::_struct_to_array ( $values, $i );
		return $array;
	}
	private static function _struct_to_array($values, &$i) {
		$child = array ();
		if (isset ( $values [$i] ['value'] ))
			array_push ( $child, $values [$i] ['value'] );
		
		while ( $i ++ < count ( $values ) ) {
			switch ($values [$i] ['type']) {
				case 'cdata' :
					array_push ( $child, $values [$i] ['value'] );
					break;
				
				case 'complete' :
					$name = $values [$i] ['tag'];
					if (! empty ( $name )) {
						$child [$name] = ($values [$i] ['value']) ? ($values [$i] ['value']) : '';
						if (isset ( $values [$i] ['attributes'] )) {
							$child [$name] = $values [$i] ['attributes'];
						}
					}
					break;
				
				case 'open' :
					$name = $values [$i] ['tag'];
					$size = isset ( $child [$name] ) ? sizeof ( $child [$name] ) : 0;
					$child [$name] [$size] = self::_struct_to_array ( $values, $i );
					break;
				
				case 'close' :
					return $child;
					break;
			}
		}
		return $child;
	}
}

?>
