php
<?php
// Стили для красивого отображения
echo "
<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
    h2 { color: #666; margin-top: 30px; }
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th { background: #4CAF50; color: white; padding: 10px; text-align: left; }
    td { padding: 8px; background: white; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .info { background: #e7f3fe; border-left: 6px solid #2196F3; padding: 10px; margin: 10px 0; }
    .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
</style>
";

echo "<h1>🔍 Диагностика сервера</h1>";

// Секция 1: Общая информация
echo "<div class='section'>";
echo "<h2>📊 Общая информация</h2>";
echo "<table>";
echo "<tr><th>Параметр</th><th>Значение</th></tr>";
echo "<tr><td>Дата и время</td><td>" . date('Y-m-d H:i:s') . "</td></tr>";
echo "<tr><td>Ваш IP</td><td>" . $_SERVER['REMOTE_ADDR'] . "</td></tr>";
echo "<tr><td>Сервер</td><td>" . $_SERVER['SERVER_NAME'] . "</td></tr>";
echo "<tr><td>Порт</td><td>" . $_SERVER['SERVER_PORT'] . "</td></tr>";
echo "<tr><td>Протокол</td><td>" . $_SERVER['SERVER_PROTOCOL'] . "</td></tr>";
echo "<tr><td>Метод запроса</td><td>" . $_SERVER['REQUEST_METHOD'] . "</td></tr>";
echo "</table>";
echo "</div>";

// Секция 2: Проверка PHP расширений
echo "<div class='section'>";
echo "<h2>🧩 Ключевые PHP расширения</h2>";
echo "<table>";
echo "<tr><th>Расширение</th><th>Статус</th></tr>";

$extensions = ['mysqli', 'pdo_mysql', 'curl', 'json', 'mbstring', 'xml', 'zip', 'gd'];
foreach ($extensions as $ext) {
    $status = extension_loaded($ext) ? "✅ Загружено" : "❌ Не загружено";
    $class = extension_loaded($ext) ? "success" : "error";
    echo "<tr><td>$ext</td><td class='$class'>$status</td></tr>";
}
echo "</table>";
echo "</div>";

// Секция 3: Информация о файловой системе
echo "<div class='section'>";
echo "<h2>💾 Дисковая информация</h2>";
echo "<table>";
echo "<tr><th>Параметр</th><th>Значение</th></tr>";
echo "<tr><td>Директория скрипта</td><td>" . __DIR__ . "</td></tr>";
echo "<tr><td>Свободно места</td><td>" . round(disk_free_space(__DIR__) / 1024 / 1024 / 1024, 2) . " GB</td></tr>";
echo "<tr><td>Всего места</td><td>" . round(disk_total_space(__DIR__) / 1024 / 1024 / 1024, 2) . " GB</td></tr>";
echo "</table>";
echo "</div>";

// Секция 4: Память и нагрузка
echo "<div class='section'>";
echo "<h2>⚡ Производительность</h2>";
echo "<table>";
echo "<tr><th>Параметр</th><th>Значение</th></tr>";
echo "<tr><td>Лимит памяти PHP</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td>Макс. размер загрузки</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>Макс. POST размер</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>Время выполнения</td><td>" . ini_get('max_execution_time') . " сек</td></tr>";
echo "</table>";
echo "</div>";

// Предупреждение о безопасности
echo "<div class='info'>";
echo "⚠️ <strong>Важно:</strong> Удалите этот файл после диагностики! Он показывает много информации о сервере.";
echo "</div>";

// Оригинальный phpinfo() в свернутом виде
echo "<details>";
echo "<summary style='cursor: pointer; font-weight: bold; margin: 20px 0;'>📋 Показать полный phpinfo()</summary>";
phpinfo();
echo "</details>";
?>
