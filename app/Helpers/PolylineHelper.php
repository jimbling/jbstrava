<?php

namespace App\Helpers;

class PolylineHelper
{
    public static function decode($encoded)
    {
        $index = 0;
        $lat = 0;
        $lng = 0;
        $coordinates = [];

        $len = strlen($encoded);

        while ($index < $len) {

            $shift = 0;
            $result = 0;

            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);

            $dlat = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lat += $dlat;

            $shift = 0;
            $result = 0;

            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift += 5;
            } while ($b >= 0x20);

            $dlng = (($result & 1) ? ~($result >> 1) : ($result >> 1));
            $lng += $dlng;

            $coordinates[] = [
                $lat / 1e5,
                $lng / 1e5
            ];
        }

        return $coordinates;
    }
}
