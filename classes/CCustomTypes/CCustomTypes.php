<?php

/**
 * Created by PhpStorm.
 * Developer: Oreshkov Alexander
 * Email: asor86@ya.ru
 * Date: 05.08.2016
 */
class CCustomTypeElementDate
{
    function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'test_answers',
            'DESCRIPTION' => 'Ответы на  вопросы',
            'GetPropertyFieldHtml' => array('CCustomTypeElementDate', 'GetPropertyFieldHtml'),
            'ConvertToDB' => array('CCustomTypeElementDate', 'ConvertToDB'),
            'ConvertFromDB' => array('CCustomTypeElementDate', 'ConvertFromDB')
        );
    }

    function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $value['VALUE'] = json_decode($value['VALUE']);
        $answerText = '';
        if (!empty($value['VALUE']->ANSWER)) {
            $answerText = $value['VALUE']->ANSWER;
        }
        $TRUE_ANSWER = false;
        if (!empty($value['VALUE']->TRUE_ANSWER)) {
            $TRUE_ANSWER = true;
        }
        $html = '<label for="' . $strHTMLControlName["VALUE"] . '[ANSWER]">Ответ: </label>';
        $html .= '<input type="text" style="width:350px;" name="' . $strHTMLControlName["VALUE"] . '[ANSWER]" value="' . $answerText . '" id="' . $strHTMLControlName["VALUE"] . '[ANSWER]" /><br/>';
        $html .= '<label for="' . $strHTMLControlName["VALUE"] . '[TRUE_ANSWER]">Правильный ответ: </label>';
        if ($TRUE_ANSWER) {
            $html .= '<input checked="checked" type="checkbox" name="' . $strHTMLControlName["VALUE"] . '[TRUE_ANSWER]" id="' . $strHTMLControlName["VALUE"] . '[TRUE_ANSWER]" value="yes"  /><br/><hr/>';
        } else {
            $html .= '<input type="checkbox" name="' . $strHTMLControlName["VALUE"] . '[TRUE_ANSWER]" id="' . $strHTMLControlName["VALUE"] . '[TRUE_ANSWER]" value="yes"  /><br/><hr/>';
        }
        return $html;
    }

    function ConvertToDB($arProperty, $value)
    {
        if (!empty($value['VALUE']['ANSWER'])) {
            $value['VALUE'] = json_encode($value['VALUE']);
        }
        return $value;
    }

    function ConvertFromDB($arProperty, $value)
    {
        return $value;
    }
}