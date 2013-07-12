<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package Import_from_csv
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 * @author Marko Cupic <m.cupic@gmx.ch>
 */


// fields
$GLOBALS['TL_LANG']['tl_csv_import']['import_table'] = array('Import data into this table', 'Choose a table for import.');
$GLOBALS['TL_LANG']['tl_csv_import']['import_mode'] = array('Import mode', 'Decide if the table will be truncated before importing the data from the csv-file.');
$GLOBALS['TL_LANG']['tl_csv_import']['field_enclosure'] = array('Field enclosure', 'Character with which  the field-content is enclosed. Normally it is a double quote: => "');
$GLOBALS['TL_LANG']['tl_csv_import']['field_separator'] = array('Field separator', 'Character with which the fields are separated. Normally it is a semicolon: => ;');
$GLOBALS['TL_LANG']['tl_csv_import']['selected_fields'] = array('Select the fields for the import');
$GLOBALS['TL_LANG']['tl_csv_import']['fileSRC'] = array('Select a csv-file for the import');

// references
$GLOBALS['TL_LANG']['tl_csv_import']['truncate_table'] = array('truncate the target table before importing data');
$GLOBALS['TL_LANG']['tl_csv_import']['append_entries'] = array('only append data into the target table');

// messages
$GLOBALS['TL_LANG']['tl_csv_import']['manual'] = 'Watch the manual (screenshot)';
$GLOBALS['TL_LANG']['tl_csv_import']['error_annunciation'] = 'There was at least one error during the import process. Please check the error messages:';
$GLOBALS['TL_LANG']['tl_csv_import']['success_annunciation'] = '%s records have been successfully inserted into %s.';

