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
 * Namespace
 */
namespace CsvImport;


/**
 * Class CsvImport
 *
 * @copyright  Marko Cupic 2013
 * @author     Marko Cupic
 * @package Csv_import
 */
class CsvImport extends \System
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
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param object
     * @param string
     * @param string
     * @param string
     * @param string
     * @param string
     */
    public function csvImport($objFile, $strTable, $importMode, $arrSelectedFields = null, $strFieldseparator = ';', $strFieldenclosure = '')
    {
        $this->fs = $strFieldseparator;
        $this->fe = $strFieldenclosure;
        $this->importMode = $importMode;
        $this->strTable = $strTable;
        $this->errorMessages = array();
        $this->hasError = null;

        if ($this->importMode == 'truncate_table') {
            \Database::getInstance()->execute('TRUNCATE TABLE `' . $this->strTable . '`');
        }
        $arrSelectedFields = is_array($arrSelectedFields) ? $arrSelectedFields : array();
        if (count($arrSelectedFields) < 1)
            return;
        // create a tmp file
        $tmpFile = new \File('system/tmp/mod_csv_import_' . md5(time()) . '.csv');
        $tmpFile->truncate();
        $tmpFile->write($this->formatFile($objFile));
        $tmpFile->close();
        $arrFileContent = $tmpFile->getContentAsArray();

        // get array with the fieldnames
        $arrFieldnames = explode($this->fs, $arrFileContent[0]);

        // trim quotes
        $arrFieldnames = array_map(function ($strFieldname, $fe) {
            return trim($strFieldname, $fe);
        }, $arrFieldnames, array($this->fe));

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
            }, $arrLine, array($this->fe));

            // define the insert array
            $this->set = array();

            // traverse the line
            foreach ($arrFieldnames as $k => $fieldname) {
                // continue if field is excluded from import
                if (!in_array($fieldname, $arrSelectedFields)) continue;

                // continue if field is the PRIMARY_KEY
                if ($this->importMode == 'append_entries' && strtolower($fieldname) == $this->strPk) continue;

                $value = $arrLine[$k];
                $value = str_replace('[NEWLINE-N]', chr(10), $value);
                $value = str_replace('[DOUBLE-QUOTE]', '"', $value);

                // continue if there is no content
                if (!strlen($value)) continue;

                // detect the encoding
                $encoding = mb_detect_encoding($value, "auto", true);
                if ($encoding == 'ASCII' || $encoding == '') {
                    $value = utf8_encode($value);
                }

                // store value int the insert array
                $this->set[$fieldname] = $value;

                // call the table-specific helper class
                $strClass = 'ImportTo_' . $this->strTable;
                if (class_exists($strClass)) {
                    $objClass = new $strClass;
                    $objClass->prepareDataForInsert($fieldname, $value);
                }
            }

            try {
                // Insert into Database
                $objInsertStmt = \Database::getInstance()->prepare("INSERT INTO " . $this->strTable . " %s")->set($this->set)->executeUncached();

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
            } catch (\Exception $e) {
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

        $tmpFile->delete();
    }

    /**
     * @param object
     * @return string
     */
    private function formatFile($objFile)
    {
        $file = new \File($objFile->path);
        $fileContent = $file->getContent();
        $fileContent = str_replace('\"', '[DOUBLE-QUOTE]', $fileContent);
        $fileContent = str_replace('\r\n', '[NEWLINE-RN]', $fileContent);
        $fileContent = str_replace(chr(13) . chr(10), '[NEWLINE-RN]', $fileContent);
        $fileContent = str_replace('\n', '[NEWLINE-N]', $fileContent);
        $fileContent = str_replace(chr(10), '[NEWLINE-N]', $fileContent);
        $fileContent = str_replace('[NEWLINE-RN]', chr(13) . chr(10), $fileContent);
        return $fileContent;
    }
}

