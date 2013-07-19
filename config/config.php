<?php

/**
 * Contao Open Source CMS
 * Copyright (c) 2005-2013 Leo Feyer
 * @package Csv_import
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
/**
 * -------------------------------------------------------------------------
 * BACK END MODULES
 * -------------------------------------------------------------------------
 */
if (TL_MODE == 'BE') {
    $GLOBALS['BE_MOD']['system']['csv_import'] = array(
        'icon' => 'system/modules/csv_import/assets/images/database_add.png',
        'tables' => array(
            'tl_csv_import'
        )
    );

    if ($_GET['do'] == 'csv_import') {
        $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/csv_import/assets/js/csv_import.js';
        $GLOBALS['TL_CSS'][] = 'system/modules/csv_import/assets/css/csv_import.css';

        // Register insert-Hooks

        // Prepare Data for import into tl_member
        $GLOBALS['TL_HOOKS']['csv_import'][] = array('ImportCsvToTlMember', 'prepareDataForInsert');

        // Prepare Data for import into tl_user
        //$GLOBALS['TL_HOOKS']['csv_import'] = array('ImportCsvToTlUser', 'prepareDataForInsert');

        // Prepare Data for import into tl_news
        //$GLOBALS['TL_HOOKS']['csv_import'] = array("ImportCsvToTlNews", 'prepareDataForInsert');
    }

}

