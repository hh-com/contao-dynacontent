<?php



array_insert($GLOBALS['BE_MOD']['ContaoDynaContent'], 0, array
(
	'dynacontent' => array
	(
		'callback'	=> '\Hhcom\ContaoDynaContent\DynaContent'
	)
));

if($GLOBALS['TL_CONFIG']['inLeftNavigation'])
	$GLOBALS['TL_HOOKS']['getUserNavigation'][] = array('\Hhcom\ContaoDynaContent\DynaContent', 'generateNavigation');

/* remove css class to prevent from js actions */
if (\Input::get('dynacontent'))
	$GLOBALS['TL_HOOKS']['outputBackendTemplate'][] = array('\Hhcom\ContaoDynaContent\DynaHelper', 'customizeBeMain');

// Style sheet
if (defined('TL_MODE') && TL_MODE == 'BE')
{
	$GLOBALS['TL_CSS'][] = 'bundles/contaodynacontent/css/backend.scss|static';
	$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaodynacontent/js/iFrameResizer.js';

	if(\Input::get('do') == 'dynacontent') {
		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaodynacontent/js/dybackend.js';
	} else {	
		$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaodynacontent/js/iframeResizer.contentWindow.min.js';
	}
	
	$GLOBALS['TL_JAVASCRIPT'][] = 'bundles/contaodynacontent/js/backend.js';
}

/* redirect to do=dynacontent after create, copy, delete or dragdrop */
$GLOBALS['TL_HOOKS']['outputBackendTemplate'][] = array('\Hhcom\ContaoDynaContent\DynaHelper', 'redirectToDynaContent');

?>