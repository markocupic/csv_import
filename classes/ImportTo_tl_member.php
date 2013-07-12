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
     * @return mixed|void
     */
    public function prepareDataForInsert($fieldname, $value)
    {
        try {
            // set some default values
            $this->set['tstamp'] = time();
            $this->set['createdOn'] = time();
            $this->set['dateAdded'] = time();

            if ($fieldname == 'password') {
                $this->set[$fieldname] = \Encryption::hash($value);
            }

            if ($fieldname == 'groups' || $fieldname == 'newsletter') {
                $this->set[$fieldname] = serialize(explode(',', $value));
            }

        } catch (\Exception $e) {
            $this->errorMessages[] = $e->getMessage();
            $this->hasError = true;
        }
    }


}

