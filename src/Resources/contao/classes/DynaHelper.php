<?php

namespace Hhcom\ContaoDynaContent;

class DynaHelper extends \Controller {

	
	/**
	 * Redirect to do=dynacontent after create or copy an element
	 */
    public function redirectToDynaContent($sbuffer, $buffer)
	{
		$pageId = \Input::get('dynaback');
		if ( is_numeric ($pageId) ) 
		{
			\System::setCookie('newItemBe', \Input::get('id'), time() + 300);
			\Controller::redirect('/contao?do=dynacontent&ref='.TL_REFERER_ID.'&rt='.REQUEST_TOKEN.'&page=' . \Input::get('dynaback'));
		}
	    
	    return $sbuffer;
	}

	/**
	 * remove tl_formbody_edit to prevent from js actions 
	 */
	public function customizeBeMain( $strContent, $strTemplate )
	{
		if ($strTemplate == 'be_main')
		{
			$strContent = str_replace("tl_formbody_edit", "tl_formbody_edit_dyna", $strContent);
		}

		return $strContent;
	}
	
}

?>
