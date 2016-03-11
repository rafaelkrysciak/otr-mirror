<?php


/**
 * Convert a numeric value to human readable string like 2M or 3KB
 *
 * @param numeric $size
 * @param int $precision
 * @return string human readable byte string
 */
function byteToSize($size, $precision = 2)
{
    $sizes = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    for($i = 0; $size >= 1024 && $i < 9; $i++) {
        $size /= 1024;
    }

    return number_format(round($size, $precision), $precision, ',', '.') .' '. $sizes[$i];
}