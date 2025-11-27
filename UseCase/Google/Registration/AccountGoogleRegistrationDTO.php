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

namespace BaksDev\Auth\Google\UseCase\Google\Registration;

use BaksDev\Auth\Google\Entity\Event\AccountGoogleEventInterface;
use BaksDev\Auth\Google\UseCase\Google\Registration\Active\AccountGoogleRegistrationActiveDTO;
use BaksDev\Auth\Google\Type\Event\AccountGoogleEventUid;
use BaksDev\Auth\Google\UseCase\Google\Registration\Name\AccountGoogleRegistrationNameDTO;
use BaksDev\Auth\Google\UseCase\Google\Registration\Sub\AccountGoogleRegistrationSubDTO;
use Symfony\Component\Validator\Constraints as Assert;
use BaksDev\Auth\Google\Entity\Event\AccountGoogleEvent;

/** @see AccountGoogleEvent */
final class AccountGoogleRegistrationDTO implements AccountGoogleEventInterface
{
    /**
     * Идентификатор события
     */
    #[Assert\Uuid]
    private ?AccountGoogleEventUid $id = null;

    #[Assert\Valid]
    private AccountGoogleRegistrationActiveDTO $active;

    #[Assert\Valid]
    private AccountGoogleRegistrationNameDTO $name;

    #[Assert\Valid]
    private AccountGoogleRegistrationSubDTO $sub;

    public function __construct()
    {
        $this->active = new AccountGoogleRegistrationActiveDTO();
        $this->name = new AccountGoogleRegistrationNameDTO();
        $this->sub = new AccountGoogleRegistrationSubDTO();
    }

    /**
     * Идентификатор события
     */
    public function getEvent(): ?AccountGoogleEventUid
    {
        return $this->id;
    }

    public function setId(AccountGoogleEventUid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getActive(): AccountGoogleRegistrationActiveDTO
    {
        return $this->active;
    }

    public function getName(): AccountGoogleRegistrationNameDTO
    {
        return $this->name;
    }

    public function getSub(): AccountGoogleRegistrationSubDTO
    {
        return $this->sub;
    }

    public function setActive(bool $active): self
    {
        $this->active->setActive($active);
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name->setName($name);
        return $this;
    }

    public function setSub(string $sub): self
    {
        $this->sub->setValue($sub);
        return $this;
    }
}