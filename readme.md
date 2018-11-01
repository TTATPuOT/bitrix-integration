# Класс для быстрой интеграции форм с CRM Битрикс 24

[![Latest Stable Version](https://poser.pugx.org/neverov12/bitrix24-simple-integration/v/stable)](https://packagist.org/packages/neverov12/bitrix24-simple-integration)
[![Total Downloads](https://poser.pugx.org/neverov12/bitrix24-simple-integration/downloads)](https://packagist.org/packages/neverov12/bitrix24-simple-integration)

Написан на базе Guzzle. Прост в понимании, легко дописать.

Возможности:
* Создание контакта
* Создание сделки
* Создание и того с использованием одного метода
* Обновление этапа сделки

## Установка
```
composer install neverov12/bitrix24-form-integration
```
>Для получения OAuth токена можно вспользоваться [следующей инструкцией](https://dev.1c-bitrix.ru/learning/course/index.php?COURSE_ID=99&LESSON_ID=8579). В дальнейшем класс научится хранить OAuth токен самостоятельно.
## Использование
### Создание экземпляра класса
```php
$bitrix = new('ВАШ_OAUTH_ТОКЕН', 'ВАШ_РАБОЧИЙ_ДОМЕН.bitrix24.ru');
```
### Базовое использование
```php
$new_deal_id = add('Антон Неверов', '79999999999', 'neverov12@gmail.com', 100, 'Название сделки'); //Где 100 - стоимость сделки, можно указать 0
```
### Создание только контакта
```php
$new_contact_id = createContact('Антон Неверов', '79999999999', 'neverov12@gmail.com');
```
### Создание только сделки
```php
$new_deal_id = createDeal($contact_id, $price, 'Название сделки');
```
### Обновление сделки
```php
$new_deal_id = update($deal_id, $stage_id); //Где $stage_id - номер этапа. Можно узнать через HTML при создании сделки по поиску поля с name="STAGE_ID"
```