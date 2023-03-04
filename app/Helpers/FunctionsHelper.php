<?php

namespace App\Helpers;

class FunctionsHelper
{

    public static function verifyIfExistTitle($arrayTitles, $title){
        foreach ($arrayTitles as $arrayTitle) {
            if ($arrayTitle['title'] == $title){
                return true;
            }
        }
        return false;
    }

    public static function getIdWooByTitle($arrayTitles, $title){
        foreach ($arrayTitles as $arrayTitle) {
            if ($arrayTitle['title'] == $title){
                return $arrayTitle['id_woo'];
            }
        }
        return null;
    }

}
