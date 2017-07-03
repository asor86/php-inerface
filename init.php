    <?


require_once($_SERVER["DOCUMENT_ROOT"] . "/api/push/firebases/Sender.php");

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("PushNotification", "SendNotification"));

class PushNotification
{
    function SendNotification(&$arFields)
    {
        if ($arFields["IBLOCK_ID"] == 25) {
            if (!empty($arFields['PROPERTY_VALUES'][387][0]['VALUE'])) {
                CModule::IncludeModule("iblock");

                $DB_ELEMENT = CIBlockElement::GetList(
                    Array("SORT" => "ASC"),
                    Array("IBLOCK_ID" => "77", "ACTIVE" => "Y"),
                    false,
                    false,
                    Array("ID", "IBLOCK_ID", "PROPERTY_TOKEN")
                );
                $arElm = array();
                $arIDs = array();
                while ($ob = $DB_ELEMENT->GetNext()) {
                    $arElm[] = $ob;
                    $arIDs[] = $ob['PROPERTY_TOKEN_VALUE'];
                }


                $send = new Sender();
                $arResult = $send->sendMessage("Новость", $arFields['NAME'], $arIDs);
                $arFields["PROPERTY_VALUES"][388]["n0"]['VALUE'] = date("d.m.Y H:i:s", time());
                $arFields['PROPERTY_VALUES'][387][0]['VALUE'] = "";


                $arResultDecode = json_decode($arResult);
                foreach ($arResultDecode->results as $key => $res) {
                    if ($res->error) {
                        $elObj = new CIBlockElement;
                        $arTokenUpdate = Array(
                            "ACTIVE" => "N",
                        );
                        $elObj->Update($arElm[$key]["ID"], $arTokenUpdate);

                    }
                }
            }
        }
        if ($arFields["IBLOCK_ID"] == 2) {
            if (!empty($arFields['PROPERTY_VALUES'][389][0]['VALUE'])) {
                CModule::IncludeModule("iblock");

                $DB_ELEMENT = CIBlockElement::GetList(
                    Array("SORT" => "ASC"),
                    Array("IBLOCK_ID" => "77", "ACTIVE" => "Y"),
                    false,
                    false,
                    Array("ID", "IBLOCK_ID", "PROPERTY_TOKEN")
                );
                $arElm = array();
                $arIDs = array();
                while ($ob = $DB_ELEMENT->GetNext()) {
                    $arElm[] = $ob;
                    $arIDs[] = $ob['PROPERTY_TOKEN_VALUE'];
                }


                $send = new Sender();
                $arResult = $send->sendMessage("Новая публикация", $arFields['NAME'], $arIDs);
                $arFields["PROPERTY_VALUES"][390]["n0"]['VALUE'] = date("d.m.Y H:i:s", time());
                $arFields['PROPERTY_VALUES'][389][0]['VALUE'] = "";


                $arResultDecode = json_decode($arResult);
                foreach ($arResultDecode->results as $key => $res) {
                    if ($res->error) {
                        $elObj = new CIBlockElement;
                        $arTokenUpdate = Array(
                            "ACTIVE" => "N",
                        );
                        $elObj->Update($arElm[$key]["ID"], $arTokenUpdate);

                    }
                }
            }
        }

        return $arFields;
    }
}


require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/php_interface/classes/CCustomTypes/CCustomTypes.php");
AddEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CCustomTypeElementDate', 'GetUserTypeDescription'));


const IB_NEWS = 25;
const IB_ARTICLES = 2;
const IB_OPINION = 27;
const IB_CORP_NEWSPAPER = 66;

const IB_NEWS_EN = 62;
const IB_ARTICLES_EN = 58;
const IB_OPINION_EN = 63;

const _IMG_QUALITY_JPEG_ = 80;


