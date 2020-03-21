<?php


namespace Nicodinus\UmeyeApi\Utils;


final class Utils
{
    public static function isImplementsClassname($target, string $compareClassname): bool
    {
        return array_search($compareClassname, class_implements($target)) !== false;
    }

    public static function compareClassname($target, string $compareClassname): bool
    {
        return $target == $compareClassname
            || is_a($target, $compareClassname)
            || is_subclass_of($target, $compareClassname)
            //|| $target instanceof $compareClassname
        ;
    }
}