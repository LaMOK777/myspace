#!/bin/bash
# Скрипт для подсчета использования swap всеми процессами

# Определяем цветовые коды
RED='\033[0;31m'      # Красный цвет
GREEN='\033[0;32m'    # Зеленый цвет
YELLOW='\033[1;33m'   # Желтый цвет
BLUE='\033[0;34m'     # Синий цвет для заголовков
CYAN='\033[0;36m'     # Голубой цвет для границ
WHITE='\033[1;37m'    # Белый цвет для текста
NC='\033[0m'          # No Color (сброс цвета)

# Функция для форматирования чисел с выравниванием
format_number() {
    printf "%8s" "$1"
}

# Функция для преобразования KB в читаемый формат (KB, MB, GB)
format_size() {
    local size=$1
    if [ $size -gt 1048576 ]; then
        echo "$(echo "scale=2; $size/1048576" | bc) GB"
    elif [ $size -gt 1024 ]; then
        echo "$(echo "scale=2; $size/1024" | bc) MB"
    else
        echo "${size} KB"
    fi
}

SUM=0
OVERALL=0

# Создаем временный файл для хранения данных
TEMP_FILE=$(mktemp)

# Заголовок таблицы
echo -e "${CYAN}┌──────────┬──────────────────────────────┬──────────────┐${NC}"
echo -e "${CYAN}│${WHITE} SWAP (KB) ${CYAN}│${WHITE} PROCESS NAME                 ${CYAN}│${WHITE} PID         ${CYAN}│${NC}"
echo -e "${CYAN}├──────────┼──────────────────────────────┼──────────────┤${NC}"

# Ищем все директории процессов в /proc
for DIR in `find /proc/ -maxdepth 1 -type d | egrep "^/proc/[0-9]" | sort -V`; do
    PID=`echo $DIR | cut -d / -f 3`
    PROGNAME=`ps -p $PID -o comm --no-headers 2>/dev/null | cut -c1-28` # Обрезаем имя до 28 символов
    
    if [ -z "$PROGNAME" ]; then
        continue
    fi
    
    # Ищем в smaps все строки Swap и суммируем значения
    for SWAP in `grep Swap $DIR/smaps 2>/dev/null| awk '{ print $2 }'`
    do
        let SUM=$SUM+$SWAP
    done
    
    # Сохраняем данные во временный файл для последующей сортировки
    if [[ $SUM -gt 0 ]]; then
        echo "$SUM:$PROGNAME:$PID" >> $TEMP_FILE
        let OVERALL=$OVERALL+$SUM
    fi
    SUM=0
done

# Сортируем по использованию swap (по убыванию) и выводим в таблицу
sort -t: -k1 -rn $TEMP_FILE | while IFS=':' read SWAP PROGNAME PID; do
    # Определяем цвет в зависимости от размера swap
    if [[ $SWAP -gt 10000 ]]; then
        COLOR=$RED
    elif [[ $SWAP -gt 5000 ]]; then
        COLOR=$YELLOW
    else
        COLOR=$GREEN
    fi
    
    # Форматируем вывод с фиксированной шириной колонок
    printf "${CYAN}│${COLOR} %8s ${CYAN}│${WHITE} %-28s ${CYAN}│${WHITE} %-12s ${CYAN}│${NC}\n" "$SWAP" "$PROGNAME" "$PID"
done

# Нижняя граница таблицы
echo -e "${CYAN}└──────────┴──────────────────────────────┴──────────────┘${NC}"

# Выводим общий результат в красивой рамке
echo ""
echo -e "${CYAN}═══════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}📊 Общее использование SWAP: ${WHITE}$(format_number $OVERALL) KB ($(format_size $OVERALL))${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════${NC}"

# Удаляем временный файл
rm -f $TEMP_FILE
