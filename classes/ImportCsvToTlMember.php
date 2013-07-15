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
 * Class ImportCsvToTlMember
 *
 * @copyright  Marko Cupic 2013
 * @author     Marko Cupic
 * @package Csv_import
 */
class ImportCsvToTlMember extends tl_csv_import
{
    /**
     * @param $objDCA
     * @param $strTable
     * @param $fieldname
     * @param $value
     * @param $set
     * @return mixed
     */
    public function prepareDataForInsert(tl_csv_import $objImport, $strTable, $fieldname, $value, $set)
    {
        try {
            // set some default values
            $set['tstamp'] = time();
            $set['createdOn'] = time();
            $set['dateAdded'] = time();

            if ($fieldname == 'password') {
                $set[$fieldname] = \Encryption::hash($value);
            }

            if ($fieldname == 'groups' || $fieldname == 'newsletter') {
                $set[$fieldname] = serialize(explode(',', $value));
            }

        } catch (Exception $e) {
            $objImport->errorMessages[] = $e->getMessage();
            $objImport->hasError = true;
        }
        return $set;
    }

}

