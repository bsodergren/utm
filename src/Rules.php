<?php
/**
 *
 *   UTM Common Class
 *
 */

namespace UTM;

use UTM\Rules\FixerRules;

class Rules
{
    use FixerRules;

    public static $fileHeaderComment = 'This file is part of the UTM package. (c) Bjorn';

    public static function setFileHeaderComment($comment)
    {
        self::$fileHeaderComment = $comment;
    }

    public static function getCsFixerRules()
    {
        return self::getRulesetOne();
    }

    public static function getRules()
    {
        return [
            'utm_source'   => [
                'required'  => true,
                'type'      => 'string',
                'maxLength' => 50,
            ],
            'utm_medium'   => [
                'required'  => true,
                'type'      => 'string',
                'maxLength' => 50,
            ],
            'utm_campaign' => [
                'required'  => true,
                'type'      => 'string',
                'maxLength' => 100,
            ],
            'utm_term'     => [
                'required'  => false,
                'type'      => 'string',
                'maxLength' => 100,
            ],
            'utm_content'  => [
                'required'  => false,
                'type'      => 'string',
                'maxLength' => 100,
            ],
        ];
    }
}
