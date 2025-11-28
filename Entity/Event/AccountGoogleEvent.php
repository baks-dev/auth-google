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

namespace BaksDev\Auth\Google\Entity\Event;

use BaksDev\Auth\Google\Entity\AccountGoogle;
use BaksDev\Auth\Google\Entity\Active\AccountGoogleActive;
use BaksDev\Auth\Google\Entity\Modify\AccountGoogleModify;
use BaksDev\Auth\Google\Entity\Invariable\AccountGoogleInvariable;
use BaksDev\Auth\Google\Type\Event\AccountGoogleEventUid;
use BaksDev\Users\User\Type\Id\UserUid;
use Doctrine\ORM\Mapping as ORM;
use BaksDev\Core\Entity\EntityEvent;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'account_google_event')]
class AccountGoogleEvent extends EntityEvent
{
    /**
     * Идентификатор События
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: AccountGoogleEventUid::TYPE)]
    private AccountGoogleEventUid $id;

    /**
     * Идентификатор пользователя
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: UserUid::TYPE, nullable: false)]
    private UserUid $account;

    /** Уникальный google-идентификатор */
    #[ORM\OneToOne(targetEntity: AccountGoogleInvariable::class, mappedBy: 'event', cascade: ['all'])]
    private AccountGoogleInvariable $invariable;

    /** Флаг - активен данный пользователь или нет */
    #[ORM\OneToOne(targetEntity: AccountGoogleActive::class, mappedBy: 'event', cascade: ['all'])]
    private AccountGoogleActive $active;

    /** Модификатор */
    #[ORM\OneToOne(targetEntity: AccountGoogleModify::class, mappedBy: 'event', cascade: ['all'])]
    private AccountGoogleModify $modify;

    public function __construct()
    {
        $this->id = new AccountGoogleEventUid();
        $this->modify = new AccountGoogleModify($this);
    }

    /** Идентификатор события */

    public function __clone()
    {
        $this->id = clone new AccountGoogleEventUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): AccountGoogleEventUid
    {
        return $this->id;
    }

    /**
     * Идентификатор AccountGoogle
     */
    public function setMain(UserUid|AccountGoogle $account): void
    {
        $this->account = $account instanceof AccountGoogle ? $account->getId() : $account;
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof AccountGoogleEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof AccountGoogleEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function getAccount(): UserUid
    {
        return $this->account;
    }
}