function getDatePrint($dateTime)
{
    $dateToday = ParseDateTime(date('d.m.Y', time()));
    $dateYesterday = ParseDateTime(date('d.m.Y', strtotime('-1 day')));
    $dateTimeItem = ParseDateTime($dateTime);
    $dateItem = ParseDateTime(date('d.m.Y', strtotime($dateTime)));

    if (
        ($dateToday['DD'] == $dateItem['DD']) &&
        ($dateToday['MM'] == $dateItem['MM']) &&
        ($dateToday['YYYY'] == $dateItem['YYYY'])
    ) {
        return 'Сегодня в ' . $dateTimeItem['HH'] . ':' . $dateTimeItem['MI'];
    } elseif (
        ($dateYesterday['DD'] == $dateItem['DD']) &&
        ($dateYesterday['MM'] == $dateItem['MM']) &&
        ($dateYesterday['YYYY'] == $dateItem['YYYY'])
    ) {
        return 'Вчера в ' . $dateTimeItem['HH'] . ':' . $dateTimeItem['MI'];
    } else {
        return $dateTimeItem['DD'] . ' ' . ToLower(GetMessage("MONTH_" . intval($dateTimeItem["MM"]) . "_S")) . ' ' . $dateTimeItem['YYYY'] . ' в ' . $dateTimeItem['HH'] . ':' . $dateTimeItem['MI'];
    }
}


AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("NotificationMail", "onAdd"));

//AddEventHandler("iblock", "OnAfterIBlockElementUpdate", Array("NotificationMail", "onUpdate"));


class NotificationMail
{

    public static $url;
    public static $urlAdmin;
    public static $strUser;
    public static $strSection;
    public static $arIBlocks = array(
        2 => array("NAME" => "Публикации"),
        27 => array("NAME" => "Особое мнение"),
        35 => array("NAME" => "Компании"),
        40 => array("NAME" => "Аналитика"),
        41 => array("NAME" => "Лица отрасли"),
        47 => array("NAME" => "Дочернии компании"),
        55 => array("NAME" => "Инфографика"),

    );

    public function onAdd(&$arFields)
    {
        if (array_key_exists($arFields['IBLOCK_ID'], NotificationMail::$arIBlocks)) {
            self::getVars($arFields, NotificationMail::$arIBlocks);

            $arEventFields = array(
                "NAME" => $arFields['NAME'],
                "SECTION_NAME" => NotificationMail::$strSection,
                "URL" => NotificationMail::$url,
                "URL_ADMIN" => NotificationMail::$urlAdmin,
                "USER" => NotificationMail::$strUser
            );
            CEvent::Send("ADD_NEW_ELEMENT", "s1", $arEventFields);
        }
    }

    public function onUpdate(&$arFields)
    {
        if (array_key_exists($arFields['IBLOCK_ID'], NotificationMail::$arIBlocks)) {
            self::getVars($arFields, NotificationMail::$arIBlocks);

            $arEventFields = array(
                "NAME" => $arFields['NAME'],
                "SECTION_NAME" => NotificationMail::$strSection,
                "URL" => NotificationMail::$url,
                "URL_ADMIN" => NotificationMail::$urlAdmin,
                "USER" => NotificationMail::$strUser
            );
            CEvent::Send("UPDATE_NEW_ELEMENT", "s1", $arEventFields);
        }
    }


