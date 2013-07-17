<?php
/**
 * Contao Open Source CMS
 * Copyright (c) 2005-2013 Leo Feyer
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
        'onload_callback' => array
        (
            array('tl_csv_import', 'onloadCallback')
        ),
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
            /*
            'options_callback' => array(
                'tl_csv_import',
                'optionsCbGetTables'
            ),
            */
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
     * string
     * primary key
     */
    public $strTable;

    /**
     * string
     * primary key
     */
    public $strPk = 'id';

    /**
     * string
     * field enclosure
     */
    public $fe;

    /**
     * string
     * field separator
     */
    public $fs;

    /**
     * array
     * data-array
     */
    public $set;

    /**
     * string
     * import mode ("append" or "truncate table before insert")
     */
    public $importMode;

    /**
     * string
     * primary key
     */
    public $errorMessages;

    /**
     * string
     * primary key
     */
    public $hasError;

    /**
     * onload callback
     */
    public function onloadCallback()
    {
        if ($_SESSION['csvImport']['response']) {
            // set the palette
            $GLOBALS['TL_DCA']['tl_csv_import']['palettes']['default'] = 'response_box';
        }
    }

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
                    $strSrc = $objFile->path;
                    $this->csvImport($strSrc, $strTable, $importMode, $arrSelectedFields, $strFieldseparator, $strFieldenclosure, 'id');
                }
            }
        }
    }

    /**
     * @param object
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     */
    public function csvImport($csvSrc, $strTable, $importMode, $arrSelectedFields = null, $strFieldseparator = ';', $strFieldenclosure = '')
    {
        $this->fs = $strFieldseparator;
        $this->fe = $strFieldenclosure;
        $this->importMode = $importMode;
        $this->strTable = $strTable;
        $this->errorMessages = array();
        $this->hasError = null;

        // Get the fieldname with the PRIMARY KEY
        $this->strPk = $this->getPk($this->strTable) ? $this->getPk($this->strTable) : $this->strPk;

        if ($this->importMode == 'truncate_table') {
            Database::getInstance()->execute('TRUNCATE TABLE `' . $this->strTable . '`');
        }
        $arrSelectedFields = is_array($arrSelectedFields) ? $arrSelectedFields : array();
        if (count($arrSelectedFields) < 1)
            return;
        // get file content as array
        $objFile = new File ($csvSrc);
        $arrFileContent = $objFile->getContentAsArray();

        // get array with the fieldnames
        $arrFieldnames = explode($this->fs, $arrFileContent[0]);

        // trim quotes
        $arrFieldnames = array_map(function ($strFieldname, $fe) {
            return trim($strFieldname, $fe);
        }, $arrFieldnames, array_fill(0, count($arrFieldnames), $this->fe));

        $row = 0;
        // traverse the lines
        foreach ($arrFileContent as $line => $lineContent) {
            // first line contains the fieldnames
            if ($line == 0) continue;

            // get line
            $arrLine = explode($this->fs, $lineContent);

            // trim quotes
            $arrLine = array_map(function ($fieldContent, $fe) {
                return trim($fieldContent, $fe);
            }, $arrLine, array_fill(0, count($arrLine), $this->fe));

            // define the insert array
            $this->set = array();

            // traverse the line
            foreach ($arrFieldnames as $k => $fieldname) {
                // continue if field is excluded from import
                if (!in_array($fieldname, $arrSelectedFields)) continue;

                // continue if field is the PRIMARY_KEY
                if ($this->importMode == 'append_entries' && strtolower($fieldname) == $this->strPk) continue;

                $value = $arrLine[$k];

                // continue if there is no content
                if (!strlen($value)) continue;

                // detect the encoding
                $encoding = mb_detect_encoding($value, "auto", true);
                if ($encoding == 'ASCII' || $encoding == '') {
                    $value = utf8_encode($value);
                }

                // store value int the insert array
                $this->set[$fieldname] = $value;
                // Trigger the csv_import-Hook
                if (is_array($GLOBALS['TL_HOOKS']['csv_import'])) {
                    foreach ($GLOBALS['TL_HOOKS']['csv_import'] as $hook) {
                        if (class_exists($hook[0])) {
                            $objClass = new $hook[0];
                            $this->set = $objClass->$hook[1]($this, $this->strTable, $fieldname, $value, $this->set);
                        }
                    }
                }
            }

            try {
                // Insert into Database
                $objInsertStmt = Database::getInstance()->prepare("INSERT INTO " . $this->strTable . " %s")->set($this->set)->executeUncached();

                if ($objInsertStmt->affectedRows) {
                    $insertID = $objInsertStmt->insertId;

                    // Call the oncreate_callback
                    if (is_array($GLOBALS['TL_DCA'][$this->strTable]['config']['oncreate_callback'])) {
                        foreach ($GLOBALS['TL_DCA'][$this->strTable]['config']['oncreate_callback'] as $callback) {
                            $this->import($callback[0]);
                            $this->$callback[0]->$callback[1]($this->strTable, $insertID, $this->set, $this);
                        }
                    }

                    // Add a log entry
                    $this->log('A new entry "' . $this->strTable . '.id=' . $insertID . '" has been created.', __CLASS__ . ' ' . __FUNCTION__ . '()', TL_GENERAL);
                }
            } catch (Exception $e) {
                $this->errorMessages[] = $e->getMessage();
                $this->hasError = true;
            }
            $row++;
        }
        if ($this->hasError) {
            $message = $GLOBALS['TL_LANG']['tl_csv_import']['error_annunciation'] . ':<br><br><br>';
            $message .= implode('<br><br><br>', $this->errorMessages);
            $message .= '<span class="red">';
            $message .= '</span>';
            $_SESSION['csvImport']['response'] = $message;
        } else {
            $_SESSION['csvImport']['response'] = '<span class="green">' . sprintf($GLOBALS['TL_LANG']['tl_csv_import']['success_annunciation'], $row, $this->strTable) . '</span>';
        }
    }

    /**
     * get the PRIMARY KEY
     * @param $strTable
     * @return string|null
     */
    private function getPk($strTable)
    {
        $pk = null;
        $arrFields = Database::getInstance()->listFields($strTable);
        if (!is_array($arrFields)) return null;
        foreach ($arrFields as $field) {
            if ($field['index'] == 'PRIMARY') {
                $pk = $field['name'];
            }
        }
        return $pk;
    }

    /**
     * @return array
     */
    public function optionsCbGetTables()
    {
        $objTables = Database::getInstance()->listTables();
        $arrOptions = array();
        foreach ($objTables as $table) {
            $arrOptions[] = $table;
        }
        return $arrOptions;
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
              