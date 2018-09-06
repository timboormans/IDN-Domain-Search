<?php
/**
 * Class DIDN
 *
 * This algorithm creates a comparable format of IDN domainnames which makes it possible to search the mysql
 * specifically for IDN characters using an extra precomputed column.
 *
 * The algorithm creates a searchable hash key of the inputted text string, for example a domain name, which
 * can be used to for your website's search function without all the UTF-8 hassle.
 *
 * Make sure your input is always only once encoded. If you trigger the utf8_encode() twice on a particular
 * string it will scramble the string making your input corrupt.
 *
 * @author Tim Boormans (tim@directwebsolutions.nl)
 * @copyright Direct Web Solutions B.V.
 * @date August 2014
 */
class DIDN {

    /**
     * Meant for encoding each character of the inputted string (this is the default function used)
     * @param $domain_name_incl_tld
     * @return string
     */
    public static function convert($domain_name_incl_tld) {
        $out_str = '';
        $len = mb_strlen($domain_name_incl_tld);
        for($i = 0; $i < $len; $i++) {
            $utf8Character = mb_substr($domain_name_incl_tld, $i, 1);
            $ord = self::one_char($utf8Character);
            $out_str .= '.'.$ord.'.';
        }
        return $out_str;
    }

    public static function one_char($utf8Character) {
        $enc = mb_detect_encoding($utf8Character);
        $conv = mb_convert_encoding($utf8Character, 'UCS-4BE', $enc);
        $out = unpack('N', $conv);
        list(, $ord) = $out;
        return $ord;
    }

}
