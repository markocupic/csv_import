<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Csv_import
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'ImportTo_tl_member' => 'system/modules/csv_import/classes/ImportTo_tl_member.php',
	'ImportTo'           => 'system/modules/csv_import/classes/ImportTo.php',
));
