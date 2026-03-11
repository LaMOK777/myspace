<?php
// Функция для получения цвета в зависимости от значения
function getStatusColor($value, $threshold) {
    if ($value > $threshold) return "#4CAF50";
    if ($value > $threshold * 0.5) return "#FFC107";
    return "#F44336";
}

// Функция для форматирования байт
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Собираем данные
$memory_limit = ini_get('memory_limit');
$upload_max = ini_get('upload_max_filesize');
$post_max = ini_get('post_max_size');
$max_execution = ini_get('max_execution_time');
$free_disk = disk_free_space(__DIR__);
$total_disk = disk_total_space(__DIR__);
$free_disk_percent = ($free_disk / $total_disk) * 100;

// Данные для графика расширений
$extensions_loaded = 0;
$extensions_total = 8;
$extensions_list = ['mysqli', 'pdo_mysql', 'curl', 'json', 'mbstring', 'xml', 'zip', 'gd'];
foreach ($extensions_list as $ext) {
    if (extension_loaded($ext)) $extensions_loaded++;
}
$extensions_percent = ($extensions_loaded / $extensions_total) * 100;

// Эмодзи для разных ситуаций
$server_emojis = [
    'mysql' => '🐬',
    'php' => '🐘',
    'server' => '🚀',
    'database' => '🗄️',
    'happy' => '😊',
    'sad' => '😢',
    'cool' => '😎',
    'fire' => '🔥',
    'star' => '⭐',
    'rocket' => '🚀'
];

