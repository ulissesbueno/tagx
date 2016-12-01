<?php
class tagx{

	var $tree;
	var $ignore_tag = array('br','input','hr');

	function tagx( $html ){

		// limpar espaços duplos
		$html = preg_replace('/\s+/', ' ', $html);
		// limpa espaço entre tags
		$html = preg_replace('/>\s+</', '><', $html);
		// limpa final das tags 
		$html = preg_replace('/\s>/', '>', $html);
		// limpa inicio das tags 
		$html = preg_replace('/<\s/', '<', $html);
		// limpa espaço inicial
		$html = preg_replace('/^\s+/', '', $html);
		// ajusta tags
		//$html = preg_replace('/<('.implode('|', $this->ignore_tag ).')([^<>].*?)*>/i', "<$1$2/>", $html);
		// limpa tag problematicas
		$html = $this->hide_ignore_tag( $html );
		$html = trim($html);
		
		//echo htmlentities($html)."<Br><br>";

		// captura de tags
		$this->tree = $this->tag_tree( $html );
	}

	var $output = '';

	function setValueChave( $str, $data ){

		$GLOBALS["__data__"] = $data;
		$ret = '';

		if( is_array($data) ){
			$ret = 	preg_replace_callback('/{(\w+)}/',
	    			create_function(
			        	'$matches',
			        	'return @$GLOBALS["__data__"][$matches[1]];'
			    	),$str);

		}else{
			$ret = 	preg_replace_callback('/{(\w+)}/',
	    			create_function(
			        	'$matches',
			        	'return @$GLOBALS["__data__"];'
			    	),$str);

		}
		
		$GLOBALS["__data__"] = '';
		return $ret;
		
	}

	function is_breaacrumb( $bc, $data, $tag ){
		$chaves = preg_replace( '/(^|>)\s*([0-9]+|[\w]+)\s*/i' , '[$2]' , $bc);
		eval( " return \$data". $chaves .";" );
	}

	function write( $data = NULL, $ar = NULL, $breadcrumb = '', $open_parent = '', $parent = ''){

		if(!$ar) $ar = $this->tree;
		//$this->output = '';
		foreach( $ar as $index => $tg ){

			$tag = $tg['tag'];
			$inner = $tg['inner'];
			$open = $tg['open'];

			// Tem filho ?
			if( $tg['child'] ){		

				if( array_key_exists( $tag , $data) ){
					$breadcrumb = $data[$tag];
					//echo "<pre>". htmlentities( print_r( $tg['child'], 1 ) )."</pre>";
				}

				/* Abre a tag do pai */
				if(!$breadcrumb) $this->output .= $tg['open']; // OPEN					
				$this->write( $data , $tg['child'] , $breadcrumb, $open , $tag);
				if(!$breadcrumb) $this->output .= "</".$tag.">"; // CLOSE

			} else {

				$tmp = $inner;

				if( $breadcrumb ) {
					//echo $tag."<Br>";
					//echo "<pre>". htmlentities( print_r( $breadcrumb, 1 ) )."</pre>";
					//echo $breadcrumb." : ".htmlentities($inner)."<br>";
					$lines = '';
					foreach( $breadcrumb as $v ){
						$tmp = $open_parent.$inner."</".$parent.">";
						foreach( $v as $a => $b ){
							if( is_string( $b ) ){
								$tmp = $this->setValueChave( $tmp , $b );	
							}
						}

						$lines[] = $tmp;
						
					}
					if( is_array($lines) ) $tmp = implode('',$lines);
				} else {
					// simples...
					/* Quando o array tem index diretos . EX: array( index => valor, ... ) */
					$tmp = $this->setValueChave( $inner, $data );	
				}
				

				$this->output .= $tmp;
				
			}
		}

		$this->output = $this->reverse_hide_ignore_tag( $this->output );

	}
	var $tag_hide ;
	function hide_ignore_tag( $html ){
		foreach( $this->ignore_tag as $i => $tgi ){
			$html = preg_replace('/<('.$tgi.')(.*?)>/i', "[".($i+1)."$2]", $html);
		}
		return $html;
	}

