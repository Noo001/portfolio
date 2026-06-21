# Портфолио-лендинг Андрея Ефремцева

Лендинг-презентация разработчика с полноценным PHP backend API, формой обратной связи и AI-интеграцией. Тестовое задание на позицию Backend-ориентированного разработчика.

## 🌐 Демо

- **Живой сайт:** https://noo001.github.io/portfolio/
- **Backend API:** разворачивается отдельно (см. [🚀 Деплой](#-деплой)). После деплоя обновите `frontend/src/environments/environment.prod.ts` актуальным URL.
- **API Docs:** Swagger UI доступна по `/docs.html` на развёрнутом backend

## 📦 Стек технологий

### Backend
| Технология | Назначение |
|------------|------------|
| PHP 8.1+ | Язык backend |
| Slim 4 | Легковесный PHP-фреймворк, роутинг, middleware |
| Composer | Управление зависимостями |
| PHPMailer | Отправка email через SMTP |
| SQLite | Локальная база данных |
| Monolog | Логирование в файл |
| Guzzle | HTTP-клиент для запросов к AI API |
| phpdotenv | Переменные окружения |

### Frontend
| Технология | Назначение |
|------------|------------|
| Angular 22 | Frontend (Standalone компоненты, Signals) |
| TypeScript | Типизация |
| SCSS | Стилизация, адаптив |

### AI
- OpenAI API (GPT-4o-mini) — анализ тональности и генерация ответов
- Stub-реализация — fallback, если AI недоступен или ключ не указан

## 📁 Структура проекта

```text
portfolio/
├── frontend/               # Angular приложение
│   ├── src/
│   │   ├── app/
│   │   ├── components/
│   │   ├── services/       # contact.service.ts, ai.service.ts
│   │   └── environments/   # environment.ts, environment.prod.ts
│   └── ...
├── backend/                # PHP backend на Slim 4
│   ├── public/             # Точка входа (index.php), openapi.json, docs.html
│   ├── src/
│   │   ├── Config/         # ContainerConfig, RoutesConfig
│   │   ├── Controllers/    # ContactController, AiController, HealthController, MetricsController
│   │   ├── Middleware/     # CorsMiddleware, RateLimitMiddleware, ErrorHandler
│   │   ├── Database/       # Database, Migrator
│   │   ├── Services/       # ContactService, EmailService, MetricsService, RateLimitService
│   │   ├── Services/Ai/    # AiServiceInterface, OpenAiService, StubAiService
│   │   ├── Repositories/   # SQLite репозитории для contacts, metrics, rate limits
│   │   ├── Utils/          # Validator
│   │   └── Exceptions/     # ValidationException
│   ├── logs/               # Логи приложения
│   ├── data/               # SQLite база данных
│   ├── composer.json
│   ├── Dockerfile
│   └── .env.example
```

## 🛠️ Установка и запуск

### Требования
- PHP 8.1+ с расширениями: curl, mbstring, openssl, fileinfo, pdo_sqlite
- Composer
- Node.js 26+ и Angular CLI 22 (для frontend)

### 1. Backend

```bash
cd backend

# Копируем настройки окружения
cp .env.example .env

# Устанавливаем зависимости
composer install

# Запускаем встроенный PHP-сервер
composer start
```

Backend будет доступен на http://localhost:8080

### 2. Frontend

```bash
cd frontend
npm i
ng s
```

Frontend откроется на http://localhost:4200, запросы к `/api` проксируются на http://localhost:8080.

### 3. Docker

```bash
cd backend
docker build -t portfolio-backend .
docker run -p 8080:80 --env-file .env portfolio-backend
```

## ⚙️ Переменные окружения

Создайте файл `backend/.env` на основе `backend/.env.example`:

```env
APP_ENV=development
APP_NAME=PortfolioBackend
APP_DEBUG=true

PORT=8080
FRONTEND_URL=http://localhost:4200

# SMTP для отправки писем. Оставьте пустым, чтобы писать сообщения только в лог.
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
SMTP_FROM_EMAIL=your-email@gmail.com
SMTP_FROM_NAME=PortfolioSite
OWNER_EMAIL=owner@example.com

# AI Provider: stub или openai
AI_PROVIDER=stub
AI_API_KEY=sk-...
AI_MODEL=gpt-4o-mini

RATE_LIMIT_MAX=5
RATE_LIMIT_WINDOW=60
```

## 🔌 API Endpoints

### `GET /api/health`
Проверка статуса сервиса.

**Ответ:**
```json
{
  "status": "ok",
  "service": "Portfolio Backend",
  "timestamp": "2026-06-21T10:20:36+00:00",
  "version": "1.0.0"
}
```

### `GET /api/metrics`
Статистика обращений.

**Ответ:**
```json
{
  "metrics": {
    "total_contacts": 10,
    "successful_emails": 8,
    "failed_emails": 2,
    "ai_requests": 5
  },
  "timestamp": "2026-06-21T10:21:24+00:00"
}
```

### `POST /api/contact`
Форма обратной связи.

**Тело запроса:**
```json
{
  "name": "Иван Иванов",
  "phone": "+7 999 123-45-67",
  "email": "ivan@example.com",
  "comment": "Хочу заказать разработку сайта"
}
```

**Ответ:**
```json
{
  "success": true,
  "message": "Письма успешно отправлены",
  "sentiment": {
    "sentiment": "positive",
    "confidence": 0.9
  }
}
```

**Валидация:**
- Все поля обязательны
- Email проверяется на корректность
- Телефон должен содержать от 7 до 15 цифр

**Защита:**
- Rate limiting: не более `RATE_LIMIT_MAX` запросов за `RATE_LIMIT_WINDOW` секунд с одного IP
- CORS настроен для `FRONTEND_URL`
- Входные данные санитизируются (`htmlspecialchars`)

### `POST /api/ai-suggest`
AI-генерация ответа на текст.

**Тело запроса:**
```json
{
  "text": "Хочу заказать сайт"
}
```

**Ответ:**
```json
{
  "suggestion": "Спасибо за ваше интерес! Мы свяжемся с вами в ближайшее время.",
  "fallback": false
}
```

### Документация
Откройте http://localhost:8080/docs.html для просмотра Swagger UI или http://localhost:8080/openapi.json для схемы.

## 🤖 AI-интеграция

Реализованы две AI-функции:

1. **Анализ тональности комментария** (`/api/contact`)
   - Определяет positive / neutral / negative
   - Возвращает confidence score
   - Работает даже если AI недоступен (stub fallback)

2. **Генерация ответа** (`/api/ai-suggest`)
   - Генерирует вежливый ответ на сообщение пользователя
   - Использует OpenAI API при наличии ключа
   - При ошибке API возвращает fallback-ответ

### Промпты

**Анализ тональности:**
```
Ты анализатор тональности. Ответь только JSON: {"sentiment": "positive|neutral|negative", "confidence": 0.0-1.0}.
```

**Генерация ответа:**
```
Ты помощник сайта портфолио. Напиши вежливый краткий ответ на сообщение пользователя на русском языке.
```

### Graceful fallback
Если `AI_PROVIDER=stub` или OpenAI API недоступен:
- Сервис продолжает работать
- Возвращается стандартный ответ
- Ошибка логируется в `logs/app.log`

## 🗄️ База данных

В качестве базы данных используется **SQLite**. Она не требует отдельного сервера и идеально подходит для тестового задания.

Таблицы:
- `contacts` — сохранённые сообщения из формы обратной связи
- `metrics` — статистика обращений
- `rate_limits` — данные для rate limiting
- `migrations` — история применённых миграций

Миграции запускаются автоматически при старте приложения (`Migrator`).

Путь к файлу базы данных настраивается через переменную окружения `DATABASE_PATH`:

```env
DATABASE_PATH=data/portfolio.sqlite
```

### Хранение данных

- **Логи запросов и ошибок** — записываются в `backend/logs/app.log` через Monolog. Каждый HTTP-запрос логируется методом, URI, статусом, временем выполнения и IP.
- **Статистика обращений** — хранится в таблице `metrics` SQLite (`/api/metrics`).
- **Rate limiting** — реализован на уровне middleware с хранением данных в таблице `rate_limits` SQLite.
- **Сообщения формы** — сохраняются в таблице `contacts` SQLite.

## 🏗️ Архитектура

Слоистая архитектура:

```
Controllers → Services → Repositories
     ↑            ↑            ↑
  HTTP       Бизнес-логика   Хранение
```

- **Controllers** — принимают HTTP-запросы, возвращают ответы
- **Services** — содержат бизнес-логику (валидация, email, AI, rate limiting)
- **Repositories** — абстракция над хранением данных (SQLite)
- **Middleware** — CORS, rate limiting, логирование запросов, глобальная обработка ошибок

Паттерны:
- Dependency Injection через PHP-DI
- Repository Pattern
- Middleware Pipeline
- Strategy Pattern для AI-провайдеров

## 📮 Postman коллекция

В папке `backend/` находится файл `Portfolio API.postman_collection.json`. Импортируйте его в Postman для быстрого тестирования API.

## 🧪 Тестирование

```bash
cd backend
composer test
```

Примеры запросов через curl:

```bash
# Health check
curl http://localhost:8080/api/health

# Отправка сообщения
curl -X POST http://localhost:8080/api/contact \
  -H "Content-Type: application/json" \
  -d '{"name":"Иван","phone":"+79991234567","email":"ivan@example.com","comment":"Хочу заказать сайт"}'

# AI-подсказка
curl -X POST http://localhost:8080/api/ai-suggest \
  -H "Content-Type: application/json" \
  -d '{"text":"Хочу заказать сайт"}'

# Метрики
curl http://localhost:8080/api/metrics
```

## 🚀 Деплой

### Render (рекомендуется)

Backend разворачивается как Docker-сервис из папки `backend`:

1. Откройте [Render Dashboard](https://dashboard.render.com)
2. Создайте новый Web Service (или обновите существующий `portfolio-backend`):
   - New → Web Service → подключите GitHub-репозиторий
   - **Name:** `portfolio-backend`
   - **Runtime:** Docker
   - **Root Directory:** `backend`
3. Добавьте переменные окружения из `backend/.env.example`:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `FRONTEND_URL=https://noo001.github.io`
   - `DATABASE_PATH=/var/www/html/data/portfolio.sqlite`
4. Заполните секретные переменные:
   - `SMTP_HOST`, `SMTP_USERNAME`, `SMTP_PASSWORD`, `SMTP_FROM_EMAIL`, `OWNER_EMAIL` — для отправки писем
   - `AI_PROVIDER=openai`, `AI_API_KEY` — для реального AI (или оставьте `stub`)
5. Нажмите **Deploy**

> ⚠️ Значения переменных окружения с пробелами должны быть взяты в двойные кавычки, иначе `phpdotenv` не сможет их распарсить.

Альтернативно, используйте файл [`render.yaml`](./render.yaml) в корне репозитория для деплоя через Render Blueprint.

### Docker

```bash
cd backend
docker build -t portfolio-backend .
docker run -p 8080:80 --env-file .env portfolio-backend
```

После деплоя обновите `frontend/src/environments/environment.prod.ts`, указав URL вашего backend:

```typescript
export const environment = {
  production: true,
  apiBaseUrl: 'https://your-backend-url.com/api'
};
```

Затем пересоберите frontend:

```bash
cd frontend
npm run build -- --configuration production
```

## 🤖 Что сделано с помощью ИИ

- Генерация структуры Slim-приложения
- Шаблоны писем для PHPMailer
- Промпты для OpenAI
- Первичная реализация middleware и репозиториев

## ✍️ Что исправлено вручную

- Адаптация архитектуры под требования backend-тестового
- Настройка rate limiting с SQLite
- Реализация graceful fallback для AI
- Интеграция PHPMailer и логирования через Monolog
- Настройка CORS и валидации
- Swagger/OpenAPI документация
- Dockerfile и инструкции по деплою
- Подключение SQLite базы данных
- Обновление Angular до 22
- Миграция на новую Angular build system (@angular/build)
- Исправление Sass @import на @use
- Логирование всех HTTP-запросов

## 📬 Контакты

- **Email:** noo_@bk.ru
- **Telegram:** @pompadurik
- **GitHub:** [github.com/Noo001](https://github.com/Noo001)

---

**Выполнено для тестового задания:** Backend-ориентированная версия портфолио с PHP API и AI-интеграцией.