echo "
<style>
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-5px); }
        100% { transform: translateY(0px); }
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        margin: 0; 
        padding: 20px; 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    h1 { 
        color: white; 
        text-align: center;
        font-size: 3em;
        margin-bottom: 30px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        animation: float 3s ease-in-out infinite;
    }
    
    h1 .emoji {
        display: inline-block;
        animation: spin 10s linear infinite;
    }
    
    .section { 
        background: rgba(255,255,255,0.95); 
        padding: 25px; 
        margin: 25px 0; 
        border-radius: 20px; 
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .section:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 80px rgba(0,0,0,0.4);
    }
    
    .section h2 {
        color: #4a5568;
        margin-top: 0;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section h2 .emoji-large {
        font-size: 1.8em;
        animation: pulse 2s ease-in-out infinite;
    }
    
    table { 
        border-collapse: collapse; 
        width: 100%; 
        margin: 15px 0; 
        border-radius: 15px;
        overflow: hidden;
    }
    
    th { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white; 
        padding: 15px; 
        text-align: left; 
        font-weight: 600;
    }
    
    td { 
        padding: 12px 15px; 
        background: white; 
        border-bottom: 1px solid #e2e8f0;
    }
    
    tr:hover td {
        background: #f7fafc;
    }
    
    .success { color: #4CAF50; font-weight: bold; }
    .error { color: #F44336; font-weight: bold; }
    .warning { color: #FFC107; font-weight: bold; }
    
    .info { 
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: none;
        border-radius: 15px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
    }
    
    /* Прогресс бары */
    .progress-container {
        background: #edf2f7;
        border-radius: 25px;
        height: 30px;
        margin: 10px 0;
        overflow: hidden;
        position: relative;
    }
    
    .progress-bar {
        height: 100%;
        border-radius: 25px;
        transition: width 1s ease-in-out;
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            45deg,
            rgba(255,255,255,0.2) 25%,
            transparent 25%,
            transparent 50%,
            rgba(255,255,255,0.2) 50%,
            rgba(255,255,255,0.2) 75%,
            transparent 75%,
            transparent
        );
        background-size: 50px 50px;
        animation: move 2s linear infinite;
    }
    
    @keyframes move {
        0% { background-position: 0 0; }
        100% { background-position: 50px 50px; }
    }
    
    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-weight: bold;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        z-index: 1;
    }
    
    /* Карточки */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 20px;
        color: white;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: scale(1.05);
    }
    
    .stat-card .stat-icon {
        font-size: 3em;
        margin-bottom: 10px;
        animation: float 3s ease-in-out infinite;
    }
    
    .stat-card .stat-value {
        font-size: 2.5em;
        font-weight: bold;
        margin: 10px 0;
    }
    
    .stat-card .stat-label {
        font-size: 1.1em;
        opacity: 0.9;
    }
    
    /* Индикатор загрузки */
    .loader {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #667eea;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 20px auto;
    }
    
    /* Кнопка обновления */
    .refresh-btn {
        background: white;
        border: none;
        padding: 15px 30px;
        border-radius: 50px;
        font-size: 1.2em;
        font-weight: bold;
        color: #667eea;
        cursor: pointer;
        margin: 20px auto;
        display: block;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    
    .refresh-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }
    
    /* Тултипы */
    .tooltip {
        position: relative;
        display: inline-block;
        cursor: help;
    }
    
    .tooltip .tooltiptext {
        visibility: hidden;
        width: 200px;
        background: #2d3748;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 10px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
        font-size: 0.9em;
        pointer-events: none;
    }
    
    .tooltip:hover .tooltiptext {
        visibility: visible;
        opacity: 1;
    }
</style>

<div class='container'>
    <h1>
        <span class='emoji'>🔍</span> 
        Диагностика сервера 
        <span class='emoji'>{$server_emojis['cool']}</span>
    </h1>";

// Карточки со статистикой
echo "
    <div class='stats-grid'>
        <div class='stat-card'>
            <div class='stat-icon'>🐘</div>
            <div class='stat-value'>PHP " . phpversion() . "</div>
            <div class='stat-label'>Версия</div>
        </div>
        <div class='stat-card'>
            <div class='stat-icon'>⏱️</div>
            <div class='stat-value'>" . date('H:i:s') . "</div>
            <div class='stat-label'>Текущее время</div>
        </div>
        <div class='stat-card'>
            <div class='stat-icon'>💾</div>
            <div class='stat-value'>" . round($free_disk / 1024 / 1024 / 1024, 1) . " GB</div>
            <div class='stat-label'>Свободно места</div>
        </div>
        <div class='stat-card'>
            <div class='stat-icon'>🔌</div>
            <div class='stat-value'>{$extensions_loaded}/{$extensions_total}</div>
            <div class='stat-label'>Расширений</div>
        </div>
    </div>";

// Секция 1: Общая информация с анимацией
echo "
    <div class='section'>
        <h2>
            <span class='emoji-large'>📊</span>
            Общая информация
            <span class='tooltip'>❓
                <span class='tooltiptext'>Основная информация о сервере и запросе</span>
            </span>
        </h2>
        <table>
            <tr><th>Параметр</th><th>Значение</th></tr>
            <tr><td>📅 Дата и время</td><td>" . date('Y-m-d H:i:s') . " <span class='tooltip'>⏰<span class='tooltiptext'>Текущее серверное время</span></span></td></tr>
            <tr><td>🌍 Ваш IP</td><td>" . $_SERVER['REMOTE_ADDR'] . "</td></tr>
            <tr><td>🖥️ Сервер</td><td>" . $_SERVER['SERVER_NAME'] . "</td></tr>
            <tr><td>🔌 Порт</td><td>" . $_SERVER['SERVER_PORT'] . "</td></tr>
            <tr><td>📡 Протокол</td><td>" . $_SERVER['SERVER_PROTOCOL'] . "</td></tr>
            <tr><td>🎯 Метод запроса</td><td>" . $_SERVER['REQUEST_METHOD'] . "</td></tr>
        </table>
    </div>";

// Секция 2: Проверка PHP расширений с графиком
echo "
    <div class='section'>
        <h2>
            <span class='emoji-large'>🧩</span>
            PHP расширения
            <span class='tooltip'>❓
                <span class='tooltiptext'>Установленные и активные расширения PHP</span>
            </span>
        </h2>
        
        <div class='progress-container'>
            <div class='progress-bar' style='width: {$extensions_percent}%; background: linear-gradient(90deg, #4CAF50, #8BC34A);'>
                <div class='progress-text'>Загружено {$extensions_loaded} из {$extensions_total}</div>
            </div>
        </div>
        
        <table>
            <tr><th>Расширение</th><th>Статус</th><th>Описание</th></tr>";

$ext_descriptions = [
    'mysqli' => 'Работа с MySQL',
    'pdo_mysql' => 'PDO для MySQL',
    'curl' => 'HTTP запросы',
    'json' => 'Работа с JSON',
    'mbstring' => 'Многобайтовые строки',
    'xml' => 'Работа с XML',
    'zip' => 'Архивация ZIP',
    'gd' => 'Работа с изображениями'
];

foreach ($extensions_list as $ext) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? "✅ Загружено" : "❌ Не загружено";
    $class = $loaded ? "success" : "error";
    $emoji = $loaded ? "👍" : "👎";
    echo "<tr>
            <td><strong>$ext</strong></td>
            <td class='$class'>$status $emoji</td>
            <td>{$ext_descriptions[$ext]}</td>
          </tr>";
}
echo "</table></div>";

