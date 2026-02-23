#!/bin/bash

echo "=== СВОДКА ПО СЕРВЕРУ ==="
echo "Дата: $(date)"
echo "-------------------"
echo "ЗАНЯТОЕ МЕСТО НА ДИСКЕ:"
df -h / | grep -v Файл
echo "-------------------"
echo "ЗАПУЩЕННЫЕ КОНТЕЙНЕРЫ DOCKER:"
docker ps --format "table {{.Names}}\t{{.Status}}" 2>/dev/null || echo "Docker не запущен"
echo "-------------------"
echo "ОПЕРАТИВНАЯ ПАМЯТЬ:"
free -h | grep "Mem:"
