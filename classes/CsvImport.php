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
class CsvImport extends \Backend
{
    /**
     * string
     * primary key
     */
    public static $strPk = 'id';
    /**
     * string
     * field enclosure
     */
    public static $fe;
    /**
     * string
     * primary key
     */
    public static $errorMessages;
    /**
     * string
     * primary key
     */
    public static $hasError;

    /**
     * init the import
     */
    public static function initImport()
    {
        if (\Input::post('SUBMIT_TYPE') == 'auto')
            return;
        $strTable = \Input::post('import_table');
        $importMode = \Input::post('import_mode');
        $arrSelectedFields = \Input::post('selected_fields');
        $strFieldseparator = \Input::post('field_separator');
        $strFieldenclosure = \Input::post('field_enclosure');
        $objFile = \FilesModel::findByPk(\Input::post('fileSRC'));
        if (null !== $objFile) {
            if (is_file(TL_ROOT . '/' . $objFile->path) && strtolower($objFile->extension) == 'csv') {
                self::importCsv($objFile, $strTable, $importMode, $arrSelectedFields, $strFieldseparator, $strFieldenclosure);
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
    public static function importCsv($objFile, $strTable, $importMode, $arrSelectedFields = null, $strFieldseparator = ';', $strFieldenclosure = '')
    {
        $fs = $strFieldseparator;
        $fe = $strFieldenclosure;
        self::$fe = $fe;
        if ($importMode == 'truncate_table') {
            \Database::getInstance()->execute('TRUNCATE TABLE `' . $strTable . '`');
        }
        $arrSelectedFields = is_array($arrSelectedFields) ? $arrSelectedFields : array();
        if (count($arrSelectedFields) < 1)
            return;
        // create a tmp file
        $tmpFile = new \File('system/tmp/' . md5(time()) . '.csv');
        $tmpFile->truncate();
        $tmpFile->write(self::formatFile($objFile));
        $tmpFile->close();
        $arrFileContent = $tmpFile->getContentAsArray();
        $arrFieldnames = explode($fs, $arrFileContent[0]);
        // trim quotes
        $arrFieldnames = array_map(function ($strFieldname) {
            return trim($strFieldname, CsvImport::$fe);
        }, $arrFieldnames);
        self::$errorMessages = array();
        $row = 0;
        foreach ($arrFileContent as $line => $lineContent) {
            if ($line == 0) continue;
            $arrLine = explode($fs, $lineContent);
            // trim quotes
            $arrLine = array_map(function ($fieldContent) {
                return trim($fieldContent, CsvImport::$fe);
            }, $arrLine);

            $set = array();

            // traverse the line
            foreach ($arrFieldnames as $k => $fieldname) {
                // continue if field is excluded from import
                if (!in_array($fieldname, $arrSelectedFields)) continue;
                // continue if field is the PRIMARY_KEY
                if ($importMode == 'append_entries' && strtolower($fieldname) == self::$strPk) continue;
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

                $set[$fieldname] = $value;

                // call the table-specific helper class
                $strClass = 'ImportTo_' . $strTable;
                if (class_exists($strClass)) {
                    $set = $strClass::prepareData($fieldname, $value, $set);
                }
            }

            // Insert into Database
            try {
                \Database::getInstance()->prepare("INSERT INTO " . $strTable . " %s")->set($set)->executeUncached();
            } catch (\Exception $e) {
                self::$errorMessages[] = $e->getMessage();
                self::$hasError = true;
            }
            $row++;
        }
        if (self::$hasError) {
            $message = 'Beim Importvorgang kam es zu mindestens einem Fehler. Bitte konsultieren Sie die Fehlermeldung:<br><br><br>';
            $message .= implode('<br><br><br>', self::$errorMessages);
            $message .= '<span class="red">';
            $message .= '</span>';
            $_SESSION['csvImport']['response'] = $message;
        } else {
            $_SESSION['csvImport']['response'] = sprintf('<span class="green">Es wurden %s Datensätze erfolgreich in %s angelegt.</span>', $row, $strTable);
        }

        $tmpFile->delete();
    }

    /**
     * @param object
     * @return string
     */
    private static function formatFile($objFile)
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