// Секция 3: Дисковая информация с визуализацией
echo "
    <div class='section'>
        <h2>
            <span class='emoji-large'>💾</span>
            Дисковая информация
        </h2>
        
        <div class='progress-container'>
            <div class='progress-bar' style='width: " . (100 - $free_disk_percent) . "%; background: linear-gradient(90deg, #F44336, #FF9800);'>
                <div class='progress-text'>Занято " . round(100 - $free_disk_percent, 1) . "%</div>
            </div>
        </div>
        
        <table>
            <tr><th>Параметр</th><th>Значение</th><th>Статус</th></tr>
            <tr>
                <td>📁 Директория скрипта</td>
                <td>" . __DIR__ . "</td>
                <td>" . (is_writable(__DIR__) ? "✅ Доступна запись" : "❌ Нет доступа") . "</td>
            </tr>
            <tr>
                <td>💿 Свободно места</td>
                <td>" . round($free_disk / 1024 / 1024 / 1024, 2) . " GB</td>
                <td class='" . ($free_disk_percent > 20 ? "success" : "error") . "'>" . ($free_disk_percent > 20 ? "✅ OK" : "⚠️ Мало места") . "</td>
            </tr>
            <tr>
                <td>💿 Всего места</td>
                <td>" . round($total_disk / 1024 / 1024 / 1024, 2) . " GB</td>
                <td>📊 " . round($free_disk_percent, 1) . "% свободно</td>
            </tr>
        </table>
    </div>";

// Секция 4: Память и нагрузка
echo "
    <div class='section'>
        <h2>
            <span class='emoji-large'>⚡</span>
            Производительность
        </h2>
        <table>
            <tr><th>Параметр</th><th>Значение</th><th>Рекомендация</th></tr>
            <tr>
                <td>🧠 Лимит памяти PHP</td>
                <td>" . $memory_limit . "</td>
                <td class='" . (intval($memory_limit) >= 128 ? "success" : "warning") . "'>" . (intval($memory_limit) >= 128 ? "✅ OK" : "⚠️ Рекомендуется минимум 128M") . "</td>
            </tr>
            <tr>
                <td>📤 Макс. размер загрузки</td>
                <td>" . $upload_max . "</td>
                <td class='" . (intval($upload_max) >= 8 ? "success" : "warning") . "'>" . (intval($upload_max) >= 8 ? "✅ OK" : "⚠️ Для загрузки файлов нужно больше") . "</td>
            </tr>
            <tr>
                <td>📦 Макс. POST размер</td>
                <td>" . $post_max . "</td>
                <td class='" . (intval($post_max) >= 8 ? "success" : "warning") . "'>" . (intval($post_max) >= 8 ? "✅ OK" : "⚠️ Должен быть не меньше upload_max_filesize") . "</td>
            </tr>
            <tr>
                <td>⏱️ Время выполнения</td>
                <td>" . $max_execution . " сек</td>
                <td class='" . ($max_execution >= 30 ? "success" : "warning") . "'>" . ($max_execution >= 30 ? "✅ OK" : "⚠️ Рекомендуется минимум 30 сек") . "</td>
            </tr>
        </table>
    </div>";

