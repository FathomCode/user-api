# USER REST API


Реализовать методы REST API для работы с пользователями: 
 - Создание пользователя;
 - Обновление информации пользователя;
 - Удаление пользователя;
 - Авторизация пользователя;
 - Получить информацию о пользователе.
<br />

## METHODS
**CREATE USER:**
http://user-api.loc/api/user/create/

**Input POST JSON(example):**

    {
    "username": "username3",
    "email": "email3@email.ru",
    "password": "password",
    "first_name": "first_name",
    "last_name": "last_name"
    }

**Output: Success or error message**


<br />

**GET USER:**
http://user-api.loc/api/user/get/2

**Input GET id parameter in url:**

**Output: User data or error message**

    {
    "username": "username",
    "email": "email@email.ru",
    "first_name": "first_name2",
    "last_name": "last_name2"
    }


<br />

**AUTH USER:**
http://user-api.loc/api/user/auth/

**Input POST JSON(example):**

    {
    "username": "username",
    "password": "password"
    }

**Output: Data + API Auth Key or error message:**
    
    {
    "messages": "Login success",
    "user_id": "1",
    "username": "username",
    "api_key": "70494c96140f7fad5d655eca7de34321783b",
    "expired_at": "2025-03-02 18:58:53"
    }

<br />

**CHANGE USER DATA:**
http://user-api.loc/api/user/change/

**Input PUT JSON(example):**

    {
    "api_key": "unique 36 symbols",
    "username": "username",
    "first_name": "new first_name",
    "last_name": "new last_name"
    }

**Output: Data + API Auth Key or error message:**
    
    {
    "messages": "Data changing success",
    "user_id": "1",
    "username": "username",
    "first_name": "new first_name",
    "last_name": "new last_name"
    }

<br />

**DELETE USER:**
http://user-api.loc/api/user/delete/

**Input DELETE JSON(example):**

    {
    "api_key": "unique 36 symbols",
    "username": "username"
    }

**Output: success or error message:**