    public function getVars($arFields, $arIBlocks)
    {
        CModule::IncludeModule("iblock");
        $arUsers = CUser::GetByID($arFields['MODIFIED_BY'])->Fetch();
        NotificationMail::$strUser = $arUsers['ID'] . ' - ' . $arUsers['NAME'] . ' ' . $arUsers['LAST_NAME'] . ' [' . $arUsers['EMAIL'] . ']';

        if (!empty($arFields['IBLOCK_SECTION'])) {
            $DB_SECTION = CIBlockSection::GetList(
                Array("SORT" => "ASC"),
                Array("IBLOCK_ID" => $arFields['IBLOCK_ID'], "ID" => $arFields['IBLOCK_SECTION']),
                false,
                array("ID", "IBLOCK_ID", "NAME")
            );
            $arSection = $DB_SECTION->GetNext(); // Наименование раздела
            NotificationMail::$strSection = $arIBlocks[$arFields['IBLOCK_ID']]['NAME'] . ' - ' . $arSection['NAME'];
        } else {
            NotificationMail::$strSection = $arIBlocks[$arFields['IBLOCK_ID']]['NAME'];
        }


        $DB_ELEMENT = CIBlockElement::GetList(
            Array("SORT" => "ASC"),
            Array("IBLOCK_ID" => $arFields['IBLOCK_ID'], "ID" => $arFields['ID']),
            false,
            false,
            Array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL")
        );
        $DB_ELEMENT->SetUrlTemplates('', '', '');
        $arElement = $DB_ELEMENT->GetNext();

        /* url публичной части */
        NotificationMail::$url = "http://" . $_SERVER['SERVER_NAME'] . $arElement['DETAIL_PAGE_URL'];
        /* url административной части */
        NotificationMail::$urlAdmin = "http://" . $_SERVER['SERVER_NAME'] . "/bitrix/admin/iblock_element_edit.php?IBLOCK_ID={$arFields['IBLOCK_ID']}&type=articles&ID={$arFields['ID']}&lang=ru&find_section_section=&WF=Y";
    }
}


AddEventHandler("main", "OnEndBufferContent", "ChangeMyContent");
function ChangeMyContent(&$content)
{
    preg_match('/bitrix/is', $_SERVER['SCRIPT_URL'], $match);
    if (empty($match)) {
        $search = array(
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s'
        );
        $replace = array(
            '>',
            '<',
            '\\1'
        );
        $content = preg_replace($search, $replace, $content);
    }
}

function insertNoFollow($text)
{
    preg_match_all('/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ \'\"\s]*([^ \"\'>\s#]+)[^>]*>/', $text, $match);


    if (!empty($match[0])) {
        foreach ($match[0] as $_match) {

            preg_match('/nofollow/', $_match, $additionalMatch);

            if (empty($additionalMatch[0])) {
                $replace = '<a rel="nofollow" ';
                $pattern = '<a ';
                $_match_replace = str_replace($pattern, $replace, $_match);
                $text = str_replace($_match, $_match_replace, $text);
            }
        }
    }

    return $text;
}

function insertNoFollowNew($text)
{
    $countReplace = 1;
    preg_match_all('/<[Aa][\s]{1}[^>]*[Hh][Rr][Ee][Ff][^=]*=[ \'\"\s]*([^ \"\'>\s#]+)[^>]*>/', $text, $match);

    if (!empty($match[0])) {

        preg_match("/peretok\\.ru/is", $match[1][0], $matchPer);

        if (!empty($matchPer[0])) {
            for ($i = 0, $iCount = count($match[0]); $i <= $iCount; $i++) {
                $replace = '';
                if (!empty($match[1][$i])) {
                    $replace = '<a href="' . $match[1][$i] . '">';
                }
                $pattern = '';
                if (!empty($match[0][$i])) {
                    $pattern = $match[0][$i];
                }
                $text = str_replace($pattern, $replace, $text, $countReplace);
            }
        } else {
            for ($i = 0, $iCount = count($match[0]); $i <= $iCount; $i++) {
                $replace = '';
                if (!empty($match[1][$i])) {
                    $replace = '<a href="' . $match[1][$i] . '" target="_blank" rel="noreferrer nofollow">';
                }
                $pattern = '';
                if (!empty($match[0][$i])) {
                    $pattern = $match[0][$i];
                }
                $text = str_replace($pattern, $replace, $text, $countReplace);
            }
        }
    }

    return $text;
}

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("textMod", "nofollow"));
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", Array("textMod", "nofollow"));

class textMod
{
    function nofollow(&$arFields)
    {
        $arIblocks = array(2, 25, 27);
        if (in_array($arFields["IBLOCK_ID"], $arIblocks)) {
            $arFields['PREVIEW_TEXT'] = insertNoFollowNew($arFields['PREVIEW_TEXT']);
            $arFields['DETAIL_TEXT'] = insertNoFollowNew($arFields['DETAIL_TEXT']);
        }
    }
}




?>