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
 
// fields
$GLOBALS['TL_LANG']['tl_csv_import']['import_table'] = array('Datentabelle für Import auswählen', 'Wählen Sie eine Tabelle, in welche die Daten importiert werden sollen, aus.');
$GLOBALS['TL_LANG']['tl_csv_import']['import_mode'] = array('Import-Modus', 'Entscheiden Sie, ob die Tabelle vor dem Import gelöscht werden soll oder die Daten an die bestehenden Eiträge angehängt werden sollen.');
$GLOBALS['TL_LANG']['tl_csv_import']['field_enclosure'] = array('Felder eingeschlossen von', 'Zeichen, von welchem die Felder in der csv-Datei eingeschlossen sind. Normalerweise ein doppeltes Anführungszeichen: => "');
$GLOBALS['TL_LANG']['tl_csv_import']['field_separator'] = array('Felder getrennt von', 'Zeichen, mit dem die Felder in der csv-Datei voneinander getrennt sind. Normalerweise ein Semikolon: => ;');
$GLOBALS['TL_LANG']['tl_csv_import']['selected_fields'] = array('Felder für Importvorgang auswählen.');
$GLOBALS['TL_LANG']['tl_csv_import']['fileSRC'] = array('csv-Datei auswählen');

//references
$GLOBALS['TL_LANG']['tl_csv_import']['truncate_table'] = array('Tabelle vor dem Import löschen');
$GLOBALS['TL_LANG']['tl_csv_import']['append_entries'] = array('Datensätze nur anhängen');