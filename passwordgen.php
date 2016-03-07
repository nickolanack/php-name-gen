<?php

$adjectives = json_decode(file_get_contents(__DIR__ . '/adjectives.json'));
$nouns      = json_decode(file_get_contents(__DIR__ . '/nouns.json'));

$pwd = $adjectives[array_rand($adjectives)];

$parts = array(
    $nouns[array_rand($nouns)], // . $nouns[array_rand($nouns)],
    rand(10, 99),
);

shuffle($parts);
$pwd .= '-' . implode('-', $parts);
function replaceOneOccurance(&$str, $chr, $withChr)
{

    $i   = 0;
    $pos = array();
    while (($i = strpos($str, $chr, $i)) !== false) {
        $pos[] = $i;
        $i++;
    }

    if (count($pos)) {
        $p   = $pos[array_rand($pos)];
        $str = substr($str, 0, $p) . $withChr . substr($str, $p + 1);
        return 1;
    }
    return 0;
}

function capitolizeOneLetter(&$str)
{

    for ($i = 0; $i < 5; $i++) {

        $p = rand(0, strlen($str) - 1);

        if (ctype_alpha($str{p})) {
            $str = substr($str, 0, $p) . strtoupper($str{$p}) . substr($str, $p + 1);
            return 1;
        }

    }
    return 0;

}

$mutationFunctions = array(

    function (&$str) {
        return replaceOneOccurance($str, 's', '5');
    },

    function (&$str) {
        return replaceOneOccurance($str, 'o', '0');
    },

    function (&$str) {
        return replaceOneOccurance($str, 'i', '!');
    },
    function (&$str) {
        return replaceOneOccurance($str, '8', '&');
    },
    function (&$str) {
        return replaceOneOccurance($str, 'e', '3');
    },
    function (&$str) {
        return replaceOneOccurance($str, '7', '?');
    },
    function (&$str) {
        return capitolizeOneLetter($str);
    },
    function (&$str) {
        return capitolizeOneLetter($str);
    },
    function (&$str) {
        return capitolizeOneLetter($str);
    },
    function (&$str) {
        $str .= '$';
        return 1;
    },
    function (&$str) {
        $str = '$' . $str;
        return 1;
    },
    function (&$str) {
        $str .= '_';
        return 1;
    },
    function (&$str) {
        $str = '_' . $str;
        return 1;
    },

);

$minMutations = 1;
$mutations    = 0;

while ($mutations < $minMutations && count($mutationFunctions)) {

    $mutate = array_splice($mutationFunctions, array_rand($mutationFunctions), 1);
    $mutate = $mutate[0];
    $mutations += $mutate($pwd);

}

capitolizeOneLetter($pwd);

echo $pwd;
