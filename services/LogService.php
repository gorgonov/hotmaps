<?php

namespace services;

class LogService
{
    const FILENAME = 'err.log';

    /**
     * Выводит строку в файл FILENAME в корневом каталоге сайта
     * Формат строки:
     * <дата время> | <title> | <сообщение>
     *
     * @param string $message
     * @param string $title
     */
    public function error(string $message, string $title='none')
    {
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/". self::FILENAME , 'a');
        fwrite($fp,  date("d.m.Y H:i:s | ") . $title . " | " . $message . "\n");
        fclose($fp);
    }
}
