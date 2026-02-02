<img src="logo.png" alt="Slipnote Logo">

# Slipnote

[![PHP 8.5](https://img.shields.io/badge/php-8.5-%23777BB4?style=for-the-badge&logo=php&logoColor=black">)](https://www.php.net/releases/8.5/ru.php)
[![Symfony 8.0](https://img.shields.io/badge/symfony-8.0-%23000000.svg?style=for-the-badge&logo=symfony&logoColor=white)](https://symfony.com/releases/8.0)
[![PostgreSQL 18.1](https://img.shields.io/badge/PostgreSQL-18.1-396a94?style=for-the-badge&logo=postgresql&logoColor=blue)](https://www.postgresql.org/docs/release/18.1)
[![Codeception 5.3](https://img.shields.io/badge/codeception-5.3-%2344C242?style=for-the-badge&logo=codeception&logoColor=white)](https://codeception.com/)

**Slipnote** — это небольшой PET-проект для работы с текстовыми заметками пользователей.

## ✨ Возможности

- 👤 Создание / редактирование / удаление пользователей
- 📄 Создание / редактирование / удаление заметок
- 🔍 Поиск по названию и содержимому
- 🔐 JWT-аутентификация
- 📊 API архитектура с полной документацией OpenAPI

## 🚀 Быстрый старт

- Клонировать проект

```bash
git clone https://github.com/armhaina/slipnote.git
```

- Перейти в проект

```bash
cd slipnote
```

- Скопировать файл `.env.example` в `.env`

```bash
cp .env.example .env
```

- Запустить проект

```bash
make up
```

- Сгенерировать [JWT-ключи][2]

```bash
docker compose exec -it application php bin/console lexik:jwt:generate-keypair
```

## 📚 Документация

- API документация доступна после запуска проекта по адресу: http://localhost/api/doc

## 🧪 Тестирование

- Инициализация тестовой базы

```bash
make test-init
```

- Запуск тестов

```bash
make test-run
```

## 🛠️ Доп. настройки

### 🎣️ Настроить lefthook

**Lefthook** — это инструмент для управления Git-хуками.

- Установить [Node.js][1] 

- В корне проекта запустить команду, которая установит пакет `lefthook`

```bash
npm install lefthook --save-dev
```

- В корне проекта запустить команду, которая настроит `git hooks` из файла `lefthook.yml`

```bash
node_modules/.bin/lefthook install
```

- Залить изменения в ваш Git репозиторий

## 📄 Лицензия

[![Лицензия MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

Распространяется под лицензией MIT.

**Кратко о лицензии MIT:**
- ✅ Можно свободно использовать, копировать, изменять, распространять
- ✅ Можно использовать в коммерческих проектах
- ✅ Не нужно открывать исходный код производных работ
- 📋 Единственное условие — сохранить уведомление об авторских правах и лицензии

Полный текст лицензии доступен в файле [LICENSE](LICENSE)

[1]: https://nodejs.org/en/download
[2]: https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html#generate-the-ssl-keys
