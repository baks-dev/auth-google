<?php
/*
 * Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Auth\Google\Api\Google;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Autoconfigure(public: true)]
final readonly class GetAccessTokenRequest
{
    const string REDIRECT = 'auth-google:public.auth';

    public function __construct(
        #[Autowire(env: 'GOOGLE_CLIENT_ID')] private string $clientId,
        #[Autowire(env: 'GOOGLE_CLIENT_SECRET')] private string $clientSecret,
        #[Autowire(env: 'HOST')] private string $host,
        private UrlGeneratorInterface $UrlGenerator
    ) {}

    /**
     *  После получения кода авторизации обмениваем его на access токен. В запросе отправляем:
     * client_id    ID клиента, полученный со страницы Cloud Console Clients.
     * client_secret    Клиентский секрет, полученный в Cloud Console Clients. (В документации написано, что параметр
     *                  необязательный, но по какой-то причине на сегодняшний день без него запросы возвращают 400
     *                  ошибку)
     * code    Код авторизации, полученный при первом запросе.
     * grant_type    В данном запросе всегда должно быть authorization_code.
     * redirect_uri    Один из URI, добавленных в проект из Cloud Console Clientsдля данного client_id.
     *
     * @see https://developers.google.com/identity/protocols/oauth2/web-server
     */
    public function getTokens(string $code): string|false
    {
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['Host'] = 'oauth2.googleapis.com';

        $request = new RetryableHttpClient(
            HttpClient::create(['headers' => $headers])
                ->withOptions([
                    'base_uri' => 'https://oauth2.googleapis.com',
                    'verify_host' => false,
                ])
        );

        $data = [
            'code' => $code,
            'client_id' => $this->clientId,
            'redirect_uri' => 'https://'.$this->host.$this->UrlGenerator->generate(self::REDIRECT),
            'grant_type' => 'authorization_code',
            'client_secret' => $this->clientSecret
        ];

        $response = $request->request('POST', '/token', ["body" => $data]);

        if($response->getStatusCode() !== 200)
        {
            return false;
        }

        return $response->toArray()['access_token'];
    }
}