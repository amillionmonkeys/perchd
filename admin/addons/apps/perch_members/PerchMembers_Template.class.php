<?php

class PerchMembers_Template extends PerchAPI_TemplateHandler
{
	private $Session = false;

	public $tag_mask = 'member|else:member';

	public function render_runtime($contents, $Template)
	{
		if (strpos($contents, 'perch:member')!==false) {
            
            //PerchUtil::debug(PerchUtil::html($contents));

			$Session = PerchMembers_Session::fetch();
			$this->Session = $Session;

		
			// CONTENT
	    	$contents 	= $Template->replace_content_tags('member', $Session->to_array(), $contents);


	    	// Clean up
	    	$s 			= '/<perch:member\s[^>]*\/>/';
			$contents	= preg_replace($s, '', $contents);


			// CONDITIONALS
			$content_vars   = array('foo'=>'bar');
			$index_in_group = 0;
			$contents       = $this->parse_paired_tags('member', false, $contents, $content_vars, $index_in_group, 'parse_conditional');

        }

		return $contents;
	}

	protected function parse_conditional($type, $opening_tag, $condition_contents, $exact_match, $template_contents, $content_vars, $index_in_group=false)
	{
		$Tag = new PerchXMLTag($opening_tag);

		//PerchUtil::debug($Tag);
		//PerchUtil::debug(PerchUtil::html($condition_contents));

		$type = false;

		if ($Tag->logged_in() || $Tag->logged_out()) {
			$type = 'auth';
		}elseif ($Tag->has_tag()) {
			$type = 'tag';
		}else{
			$type = 'data';
		}

		//PerchUtil::debug('Condition: '.$type, 'error');
		
		$Session = $this->Session;

		$positive = $condition_contents;
        $negative = '';
        	        
        // else condition
        if (strpos($condition_contents, 'perch:else:member')>0) {
	        $parts   = preg_split('/<perch:else:member\s*\/>/', $condition_contents);
            if (is_array($parts) && count($parts)>1) {
                $positive = $parts[0];
                $negative = $parts[1];
            }
        }    
		
		switch($type) 
		{

			case 'auth':
        
            	if (($Session->logged_in && $Tag->logged_in()) || (!$Session->logged_in && $Tag->logged_out())) {
            		$template_contents  = str_replace($exact_match, $positive, $template_contents);
            	}else{
            		$template_contents  = str_replace($exact_match, $negative, $template_contents);
            	}

				break;


			case 'tag':
				if ($Session->has_tag($Tag->has_tag())) {
					$template_contents  = str_replace($exact_match, $positive, $template_contents);
            	}else{
            		$template_contents  = str_replace($exact_match, $negative, $template_contents);
				}

				break;


			default:
				if (strpos($condition_contents, 'perch:else:member')>0) {
					$condition_contents = preg_replace('/<perch:else:member\s*\/>/', '', $condition_contents);
				}
				$template_contents  = str_replace($exact_match, $condition_contents, $template_contents);
				
				break;

		}
	    
	    return $template_contents;
	}

    private function parse_paired_tags($type, $empty_opener=false, $contents, $content_vars, $index_in_group=false, $callback='parse_conditional')
    {
		$close_tag     = '</perch:'.$type.'>';
		$close_tag_len = mb_strlen($close_tag);
		$open_tag      = '<perch:'.$type.($empty_opener ? '' : ' ');

		// escape hatch
		$i = 0;
		$max_loops = 100;

		// loop through while we have closing tags
    	while($close_pos = mb_strpos($contents, $close_tag)) {

    		// we always have to go from the start, as the string length changes,
    		// but stop at the closing tag
    		$chunk = mb_substr($contents, 0, $close_pos);

    		// search from the back of the chunk for the opening tag
    		$open_pos = mb_strrpos($chunk, $open_tag);

    		// get the pair html chunk
    		$len = ($close_pos+$close_tag_len)-$open_pos;
    		$pair_html = mb_substr($contents, $open_pos, $len);

    		// find the opening tag - it's right at the start
    		$opening_tag_end_pos = mb_strpos($pair_html, '>')+1;
    		$opening_tag = mb_substr($pair_html, 0, $opening_tag_end_pos);

    		// condition contents
    		$condition_contents = mb_substr($pair_html, $opening_tag_end_pos, 0-$close_tag_len);

    		// Do the business
    		$contents = call_user_func(array($this, $callback), $type, $opening_tag, $condition_contents, $pair_html, $contents, $content_vars, $index_in_group);

    		// escape hatch counter
    		$i++;
    		if ($i > $max_loops) return $contents;
    	}

    	return $contents;
    }

}

