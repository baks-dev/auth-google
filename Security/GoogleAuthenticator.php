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

namespace BaksDev\Auth\Google\Security;

use BaksDev\Auth\Email\Messenger\CreateAccount\CreateAccountMessage;
use BaksDev\Auth\Email\Type\Email\AccountEmail;
use BaksDev\Auth\Google\Api\Google\GetAccessTokenRequest;
use BaksDev\Auth\Google\Api\Google\GetProfileInfoRequest;
use BaksDev\Auth\Google\Messenger\NewProfileOnGoogleRegistrationMessage;
use BaksDev\Auth\Google\Repository\GoogleAccountUserByGoogleIdentifier\GoogleAccountUserByGoogleIdentifierInterface;
use BaksDev\Auth\Google\Repository\GoogleAccountUserByGoogleIdentifier\GoogleAccountUserByGoogleIdentifierResult;
use BaksDev\Auth\Google\UseCase\Google\Registration\AccountGoogleRegistrationDTO;
use BaksDev\Auth\Google\UseCase\Google\Registration\AccountGoogleRegistrationHandler;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Users\User\Entity\User;
use BaksDev\Users\User\Repository\GetUserById\GetUserByIdInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class GoogleAuthenticator extends AbstractAuthenticator
{
    private const string LOGIN_ROUTE = 'auth-google:public.auth';

    private const string SUCCESS_REDIRECT = 'core:public.homepage';

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly GetAccessTokenRequest $GetAccessTokenRequest,
        private readonly GetProfileInfoRequest $GetProfileIdRequest,
        private readonly GoogleAccountUserByGoogleIdentifierInterface $GoogleAccountUserBySubRepository,
        private readonly GetUserByIdInterface $userById,
        private readonly AccountGoogleRegistrationHandler $AccountGoogleRegistrationHandler,
        private readonly MessageDispatchInterface $MessageDispatch,
    ) {}

    public function supports(Request $request): ?bool
    {
        /** Проверяем, что перенаправление идет на нужный эндпоинт и что пришел код авторизации */
        return $this->getAuthFormUrl() === $request->getPathInfo() && false === empty($request->get('code'));
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->GetAccessTokenRequest->getTokens($request->get('code'));

        /** Если access токен не был получен */
        if(false === $accessToken)
        {
            return new SelfValidatingPassport(
                new UserBadge('error', function() {
                    return null;
                }),
            );
        }

        $info = $this->GetProfileIdRequest->getInfo($accessToken);

        /** Если id пользователя Google не был возвращен */
        if(false === $info)
        {
            return new SelfValidatingPassport(
                new UserBadge('error', function() {
                    return null;
                }),
            );
        }

        /** Получаем паспорт */
        return new SelfValidatingPassport(
            new UserBadge($request->get('code'), function() use ($info) {

                $googleAccount = $this->GoogleAccountUserBySubRepository->findByIdentifier($info->getIdentifier());

                /**
                 * Если такого пользователя еще нет - нужно его создать и сохранить
                 */
                if(false === ($googleAccount instanceof GoogleAccountUserByGoogleIdentifierResult))
                {
                    $accountGoogleRegistrationDTO = new AccountGoogleRegistrationDTO()
                        ->setActive(true)
                        ->setInvariable($info->getIdentifier());

                    $user = $this->AccountGoogleRegistrationHandler->handle($accountGoogleRegistrationDTO);

                    if(false === ($user instanceof User))
                    {
                        return false;
                    }

                    $this->MessageDispatch->dispatch(
                        message: new NewProfileOnGoogleRegistrationMessage(
                            $user->getId(),
                            $info->getName(),
                        ),
                    );

                    /** Отправляем сообщение на создание нового email-аккаунта */
                    $this->MessageDispatch->dispatch(
                        message: new CreateAccountMessage(
                            $user->getId(),
                            new AccountEmail($info->getEmail())),
                        transport: 'auth-email',
                    );

                    return $user;
                }


                /** Если пользователь найден, но не активен - значит он забанен, и мы его не авторизуем */
                if(false === $googleAccount->isActive())
                {
                    return false;
                }

                return $this->userById->get($googleAccount->getId());
            }),
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /* Редирект на главную страницу после успешной авторизации */
        return new RedirectResponse($this->urlGenerator->generate(self::SUCCESS_REDIRECT));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->getAuthFormUrl());
    }

    protected function getAuthFormUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}