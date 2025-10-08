#!/bin/bash
set -e

# === Настройки ===
DOMAIN=$1
WORKDIR=~/logging-stack
EMAIL="admin@$DOMAIN"  # можно заменить на свой email

if [ -z "$DOMAIN" ]; then
  echo "❌ Ошибка: укажи домен, например:"
  echo "   sudo bash setup-logging.sh logs.myserver.ru"
  exit 1
fi

echo "🚀 Установка центра логирования для домена: $DOMAIN"
sleep 1

# === Обновление системы ===
echo "📦 Обновление пакетов..."
apt update -y && apt upgrade -y

# === Установка Docker и Compose ===
echo "🐳 Установка Docker и Docker Compose..."
apt install -y docker.io docker-compose certbot ufw

systemctl enable --now docker

# === Проверка проекта ===
if [ ! -f "$WORKDIR/docker-compose.yml" ]; then
  echo "❌ Не найден docker-compose.yml в $WORKDIR"
  echo "   Скопируй файлы проекта (docker-compose.yml, loki-config.yml, nginx.conf) в $WORKDIR"
  exit 1
fi

cd "$WORKDIR"

# === Проверка nginx.conf ===
if ! grep -q "server_name" nginx.conf; then
  echo "⚙️  Автоматически добавляем server_name в nginx.conf..."
  sed -i "0,/server_name/s/server_name.*/server_name $DOMAIN;/" nginx.conf || true
else
  sed -i "s/server_name .*/server_name $DOMAIN;/" nginx.conf
fi

# === Получение SSL-сертификатов ===
echo "🔐 Получение SSL-сертификата для $DOMAIN ..."
certbot certonly --standalone -d "$DOMAIN" --agree-tos -m "$EMAIL" --non-interactive --no-eff-email || {
  echo "⚠️ Не удалось получить сертификат. Проверь DNS-запись домена."
  exit 1
}

# === Копирование сертификатов в проект ===
mkdir -p certs
cp /etc/letsencrypt/live/$DOMAIN/fullchain.pem ./certs/
cp /etc/letsencrypt/live/$DOMAIN/privkey.pem ./certs/

# === Разрешаем порты ===
echo "🌐 Открываем порты 80 и 443..."
ufw allow 80,443/tcp || true


# === Создание директорий ===
echo "📂 Создание директорий..."
mkdir -p ./data/loki ./data/grafana
mkdir -p ./data/loki/index ./data/loki/cache ./data/loki/chunks ./data/loki/compactor ./data/loki/wal
# Для Grafana
sudo chown -R 472:472 ./data/grafana
# Для Loki
sudo chown -R 10001:10001 ./data/loki


# === Запуск docker-compose ===
echo "🐋 Запуск Loki + Grafana + Nginx в Docker..."
docker-compose up -d

# === Автоматическое обновление сертификатов ===
echo "🕓 Настройка автообновления SSL..."
(crontab -l 2>/dev/null; echo "0 3 * * * certbot renew --quiet && docker compose -f $WORKDIR/docker-compose.yml restart nginx") | crontab -

# === Проверка статуса ===
sleep 5
docker-compose ps

# === Финал ===
echo ""
echo "✅ Установка завершена успешно!"
echo "🌐 Grafana: https://$DOMAIN:3000 (логин: admin / пароль: admin)"
echo "📡 Loki API: https://$DOMAIN/loki/api/v1/push"
echo ""
echo "🎉 Всё работает внутри Docker — nginx на хосте не нужен."
echo "Сертификаты будут обновляться автоматически каждые 60 дней."
