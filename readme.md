# Centralized Logging Server (Logstash + Elasticsearch + Kibana + Nginx + HTTPS)

Этот проект поднимает централизованный сервер логирования на вашем VPS с помощью Docker Compose.
Сборка включает:

- Elasticsearch — хранение логов
- Logstash — приём логов по HTTPS
- Kibana — интерфейс для просмотра и анализа
- Nginx + Let's Encrypt — защищённый доступ по HTTPS


project-root/
├── docker-compose.yml
├── .env
├── setup.sh
├── nginx/
│   ├── conf.d/
│   │   └── log-proxy.conf
│   └── certbot/
└── logstash/
    └── pipeline/
        └── logstash.conf


## Требования

- VPS с Ubuntu 22.04+
- Домен, указывающий на IP вашего VPS
- Права sudo
- Открыты порты 80 и 443

## Установка

1. Скопируйте проект на сервер

git clone https://github.com/yourname/log-server.git
cd log-server

2. Настройте окружение
- Создайте файл .env и укажите ваш домен:

DOMAIN=logs.example.com
EMAIL=admin@example.com
ELASTIC_PASSWORD=your_secure_password


3. Запустите установку
   
chmod +x setup.sh
./setup.sh

### Что делает setup.sh

- Скрипт полностью автоматизирует установку:
- Устанавливает Docker и Docker Compose (если их нет)
- Настраивает firewall (открывает 80 и 443 порты)
- Подставляет домен из .env
- Запускает docker-compose
- Настраивает nginx и автоматически получает SSL-сертификаты через Let's Encrypt
- Перезапускает сервисы и проверяет доступность Kibana


## Проверка

После завершения установки откройте в браузере:
https://logs.example.com

## Отправка логов на сервер
Пример HTTPS-запроса из вашего приложения:

curl -X POST "https://logs.example.com/logs" \
  -H "Content-Type: application/json" \
  -d '{"service": "frontend", "level": "error", "message": "Something went wrong"}'


## Обновление контейнеров

docker-compose pull
docker-compose up -d

## Удаление

docker-compose down -v

