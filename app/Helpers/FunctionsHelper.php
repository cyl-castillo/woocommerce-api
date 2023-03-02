<?php

namespace App\Helpers;

class FunctionsHelper
{

    public static function verifyIfExistTitle($arrayTitles, $title){
        foreach ($arrayTitles as $arrayTitle) {
            if ($arrayTitle == $title){
                return true;
            }
        }
        return false;
    }

}
