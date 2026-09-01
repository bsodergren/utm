<?php

/**
 * This file is part of the UTM package. (c) Bjorn
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

    public static function getCsFixerRules($rule = 1)
    {
        $rules = self::getHeaderRules();

        switch ($rule) {
            case 1:
                $rules = array_merge($rules, self::getRulesetOne());
                // no break
            case 2:
                $rules = array_merge($rules, self::getRulesetTwo());
                // no break
            case 3:
                $rules = array_merge($rules, self::getRulesetThree());
                // no break
            default:
                $rules = array_merge($rules, self::getRulesetOne());
        }

        return $rules;
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