// Секция 5: Советы по улучшению (динамические)
echo "
    <div class='section'>
        <h2>
            <span class='emoji-large'>💡</span>
            Умные советы
        </h2>
        <ul style='list-style-type: none; padding: 0;'>";

// Анализируем и даем советы
if ($extensions_loaded < $extensions_total) {
    $missing = array_diff($extensions_list, array_filter($extensions_list, 'extension_loaded'));
    echo "<li style='margin: 10px 0; padding: 10px; background: #fff3cd; border-radius: 10px;'>
            🔧 Не хватает расширений: " . implode(', ', $missing) . " — можно установить через docker-php-ext-install
          </li>";
}

if (intval($memory_limit) < 128) {
    echo "<li style='margin: 10px 0; padding: 10px; background: #d4edda; border-radius: 10px;'>
            🚀 Увеличьте memory_limit до 256M для лучшей производительности
          </li>";
}

if ($free_disk_percent < 20) {
    echo "<li style='margin: 10px 0; padding: 10px; background: #f8d7da; border-radius: 10px;'>
            ⚠️ Мало свободного места на диске! Очистите временные файлы
          </li>";
}

echo "</ul></div>";

// Секция 6: Случайный факт о PHP
$facts = [
    "🐘 PHP изначально означало 'Personal Home Page Tools'",
    "🎮 PHP используется на 78% всех веб-сайтов",
    "🏆 PHP 8.0 в 3 раза быстрее PHP 5.6",
    "🎂 PHP был создан в 1994 году Расмусом Лердорфом",
    "📚 Самая популярная CMS на PHP - WordPress (43% всех сайтов)",
    "💰 Laravel - самый популярный PHP-фреймворк 2024 года"
];

echo "
    <div class='info'>
        <strong>🎲 Случайный факт о PHP:</strong> " . $facts[array_rand($facts)] . "
    </div>";

// Кнопка обновления
echo "
    <button class='refresh-btn' onclick='location.reload()'>
        🔄 Обновить диагностику {$server_emojis['rocket']}
    </button>";

// Индикатор загрузки (скрыт по умолчанию)
echo "
    <div id='loader' class='loader' style='display: none;'></div>

    <script>
        // Показывать загрузчик при обновлении
        document.querySelector('.refresh-btn').addEventListener('click', function() {
            document.getElementById('loader').style.display = 'block';
            this.style.opacity = '0.5';
        });
        
        // Добавляем время последнего обновления
        const lastUpdate = new Date().toLocaleTimeString();
        const info = document.createElement('div');
        info.style.textAlign = 'center';
        info.style.color = 'white';
        info.style.marginTop = '20px';
        info.innerHTML = '🕐 Последнее обновление: ' + lastUpdate;
        document.querySelector('.container').appendChild(info);
    </script>";

// Оригинальный phpinfo() в красивом оформлении
echo "
    <details style='margin: 20px 0; background: white; border-radius: 15px; padding: 20px;'>
        <summary style='cursor: pointer; font-weight: bold; font-size: 1.2em; color: #667eea;'>
            📋 Показать полный phpinfo() (для профи)
        </summary>
        <div style='margin-top: 20px;'>
            " . ob_start() . phpinfo() . ob_get_clean() . "
        </div>
    </details>
    
    <div style='text-align: center; color: white; margin: 20px 0; font-size: 0.9em;'>
        Сделано с {$server_emojis['fire']} для диагностики | 
        <span class='tooltip'>⚠️ Важно
            <span class='tooltiptext'>Удалите этот файл после использования!</span>
        </span>
    </div>
</div>";
?>
