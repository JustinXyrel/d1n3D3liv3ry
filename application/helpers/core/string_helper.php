<?php
	function append_chars($string,$position = "right",$count = 0, $char = "")
	{
	    $rep_count = $count - strlen($string);
	    $append_string = "";
	    for ($i=0; $i < $rep_count ; $i++) {
	        $append_string .= $char;
	    }
	    if ($position == 'right')
	        return $string.$append_string;
	    else
	        return $append_string.$string;
	}
	function align_center($string,$count,$char = " ")
	{
	    $rep_count = $count - strlen($string);
	    for ($i=0; $i < $rep_count; $i++) {
	        if ($i % 2 == 0) {
	            $string = $char.$string;
	        } else {
	            $string = $string.$char;
	        }
	    }
	    return $string;
	}