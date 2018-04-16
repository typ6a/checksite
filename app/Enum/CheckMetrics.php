<?php

namespace App\Enum;

class CheckMetrics
{

    const ROBOTS_EXISTS  = 'robotstxtPresents';

    const HOST_EXISTS    = 'hostDirectivePresents';

    const HOST_COUNT     = 'hostDirectiveNum';

    const ROBOTS_SIZE    = 'robotstxtSize';

    const SITEMAP_EXISTS = 'sitemapDirectiveExists';

    const HTTP_CODE      = 'httpCode';

    public static  $metrics = [

            self::ROBOTS_EXISTS => [
                'title' => 'Проверка наличия файла robots.txt',
                'number' => '1',
                'status' => [
                    'ok' => [
                        'title' => 'OK',
                        'state' => 'Файл robots.txt присутствует',
                        'recomendation' => 'Доработки не требуются',
                    ],
                    'error' => [
                        'title' => 'Ошибка',
                        'state' => 'Файл robots.txt отсутствует',
                        'recomendation' => 'Программист: Создать файл robots.txt и разместить его на сайте.',
                    ],
                ],
            ],
            self::HOST_EXISTS => [
                'title' => 'Проверка указания директивы Host',
                'number' => '6',
                'status' => [
                    'ok' => [
                        'title' => 'OK',
                        'state' => 'Директива Host указана',
                        'recomendation' => 'Доработки не требуются',
                    ],
                    'error' => [
                        'title' => 'Ошибка',
                        'state' => 'В файле robots.txt не указана директива Host',
                        'recomendation' => 'Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.',
                    ],
                ],
            ],
            self::HOST_COUNT => [
                'title' => 'Проверка количества директив Host, прописанных в файле',
                'number' => '8',
                'status' => [
                    'ok' => [
                        'title' => 'OK',
                        'state' => 'В файле прописана %s директива Host',
                        'recomendation' => 'Доработки не требуются',
                    ],
                    'error' => [
                        'title' => 'Ошибка',
                        'state' => 'В файле прописано несколько директив Host',
                        'recomendation' => 'Программист: Директива Host должна быть указана в файле толоко 1 раз. Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую основному зеркалу сайта',
                    ],
                ],
            ],
            self::ROBOTS_SIZE => [
                'title' => 'Проверка размера файла robots.txt',
                'number' => '10',
                'status' => [
                    'ok' => [
                        'title' => 'OK',
                        'state' => 'Размер файла robots.txt составляет %s, что находится в пределах допустимой нормы',
                        'recomendation' => 'Доработки не требуются',
                    ],
                    'error' => [
                        'title' => 'Ошибка',
                        'state' => 'Размера файла robots.txt составляет %s, что превышает допустимую норму',
                        'recomendation' => 'Программист: Максимально допустимый размер файла robots.txt составляем 32 кб. Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб',
                    ],
                ],
            ],
            self::SITEMAP_EXISTS => [
                'title' => 'Проверка указания директивы Sitemap',
                'number' => '11',
                'status' => [
                    'ok' => [
                        'title' => 'OK',
                        'state' => 'Директива Sitemap указана',
                        'recomendation' => 'Доработки не требуются',
                    ],
                    'error' => [
                        'title' => 'Ошибка',
                        'state' => 'В файле robots.txt не указана директива Sitemap',
                        'recomendation' => 'Программист: Добавить в файл robots.txt директиву Sitemap',
                    ],
                ],
            ],
            self::HTTP_CODE => [
                'title' => 'Проверка кода ответа сервера для файла robots.txt',
                'number' => '12',
                'status' => [
                    'ok' => [
                        'title' => 'OK',
                        'state' => 'Файл robots.txt отдаёт код ответа сервера 200',
                        'recomendation' => 'Доработки не требуются',
                    ],
                    'error' => [
                        'title' => 'Ошибка',
                        'state' => 'При обращении к файлу robots.txt сервер возвращает код ответа %s',
                        'recomendation' => 'Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращает код ответа 200',
                    ],
                ],
            ],
        ];        
}