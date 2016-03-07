<?php

class PasswordGenerator
{

    public static function Generate($options = array())
    {

        $config = array_merge(array(
            'symbols' => true,
            'join' => '',
            'mutations' => 2,
        ), $options);

        $adjectives = json_decode(file_get_contents(__DIR__ . '/adjectives.json'));

        if (count($adjectives) > 0) {
            sort($adjectives);
            $adjectives = array_filter(array_unique($adjectives), function ($w) {
                if (strlen($w) < 6) {
                    return true;
                }
                return false;
            });
            if (!empty($adjectives)) {
                file_put_contents(__DIR__ . '/adjectives.json', json_encode(array_values($adjectives), JSON_PRETTY_PRINT));
            }
        }

        $nouns = json_decode(file_get_contents(__DIR__ . '/nouns.json'));

        if (count($nouns) > 0) {
            sort($nouns);
            $nouns = array_filter(array_unique($nouns), function ($w) {
                if (strlen($w) < 6) {
                    return true;
                }
                return false;
            });
            if (!empty($nouns)) {
                file_put_contents(__DIR__ . '/nouns.json', json_encode(array_values($nouns), JSON_PRETTY_PRINT));
            }

        }

        $pwd = $adjectives[array_rand($adjectives)];

        $parts = array(
            $nouns[array_rand($nouns)], // . $nouns[array_rand($nouns)],
            //   rand(10, 99),

        );

        shuffle($parts);
        $pwd .= $config['join'] . implode($config['join'], $parts);

        $mutationFunctions = array(

            function (&$str) {
                return self::_replaceOneOccurance($str, 's', '5');
            },
            function (&$str) {
                return self::_replaceOneOccurance($str, 'o', '0');
            },
            function (&$str) {
                return self::_replaceOneOccurance($str, 'e', '3');
            },
            function (&$str) {
                return self::_capitolizeOneLetter($str);
            },

            function (&$str) {
                $str .= '_';
                return 1;
            },

        );

        if ($config['symbols']) {
            $mutationFunctions = array_merge($mutationFunctions, array(
                function (&$str) {
                    return self::_replaceOneOccurance($str, '7', '?');
                },
                function (&$str) {
                    return self::_replaceOneOccurance($str, 'i', '!');
                },
                function (&$str) {
                    return self::_replaceOneOccurance($str, '8', '&');
                },
                function (&$str) {
                    $str .= '$';
                    return 1;
                },
            ));

        }

        $minMutations = $config['mutations'];
        $mutations = 0;

        while ($mutations < $minMutations && count($mutationFunctions)) {

            $mutate = array_splice($mutationFunctions, array_rand($mutationFunctions), 1);
            $mutate = $mutate[0];
            $mutations += $mutate($pwd);

        }

        return $pwd;

    }

    protected static function _replaceOneOccurance(&$str, $chr, $withChr)
    {

        $i = 0;
        $pos = array();
        while (($i = strpos($str, $chr, $i)) !== false) {
            $pos[] = $i;
            $i++;
        }

        if (count($pos)) {
            $p = $pos[array_rand($pos)];
            $str = substr($str, 0, $p) . $withChr . substr($str, $p + 1);
            return 1;
        }
        return 0;
    }

    protected static function _capitolizeOneLetter(&$str)
    {

        for ($i = 0; $i < 5; $i++) {

            $p = rand(0, strlen($str) - 1);

            if (ctype_alpha($str{$p})) {
                $str = substr($str, 0, $p) . strtoupper($str{$p}) . substr($str, $p + 1);
                return 1;
            }

        }
        return 0;

    }

}

if (realpath($argv[0]) === __FILE__) {

    echo PasswordGenerator::Generate();
}
