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

namespace BaksDev\Auth\Google\Messenger;

use BaksDev\Users\Profile\TypeProfile\Type\Id\Choice\TypeProfileUser;
use BaksDev\Users\Profile\TypeProfile\Type\Id\TypeProfileUid;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use BaksDev\Users\Profile\UserProfile\UseCase\User\NewEdit\UserProfileDTO;
use BaksDev\Users\Profile\UserProfile\UseCase\User\NewEdit\UserProfileHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use DomainException;

#[AsMessageHandler]
final readonly class NewProfileOnGoogleRegistrationDispatcher
{
    public function __construct(private UserProfileHandler $UserProfileHandler) {}

    public function __invoke(NewProfileOnGoogleRegistrationMessage $message): void
    {
        /** Создаем профиль пользователя по умолчанию */
        $userProfileDTO = new UserProfileDTO();
        $userProfileDTO->setType(new TypeProfileUid(TypeProfileUser::class));

        $infoDTO = $userProfileDTO->getInfo();
        $infoDTO->setUrl(uniqid('', false));
        $infoDTO->setUsr($message->getUser());
        $infoDTO->setStatus(new UserProfileStatus(UserProfileStatusActive::class));

        $personalDTO = $userProfileDTO->getPersonal();
        $personalDTO->setUsername($message->getName());

        $userProfile = $this->UserProfileHandler->handle($userProfileDTO);

        if(false === ($userProfile instanceof UserProfile))
        {
            throw new DomainException(sprintf('%s: Ошибка при добавлении профиля пользователя', $userProfile));
        }
    }
}