	function reverse_hide_ignore_tag( $html ){
		foreach( $this->ignore_tag as $i => $tgi ){
			$html = preg_replace('/\['.($i+1).'(.*?)\]/i', "<".$tgi."$2>", $html);
		}
		return $html;
	}


	function echo_ar(){
		echo "<pre>". htmlentities( print_r($this->tree,1) )."</pre>";
	}
	function open_close( $str, $tag ){
		return (substr_count( $str, "<".$tag ) == substr_count( $str, "</".$tag.">" ));
	}

	var $no_tag_count = 1;

	var $no_tag_memory = array();

	function tag_tree( $html ){

		$part = "";
		$main_tag = "";
		$index = 0;
		$_ar_ = array();
		$no_tag_n = '';

		while( $html ){
			$partner = '/(<[\/]*\s*(\w+)\s*[^<>]*?>)|(.[^<]*)/i';
			preg_match($partner, $html, $match );
			$tag = trim($match[2]);
			if( !$main_tag ) $main_tag = $tag;
			$before = $match[1];
			$part .= $before;
			$after = preg_replace($partner, '', $html, 1 );
			
			$no_tag = '';			
			if(	isset($match[3]) ) {
				$html = preg_replace($partner, '<no_tag_'.$this->no_tag_count.'></no_tag_'.$this->no_tag_count.'>', $html, 1 );
				$this->no_tag_memory[$this->no_tag_count] = $match[3];
				$this->no_tag_count ++;
				continue;
			}

			if( in_array( mb_strtolower($tag), $this->ignore_tag) ){	
				$tag = '';
				$no_tag = $tag;
			}
			
			if( !$no_tag ){

				if( $main_tag == $tag ){
					if( @!array_key_exists($index,$_ar_) ){

						$_ar_[$index]['n'] = 1;
						$_ar_[$index]['tag'] = $tag;
						$_ar_[$index]['open'] = $before;
						$_ar_[$index]['child'] = '';
						$_ar_[$index]['inner'] = '';

					}else{

						//fechamento de tag;
						if( $before == "</".$tag.">" ){
							$_ar_[$index]['n'] --;
							if($_ar_[$index]['n'] == 0){
								unset( $_ar_[$index]['n'] );
								$inner = preg_replace('/<'.$tag.'[^<>]*?>(.*)<\/'.$tag.'>/i', '$1', $part);
								$_ar_[$index]['child'] = $this->tag_tree( $inner );

								if(!$_ar_[$index]['child']){
									$no_tag_n = preg_replace('/<no_tag_([0-9]+)>(.*)/i', '$1', $part);
									if( $no_tag_n )	$part = @$this->no_tag_memory[$no_tag_n];
									$_ar_[$index]['inner'] = $part;
								} 

								$part = '';
								$index ++;
								$main_tag = '';
							}
						}else{
							$_ar_[$index]['n'] ++;
						}
						
					}	
				}				
			}else{
				$part .= $no_tag;
			}
			//echo htmlentities($before)."<BR><BR>";
			//echo htmlentities($after)."<BR>";	

			$html = $after;
		}

		if( count( $_ar_ ) ) return $_ar_;
	}

	function SeachArray( $needle, $haystack = NULL ){

		if(!$haystack) $haystack = $this->tree;
		//echo "<pre>". htmlentities( print_r( $haystack , 1 ) )."</pre>";
		//exit;
		$ret = '';
		foreach( $haystack as $key => $value ) {
	        if( $value['tag'] == $needle ){
	        	return $value['child'];
	        	break;
	        } else {	
	        	if( is_array($value['child'])) {
	        		$ret = $this->SeachArray($needle,$value['child']);
	        	}
	        } 
	    }
	    return $ret;
	}

	function Selector( $Selector ){
		$Selector = preg_replace('/\s/i','',$Selector);
		preg_match_all('/(^|>*)([\w]+)/i' , $Selector , $matches);
		if( count($matches) ){
			$ar = NULL;
			foreach( $matches[2] as $tag ){
				$ar = $this->SeachArray( $tag, $ar );
			}
			echo "<pre>". htmlentities( print_r( $ar ,1) )."</pre>";

		}else{	
			//
		}
	}
	
}
?>