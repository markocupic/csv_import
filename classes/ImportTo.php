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
 * Interface
 * Class ImportTo
 *
 * @copyright  Marko Cupic 2013
 * @author     Marko Cupic
 * @package Csv_import
 */
interface ImportTo
{
    /**
     * @param $fieldname
     * @param $value
     * @param $set
     * @return mixed
     */
    public function prepareData($fieldname, $value, $set);


}