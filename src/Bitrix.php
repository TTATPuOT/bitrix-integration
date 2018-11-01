<?php
/**
 * Created by PhpStorm.
 * Project: bitrix-integration
 * User: Anton Neverov <neverov12@gmail.com>
 * Date: 01.11.2018
 * Time: 12:11
 */

namespace Bitrix;


/**
 * Class Bitrix
 * @package Bitrix
 */
class Bitrix
{

    /**
     * @var string
     * OAuth пользовательский токен
     */
    private $token;
    /**
     * @var string
     * Домен в формате company.bitrix.ru
     */
    private $endpoint;
    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    /**
     * Bitrix constructor.
     * @param $token
     * @param $domain
     */
    public function __construct($token, $domain)
    {
        $this->token = $token;
        $this->endpoint = "https://$domain/rest/";

        $this->guzzle = new \GuzzleHttp\Client([
            'base_uri' => $this->endpoint
        ]);
    }

    /**
     * Создание контакта и сделки с его участием
     *
     * @param $name
     * Имя нового контакта
     *
     * @param $phone
     * Телефон контакта
     *
     * @param $email
     * Почта контакта
     *
     * @param $price
     * Стоимость сделки (можно указать 0)
     *
     * @param $title
     * Название сделки
     *
     * @return string
     * Номер новой сделки из ответа
     */
    public function add($name, $phone, $email, $price, $title) {
        $contact_id = $this->createContact($name, $phone, $email); //Создаём контакт
        return $this->createDeal($contact_id, $price, $title); //Создаём сделку и возращаем её номер
    }

    /**
     * @param $deal_id
     * Номер сделки
     *
     * @param $stage_id
     * Номер нового этапа сделки, можно узнать через поиск по HTML при создании сделки по фразе "STAGE_ID"
     */
    public function update($deal_id, $stage_id) {
        $params = [
            'id' => $deal_id,
            'fields' => [
                'STAGE_ID' => $stage_id
            ],
            'params' => [
                'REGISTER_SONET_EVENT' => 'N'
            ]
        ];
        $this->request($params, 'crm.deal.update');
    }

    /**
     * @param $contact_id
     * Номер контакта для создания сделки
     *
     * @param $price
     * Стоимость сделки
     *
     * @param $title
     * Название сделки
     *
     * @return string
     * Номер новой сделки
     */
    public function createDeal($contact_id, $price, $title) {
        $params = [
            'fields' => [
                'TITLE' => $title,
                'CONTACT_ID' => $contact_id,
                'OPPORTUNITY' => $price,
                'CURRENCY_ID' => 'RUB'
            ],
            'params' => [
                'REGISTER_SONET_EVENT' => 'N'
            ]
        ];

        $result = $this->request($params, 'crm.deal.add');
        return $result['result'];
    }

    /**
     * @param $name
     * Имя контакта
     *
     * @param $phone
     * Телефон контакта
     *
     * @param $email
     * Почта контакта
     *
     * @return string
     * Номер нового контакта
     */
    public function createContact($name, $phone, $email) {
        $params = [
            'fields' => [
                'NAME' => $name,
                'TYPE_ID' => 'CLIENT',
                'PHONE' => [
                    [
                        'VALUE' => $phone,
                        'VALUE_TYPE' => 'WORK'
                    ]
                ],
                'EMAIL' => [
                    [
                        'VALUE' => $email,
                        'VALUE_TYPE' => 'WORK'
                    ]
                ]
            ],
            'params' => [
                'REGISTER_SONET_EVENT' => 'N'
            ]
        ];

        $result = $this->request($params, 'crm.contact.add');
        return $result['result'];
    }

    /**
     * @param $params
     * Параметры в виде массива, может быть многомерным. Примеры масивов смотри выше.
     *
     * @param $method
     * Метод в формате строки, например "crm.contact.add"
     *
     * @return array
     * Ответ в виде массива
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($params, $method){

        $params = array_merge($params, ['auth' => $this->token]);

        $request = $this->guzzle->request('GET', $method, [
            'query' => $params
        ]);

        return json_decode($request->getBody(), true);

    }

}