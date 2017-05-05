<?php 
/**
 * A simple parser that allows you to Convert  wordpress shortcode  to multidimentional array.
 * * The Shortcode API (new in 2.4) is a simple regex based parser that allows you to replace simple wordpress shortcode like tags within a HTMLText or HTMLVarchar field when rendered into a template. It is inspired by and very similar to the [Wordpress implementation](http://codex.wordpress.org/Shortcode_API) of shortcodes. Examples of shortcode tags are:
 * 
 * <code>
 *     [shortcode]
 *     [shortcode /]
 *     [shortcode parameter="value"]
 *     [shortcode parameter="value"]Enclosed Content[/shortcode]
 * </code>
 * Example : Note : define your Prefix (like tabs || tab_item ) in pattren. (ref. cs_get_pattern())
 *  $text = '[tabs animation="fadeIn" size="1/2"]
 *  [tab_item color="#CCCCCC" icon="fa-user"]Tab 1 contents[/tab_item]
 *  [tab_item color="#CCCCCC" icon="fa-login"]Tab 2 contents[/tab_item]
 *  [/tabs]';
 *	$Output = array();
 *  $PREFIX = 'prefix'; //user prefix as cs_message OR cs_tabs | tab_item
 *	$parseInstance 	= new ShortcodeParse();
 *	$output = $b->cs_shortcodes( $output, $text );
 *	echo '<pre>';
 *	var_dump( array_values( $output ) );
 *  echo '</pre>';
 */
if ( !class_exists('ShortcodeParse') ) {
class ShortcodeParse
	{
		
		function __construct()
		{
			# code...
		}

		function cs_get_pattern( $content , $PREFIX ) {
			    $pattern = '\[(\[?)(' . $PREFIX . ')(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)';
			    preg_match_all( "/$pattern/s", $content, $matches);
			    return $matches;
			}

			function cs_parse_atts( $content ) {
			    //$content = preg_match_all( '/([^ ]*)=(\'([^\']*)\'|\"([^\"]*)\"|([^ ]*))/', trim( $content ), $c );
				//$content = preg_match_all( '/([^ ]*)=(\'([^\']*)\'|\""([^\""]*)\"|([^ ]*))/', trim( $content ), $c );
				//$content = preg_match_all( '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/', trim( $content ), $c );
				$content = preg_match_all( '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/', trim( $content ), $c );
				//$content = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
			    list( $dummy, $keys, $values ) = array_values( $c );
			    $c = array();
			    foreach ( $keys as $key => $value ) {
			        $value = trim( $values[ $key ], "\"'"  );
			        /*$type = is_numeric( $value ) ? 'int' : 'string';
			        $type = in_array( strtolower( $value ), array( 'true', 'false' ) ) ? 'bool' : $type;
			        switch ( $type ) {
			            case 'int': $value = (int) $value; break;
			            case 'bool': $value = strtolower( $value ) == 'true'; break;
			        }*/ 
				   $c[ $keys[ $key ] ] = $value;
			    }
			    return $c;
			}

			function cs_shortcodes( &$output, $content, $child = true , $PREFIX) {
				print_r($content);
			    $patts = $this->cs_get_pattern( $content , $PREFIX );
			    $t = array_filter( $this->cs_get_pattern( $content , $PREFIX) );
			    if ( ! empty( $t ) ) {
			        list( $d, $d, $parents, $atts, $d, $contents ) = $patts;
			        $outputNew = array();
			        $n = 0;
			        foreach( $parents as $k=>$parent ) {
			            ++$n;
			            $name = $child ? 'child' . $n : $n;
			            $t = array_filter( $this->cs_get_pattern( $contents[ $k ] , $PREFIX ) );
			            $t_s = $this->cs_shortcodes( $outputNew, $contents[ $k ], true , $PREFIX);
			            $output[ $name ] = array( 'name' => $parents[ $k ] );
			            $output[ $name ]['atts'] = $this->cs_parse_atts( $atts[ $k ] );
			            $output[ $name ]['original_content'] = $contents[ $k ];
			            $output[ $name ]['content'] = ! empty( $t ) && ! empty( $t_s ) ? $t_s : $contents[ $k ];
			        }
			    }
			    return array_values( $output );
			}
	}
}
			

	