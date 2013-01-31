<?php

function smarty_modifier_singlequote_escape($string)
{       
    $string = str_replace('&','&amp;', $string);
	return str_replace("'","&#039;", $string);
}

?>