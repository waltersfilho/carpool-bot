<?php
require_once "Config.php";
class TelegramConnect
{
    /*TODO make methods non-static, implement constructor*/
    public static function sendMessage ($chatId, $message) {
        $url = Config::getBotConfig("ApiRequestUrl")."/sendMessage?chat_id=".$chatId."&text=".urlencode($message)."&parse_mode=html";
        file_get_contents($url);
    }
}
