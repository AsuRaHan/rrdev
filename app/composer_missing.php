<!DOCTYPE html>
<html>
<head>
    <title>Установите Composer</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>
<body>
<div class="container">

    <h1 class="display-4">К сожалению, для работы этого приложения необходимо установить Composer.</h1>


    <p>Команды для установки Composer на <a href="https://getcomposer.org/" target="_blank"
                                            rel="nofollow noopener noreferrer external" data-wpel-link="internal">общий
            хостинг</a>, Linux (ПК или сервер) и macOS одинаковы.</p>
    <pre>php composer.phar self-update</pre>
    <pre>composer self-update</pre>
    <pre>composer install</pre>
    <p>Следуйте инструкции, чтобы узнать, как установить Composer в вашей системе:</p>
    <ol>
        <li>Подключитесь к вашему хостинг-аккаунту через SSH.</li>
        <li>Загрузите Composer с официального сайта, используя следующую команду:
            <pre>php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"</pre>
        </li>
        <li>Проверьте подпись установщика (<strong>SHA-384</strong>), чтобы убедиться, что файл установщика не
            повреждён. Введите:
            <pre>php -r "if (hash_file('sha384', 'composer-setup.php') === '<?= file_get_contents('https://composer.github.io/installer.sig'); ?>') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"</pre>
            <p>Длинная строка символов в приведённой выше команде («<strong>e0012edf…</strong>») — подпись установщика.
                Она меняется каждый раз, когда выходит новая версия Composer. Поэтому обязательно загрузите последнюю
                версию <strong>SHA-384</strong> с <a href="https://composer.github.io/pubkeys.html" target="_blank"
                                                     rel="nofollow noopener noreferrer external"
                                                     data-wpel-link="external">этой страницы</a>.</p></li>
        <li>Как только это будет сделано, вы сможете установить <strong>Composer</strong> локально или глобально.
            Локальная установка означает, что менеджер зависимостей будет храниться в вашем текущем каталоге, и вы
            должны будете указать путь перед выполнением соответствующих команд. Между тем, глобальная установка
            позволяет вам запускать Composer из любой точки вашей системы, сохраняя его в каталоге <strong>/usr/local/bin</strong>.
            Вот как реализовать оба метода:
            <ul>
                <li><strong>Локальная установка</strong>:
                    <pre>php composer-setup.php</pre>
                </li>
                <li><strong>Глобальная установка</strong>:
                    <pre>php composer-setup.php --install-dir=/usr/local/bin --filename=composer</pre>
                    <p>Вы получите следующий результат:</p>
                    <pre>All settings correct for using Composer
Downloading...

Composer (version 1.10.5) successfully installed to: /usr/local/bin/composer</pre>
                </li>
            </ul>
        </li>
        <li>Как только это будет сделано, удалите установщик:
            <pre class="installer">php -r "unlink('composer-setup.php');"</pre>
        </li>
        <li>Проверьте установку Composer:
            <pre>composer</pre>
            <p>Командная строка покажет приверно следующий результат:</p>
            <pre>   ______
  / ____/___ ____ ___ ____ ____ ________ _____
 / / / __ / __ `__ / __ / __ / ___/ _ / ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__ ) __/ /
____/____/_/ /_/ /_/ .___/____/____/___/_/
                  /_/

Composer version 1.10.5 2020-02-12 16:20:11</pre>
        </li>
    </ol>
</div>

</body>
</html>