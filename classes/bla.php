<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Marko
 * Date: 09.07.13
 * Time: 22:57
 * To change this template use File | Settings | File Templates.
 */

if (strlen($text) >= 35) {
    echo substr($text, 0, strpos($text, " ", 30)) . "[nbsp]&hellip;";
} else {
    echo $text;
}?>