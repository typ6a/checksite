@extends('master')
@section('title', 'Проверка сайта')
@section('content')
<div class="container col-md-8 col-md-offset-2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3> Результат проверки {{ $data['targetUrl'] }} </h3>
        </div>
        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif
        <table class="table table-bordered table-sm">
            <thead class="thead-light">
                <tr>
                    <th >№</th>
                    <th>Название проверки</th>
                    <th>Статус</th>
                    <th>&nbsp;</th>
                    <th >Текущее состояние</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan=2 >1</td>
                    <td rowspan=2 >Проверка наличия файла robots.txt</td>
                    @if ($data['robotstxtPresent'] === true)
                    <td class="table-success" rowspan=2 >Ок</td>
                    <td>Состояние</td>
                    <td>Файл robots.txt присутствует</td>
                </tr>
                <tr>
                    <td>Рекомендации</td>
                    <td>Доработки не требуются</td>
                    @endif
                    @if ($data['robotstxtPresent'] === false)
                    <td rowspan=2 class="table-danger">Ошибка</td>
                    <td>Состояние</td>
                    <td>Файл robots.txt отсутствует</td>
                </tr>
                <tr>
                    <td>Рекомендации</td>
                    <td>Программист: Создать файл robots.txt и разместить его на сайте.</td>
                    @endif
                </tr>
                @if ($data['robotstxtPresent'] === true)
                    <tr>
                        <td rowspan=2 >6</td>
                        <td rowspan=2 >Проверка указания директивы Host</td>
                        @if ($data['hostDirectiveNum'] > 0)
                        <td class="table-success" rowspan=2 >Ок</td>
                        <td>Состояние</td>
                        <td>Директива Host указана</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Доработки не требуются</td>
                        @endif
                        @if ($data['hostDirectiveNum'] === 0)
                        <td rowspan=2 class="table-danger">Ошибка</td>
                        <td>Состояние</td>
                        <td>В файле robots.txt не указана директива Host</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Программист: Для того, чтобы поисковые системы знали, какая версия сайта является основных зеркалом, необходимо прописать адрес основного зеркала в директиве Host. В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. Директива Host задётся в файле 1 раз, после всех правил.</td>
                        @endif
                    </tr>

                    @if ($data['hostDirectiveNum'] >= 1)
                    <tr>
                        <td rowspan=2 >8</td>
                        <td rowspan=2 >Проверка количества директив Host, прописанных в файле</td>
                        @if ($data['hostDirectiveNum'] === 1)
                        <td class="table-success" rowspan=2 >Ок</td>
                        <td>Состояние</td>
                        <td>В файле прописана 1 директива Host</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Доработки не требуются</td>
                        @endif
                        @if ($data['hostDirectiveNum'] > 1)
                        <td rowspan=2 class="table-danger">Ошибка</td>
                        <td>Состояние</td>
                        <td>В файле прописано несколько директив Host</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Программист: Директива Host должна быть указана в файле толоко 1 раз. Необходимо удалить все дополнительные директивы Host и оставить только 1, корректную и соответствующую основному зеркалу сайта</td>
                        @endif
                    </tr>
                    @endif

                    <tr>
                        <td rowspan=2 >10</td>
                        <td rowspan=2 >Проверка размера файла robots.txt</td>
                        @if ($data['robotstxtSize'] < 32)
                        <td class="table-success" rowspan=2 >Ок</td>
                        <td>Состояние</td>
                        <td>Размер файла robots.txt составляет {{ $data['robotstxtSize'] }}Кб, что находится в пределах допустимой нормы</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Доработки не требуются</td>
                        @endif
                        @if ($data['hostDirectiveNum'] > 1)
                        <td rowspan=2 class="table-danger">Ошибка</td>
                        <td>Состояние</td>
                        <td>Размера файла robots.txt составляет {{ $data['robotstxtSize'] }}Кб, что превышает допустимую норму</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Программист: Максимально допустимый размер файла robots.txt составляем 32Кб. Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб</td>
                        @endif
                    </tr>
                    <tr>
                        <td rowspan=2 >11</td>
                        <td rowspan=2 >Проверка указания директивы Sitemap</td>
                        @if ($data['sitemapDirectiveNum'] > 0)
                        <td class="table-success" rowspan=2 >Ок</td>
                        <td>Состояние</td>
                        <td>Директива Sitemap указана</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Доработки не требуются</td>
                        @endif
                        @if ($data['sitemapDirectiveNum'] === 0)
                        <td rowspan=2 class="table-danger">Ошибка</td>
                        <td>Состояние</td>
                        <td>В файле robots.txt не указана директива Sitemap</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Программист: Добавить в файл robots.txt директиву Sitemap</td>
                        @endif
                    </tr>
                    
                    <tr>
                        <td rowspan=2 >12</td>
                        <td rowspan=2 >Проверка кода ответа сервера для файла robots.txt</td>
                        @if ($data['httpCode'] === 200)
                        <td class="table-success" rowspan=2 >Ок</td>
                        <td>Состояние</td>
                        <td>Файл robots.txt отдаёт код ответа сервера 200</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Доработки не требуются</td>
                        @endif
                        @if ($data['httpCode'] != 200)
                        <td rowspan=2 class="table-danger">Ошибка</td>
                        <td>Состояние</td>
                        <td>При обращении к файлу robots.txt сервер возвращает код ответа {{ $data['httpCode'] }}</td>
                    </tr>
                    <tr>
                        <td>Рекомендации</td>
                        <td>Программист: Файл robots.txt должны отдавать код ответа 200, иначе файл не будет обрабатываться. Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращает код ответа 200</td>
                        @endif
                    </tr>
                @endif
            </tbody>
        </table>
        <input type="submit" name="makeExcel" value="експорт в Excel" onclick="makeExcel()" />
    </div>
</div>
@endsection