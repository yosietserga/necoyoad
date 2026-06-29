<?php
final class utf8 {
    public function utf8_isvalid($str)
    {
            /*
            From rfc3629
            UTF8-octets = *( UTF8-char )
            UTF8-char   = UTF8-1 / UTF8-2 / UTF8-3 / UTF8-4
            UTF8-1      = %x00-7F
            UTF8-2      = %xC2-DF UTF8-tail

            UTF8-3      = %xE0 %xA0-BF UTF8-tail / %xE1-EC 2( UTF8-tail ) /
                                     %xED %x80-9F UTF8-tail / %xEE-EF 2( UTF8-tail )
            UTF8-4      = %xF0 %x90-BF 2( UTF8-tail ) / %xF1-F3 3( UTF8-tail ) /
                                     %xF4 %x80-8F 2( UTF8-tail )
            UTF8-tail   = %x80-BF
            */
            $is_utf8 = preg_match(
            '/^([^'.
            '[\x00-\x7F]'.
            '|[\xC2-\xDF][\x80-\xBF]'.
            '|\xE0[\xA0-\xBF][\x80-\xBF]'.
            '|[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}'.
            '|\xED[\x80-\x9F][\x80-\xBF]'.
            '|\xF0[\x90-\xBF][\x80-\xBF]{2}'.
            '|[\xF1-\xF3][\x80-\xBF]{3}'.
            '|\xF4[\x80-\x8F][\x80-\xBF]{2}'.
            '])*$/', $str);
            return !(bool) $is_utf8;
    }

    public function utf8_charlength($str, $offset=0)
    {
            if (strlen($str{$offset}) == 0) {
                    return 0;
            }

            $ord = ord($str{$offset});

            if ($ord <= 0x7F) {
                    return 1;
            } elseif ($ord >= 0xC2 && $ord <= 0xDF) {
                    return 2;
            } elseif ($ord >= 0xE0 && $ord <= 0xEF) {
                    return 3;
            } elseif ($ord >= 0xF0 && $ord <= 0xF4) {
                    return 4;
            } else {
                    return null;
            }
    }

    public function utf8_strlen($str)
    {
            if (!$this->utf8_isvalid($str)) {
                    return null;
            }

            $charnum = 0;
            $offset = 0;
            $len = strlen($str);

            while ($offset < $len) {
                    $charlen = $this->utf8_charlength($str, $offset);

                    if (is_null($charlen)) {
                            $offset++;
                            continue;
                    }

                    $charnum++;
                    $offset += $charlen;

                    // Just to make sure we arn't reading past the end of the string
                    if ($offset >= $len) {
                            break;
                    }
            }

            return $charnum;
    }

    function utf8_substr($string, $start, $length=null)
    {
            if ($length===0 || strlen($string) == 0) {
                    return '';
            }

            if (!$this->utf8_isvalid($string)) {
                    return null;
            }

            $buffer='';
            $charnum = 0;
            $offset = 0;
            $len = strlen($string);

            // If they want the whole string from a certain point, just set the length
            // to the length of the string (since we break out once we are done anyway
            if ($length === null) {
                    $length = $len;
            }

            while ($offset < $len) {
                    $charlen = $this->utf8_charlength($string, $offset);

                    if (is_null($charlen)) {
                            $offset++;
                            continue;
                    }

                    // If the character is one we want add it to the buffer
                    if ($charnum >= $start && $charnum < $start + $length) {
                            $buffer .= substr($string, $offset, $charlen);
                    }

                    $charnum++;
                    $offset += $charlen;

                    // Just to make sure we arn't reading past the end of the string
                    if ($offset >= $len) {
                            break;
                    }
            }
            return $buffer;
    }
}

