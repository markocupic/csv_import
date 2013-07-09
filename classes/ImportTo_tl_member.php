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
class ImportTo_tl_member extends CsvImport
{
    /**
     *
     */
    public static function importTo_tl_member($fieldname, $value)
    {
        if ($fieldname == 'password') {
            $value = \Encryption::hash($value);
        }

        if ($fieldname == 'groups' || $fieldname == 'newsletter') {
            $value = serialize(explode(',', $value));

        }
        return $value;
    }


}

