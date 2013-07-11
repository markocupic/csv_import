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

$GLOBALS['TL_DCA']['tl_csv_import'] = array
(
    // Config
    'config' => array
    (
        'sql' => array(
            'keys' => array(
                'id' => 'primary',
            )
        ),
        'dataContainer' => 'Table',
        'onsubmit_callback' => array
        (
            array('tl_csv_import', 'initImport')
        ),
    ),
    // List
    'list' => array
    (
        'sorting' => array
        (
            'fields' => array('tstamp DESC'),
        ),
        'label' => array
        (
            'fields' => array('import_table'),
            'format' => '%s'
        ),
        'global_operations' => array( //
        ),
        'operations' => array
        (
            'edit' => array
            (
                'label' => &$GLOBALS['TL_LANG']['MSC']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif'
            ),
            'delete' => array
            (
                'label' => &$GLOBALS['TL_LANG']['MSC']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
            ),
            'show' => array
            (
                'label' => &$GLOBALS['TL_LANG']['MSC']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif'
            )
        )
    ),
    // Palettes
    'palettes' => array
    (
        'default' => 'response_box,import_table,selected_fields,field_separator,field_enclosure,import_mode,fileSRC',
    ),
    // Fields
    'fields' => array
    (
        'id' => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp' => array
        (
            'sql' => "int(10) unsigned NOT NULL default '0'"
        ),
        'import_table' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_csv_import']['import_table'],
            'inputType' => 'select',
            'options' => array('tl_member'),
            'eval' => array(
                'multiple' => false,
                'mandatory' => true,
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'field_separator' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_csv_import']['field_separator'],
            'inputType' => 'text',
            'default' => ';',
            'eval' => array(
                'mandatory' => true,
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'field_enclosure' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_csv_import']['field_enclosure'],
            'inputType' => 'text',
            'eval' => array(
                'mandatory' => false,
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'import_mode' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_csv_import']['import_mode'],
            'inputType' => 'select',
            'options' => array('append_entries', 'truncate_table'),
            'reference' => $GLOBALS['TL_LANG']['tl_csv_import'],
            'eval' => array(
                'multiple' => false,
                'mandatory' => true,
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
        'selected_fields' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_csv_import']['selected_fields'],
            'inputType' => 'checkbox',
            'options_callback' => array(
                'tl_csv_import',
                'optionsCbSelectedFields'
            ),
            'eval' => array(
                'multiple' => true,
            ),
            'sql' => "blob NULL"
        ),
        'fileSRC' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_csv_import']['fileSRC'],
            'inputType' => 'fileTree',
            'eval' => array(
                'multiple' => false,
                'fieldType' => 'radio',
                'files' => true,
                'mandatory' => true,
                'extensions' => 'csv',
                'submitOnChange' => true,
            ),
            'sql' => "blob NULL"
        ),
        'response_box' => array
        (
            'label' => &$GLOBALS['TL_LANG']['tl_csv_import']['response_box'],
            'inputType' => 'textarea',
            'input_field_callback' => array('tl_csv_import', 'generateResponseBox'),
            'eval' => array(
                'mandatory' => false,
            ),
            'sql' => "varchar(255) NOT NULL default ''"
        ),
    )
);

/**
 * Class tl_csv_import
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Marko Cupic
 * @author     Marko Cupic
 * @package    tl_csv_import
 */
class tl_csv_import extends Backend
{

    /**
     * init the import
     */
    public function initImport()
    {
        if (Input::post('FORM_SUBMIT') && Input::post('SUBMIT_TYPE') != 'auto') {
            $strTable = Input::post('import_table');
            $importMode = Input::post('import_mode');
            $arrSelectedFields = Input::post('selected_fields');
            $strFieldseparator = Input::post('field_separator');
            $strFieldenclosure = Input::post('field_enclosure');
            $objFile = FilesModel::findByPk(Input::post('fileSRC'));

            // call the import class if file exists
            if (null !== $objFile) {
                if (is_file(TL_ROOT . '/' . $objFile->path) && strtolower($objFile->extension) == 'csv') {
                    $objCsvImport = new CsvImport\CsvImport();
                    $objCsvImport->csvImport($objFile, $strTable, $importMode, $arrSelectedFields, $strFieldseparator, $strFieldenclosure, 'id');
                }
            }
        }
    }


    /**
     * @return array
     */
    public function optionsCbSelectedFields()
    {
        $objDb = Database::getInstance()->prepare('SELECT * FROM tl_csv_import WHERE id = ?')->execute(Input::get('id'));
        if ($objDb->import_table == '') return;
        $objFields = Database::getInstance()->listFields($objDb->import_table, 1);
        $arrOptions = array();
        foreach ($objFields as $field) {
            if ($field['name'] == 'PRIMARY') continue;
            if (in_array($field['name'], $arrOptions)) continue;
            $arrOptions[$field['name']] = $field['name'] . ' [' . $field['type'] . ']';
        }
        return $arrOptions;
    }

    /**
     * @return string
     */
    public function generateResponseBox()
    {
        $html = '';
        if ($_SESSION['csvImport']['response']) {
            $html .= sprintf('<div id="ctrl_response_box">%s</div>', $_SESSION['csvImport']['response']);
            unset($_SESSION['csvImport']);
        } else {
            $response = '<div id="ctrl_manual"><a href="%s"  target="_blank" data-lightbox="manual">%s</a></div>';
            $response = sprintf($response, 'system/modules/csv_import/assets/images/manual_screenshot.png', $GLOBALS['TL_LANG']['tl_csv_import']['manual']);
            $html .= sprintf('<div id="ctrl_response_box_manual">%s</div>', $response);
        }
        return $html;
    }
}           
              