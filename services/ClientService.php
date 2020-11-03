<?php

namespace services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Exception;

class ClientService
{
    /* @var Client */
    private $clientAPI;

    /* @var string */
    public $token;

    /* @var LogService */
    private $logger;

    /* @var string (JSON) */
    public $data;

    /**
     * ClientService constructor.
     *
     * @param string $baseURL - базовый URL API
     */
    public function __construct(string $baseURL)
    {
        $this->logger = new LogService();
        try {
            $this->clientAPI = new Client([
                'base_uri' => $baseURL,
                'timeout' => 2.0,
            ]);
        } catch (Exception $e) {
            $this->logger->error($this->psrMessage($e->getRequest()), __METHOD__);
        }
    }

    /**
     * Получает данные в свойство data. Возвращает true, если успешно
     *
     * @param string $username
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUser(string $username): bool
    {
        if ($this->clientAPI) {
            $token = $this->token;
            $params = compact('token');

            try {
                $response = $this->clientAPI->request('GET', 'get-user/' . $username, [
                    'query' => $params,
                ]);
            } catch (RequestException $e) {
                $mess = $this->psrMessage($e->getRequest());
                $this->logger->error($mess, __METHOD__);
                if ($e->hasResponse()) {
                    $this->logger->error($this->psrMessage($e->getResponse()), __METHOD__);
                }
                return false;
            }

            $json = $response->getBody();
            $array = json_decode($json, true);
            $status = $array['status'] ?? 'err';
            if ($status == 'OK') {
                $this->data = $array;
                return true;
            }
            $this->logger->error('Ошибка: ' . $status, __METHOD__);
            return false;

        }
        $this->logger->error('Объект clientAPI не существует', __METHOD__);
        return false;
    }

    /**
     * Обновляет данные о пользователе. Возвращает true, если успешно
     *
     * @param string $username
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateUser($id, string $JSONdata): bool
    {
        if ($this->clientAPI) {
            try {
                $response = $this->clientAPI->request('POST', 'user/' . $id . '/update?token=' . $this->token, [
                    'body' => $JSONdata,
                ]);
            } catch (RequestException $e) {
                $mess = $this->psrMessage($e->getRequest());
                $this->logger->error($mess, __METHOD__);
                if ($e->hasResponse()) {
                    $this->logger->error($this->psrMessage($e->getResponse()), __METHOD__);
                }
                return false;
            }
            $json = $response->getBody();
            $array = json_decode($json, true);
            $status = $array['status'] ?? 'ошибка API';
            if ($status == 'OK') {
                $this->data = $JSONdata; // возможно, нужно вызвать getUser для проверки обновления...
                return true;
            }
            $this->logger->error('Ошибка: ' . $status, __METHOD__);
            return false;
        }
        $this->logger->error('Объект clientAPI не существует', __METHOD__);
        return false;
    }

    /**
     * Аутентификация. Возвращает true, если успешно и помещает токен в свойство token
     *
     * @param string $login
     * @param string $pass
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function auth(string $login, string $pass): bool
    {
        if ($this->clientAPI) {
            $params = compact('login', 'pass');

            try {
                $response = $this->clientAPI->request('GET', 'auth', [
                    'query' => $params,
                ]);
            } catch (RequestException $e) {
                $this->logger->error($this->psrMessage($e->getRequest()), __METHOD__);
                if ($e->hasResponse()) {
                    $this->logger->error($this->psrMessage($e->getResponse()), __METHOD__);
                }
                return false;
            }

            $json = $response->getBody();
            $array = json_decode($json, true);
            $status = $array['status'] ?? 'err';
            if ($status == 'OK') {
                $this->token = $array['token'];
                return true;
            }
            $this->token = null;
            $this->logger->error('Ошибка: ' . $status, __METHOD__);
            return false;

        }
        $this->logger->error('Объект clientAPI не существует', __METHOD__);
        return false;
    }

    /**
     * Вспомогательная функция. Возвращает строку сообщения getRequest() или getResponse()
     *
     * @param $mess
     * @return string
     */
    private function psrMessage($mess): string
    {
        return Psr7\Message::toString($mess);
    }
}
