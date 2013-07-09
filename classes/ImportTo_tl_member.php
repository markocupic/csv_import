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
 * Class ImportTo_tl_member
 *
 * @copyright  Marko Cupic 2013
 * @author     Marko Cupic
 * @package Csv_import
 */
class ImportTo_tl_member extends CsvImport implements ImportTo
{
    /**
     * @param $fieldname
     * @param $value
     * @param $set
     * @return mixed
     */
    public function prepareData($fieldname, $value, $set)
    {
        try {
            // set some default }values
            $set['tstamp'] = time();
            $set['createdOn'] = time();
            $set['dateAdded'] = time();

            if ($fieldname == 'password') {
                $set[$fieldname] = \Encryption::hash($value);
            }

            if ($fieldname == 'groups' || $fieldname == 'newsletter') {
                $set[$fieldname] = serialize(explode(',', $value));
            }

        } catch (\Exception $e) {
            $this->errorMessages[] = $e->getMessage();
            $this->hasError = true;
        }
        return $set;
    }


}

