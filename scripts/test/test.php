<?php namespace Metayota; include_once('../metayota/library.php');  
function removeDataAttributes($svgString) {
    if (!is_string($svgString) || empty($svgString)) {
        // Nichts zu tun, wenn der Input kein String oder leer ist
        return $svgString;
    }

    // Regex, um alle data-* Attribute zu entfernen (mit oder ohne Wert)
    $regex = '/\sdata-[a-z0-9-]+(?:="[^"]*")?/i';

    // Ersetzen aller data-* Attribute mit einem leeren String
    $cleanedSvgString = preg_replace($regex, '', $svgString);

    // Überprüfen, ob preg_replace erfolgreich war
    if (null === $cleanedSvgString) {
        // Im Fehlerfall die ursprüngliche Zeichenkette zurückgeben
        return $svgString;
    }

    return $cleanedSvgString;
}


echo 'removing2';
$svgString = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 211 19" fill="none" data-v-93b86943>...</svg>';
$cleanedSvgString = removeDataAttributes($svgString);
echo htmlentities($cleanedSvgString);
?>