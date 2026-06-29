# Library

The REST API for the Library of Books mobile application (test)

## Stack

- **PHP 8.2**
- **MySQL 8**
- **JWT** (firebase/php-jwt)
- **Composer** + PSR-4
- **REST API** + JSON
- **Php-cs-fixer**

---

## Endpoints

| Метод | URL | Описание |
|-------|-----|----------------|
| `POST` | `/auth/register` | Регистрация |
| `POST` | `/auth/login` | Логин + токен |
| `GET` | `/users` | Список пользователей |
| `POST` | `/users/{id}/access` | Выдать доступ |
| `GET` | `/users/{id}/books` | Книги пользователя |
| `GET` | `/books` | Мои книги |
| `POST` | `/books` | Создать книгу |
| `GET` | `/books/{id}` | Открыть книгу |
| `PUT` | `/books/{id}` | Обновить книгу |
| `DELETE` | `/books/{id}` | Удалить книгу |
| `POST` | `/books/{id}/restore` | Восстановить книгу |
| `GET` | `/external/books` | Поиск книг |
| `POST` | `/external/books/{id}/save` | Сохранить книгу |
