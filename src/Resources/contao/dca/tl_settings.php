<?php

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{dynacontent_legend},inLeftNavigation,inMainNavigation,linkToDefaultContent';

$GLOBALS['TL_DCA']['tl_settings']['fields']['inLeftNavigation'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['inLeftNavigation'],
	'inputType'               => 'checkbox',
    'eval'                    => array('submitOnChange'=>false, 'tl_class'=>'w30')
    
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['inMainNavigation'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['inMainNavigation'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>false, 'tl_class'=>'w30')
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['linkToDefaultContent'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['linkToDefaultContent'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>false, 'tl_class'=>'w30')
);
?>