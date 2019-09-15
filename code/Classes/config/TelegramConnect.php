<?php
require_once "Config.php";
class TelegramConnect
{
    public static function sendMessage ($chatId, $message) {
        $url = Config::getBotConfig("ApiRequestUrl")."/sendMessage?chat_id=".$chatId."&text=".urlencode($message)."&parse_mode=html&disable_web_page_preview=true";
        file_get_contents($url);
    }

    public static function isAdmin ($chatId, $userId) {
        $url = Config::getBotConfig("ApiRequestUrl")."/getChatMember?chat_id=".$chatId."&user_id=".$userId;
        $teste = file_get_contents($url);
        error_log("Logando response:" . $teste);
    }
}
