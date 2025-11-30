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

namespace BaksDev\Auth\Google\Type\Identifier;

use InvalidArgumentException;

/** Идентификатор аккаунта Google */
final readonly class AccountGoogleIdentifier
{
    public const string TYPE = 'account_google_identifier';

    private mixed $attr;

    private mixed $option;

    private mixed $property;

    private mixed $characteristic;

    public function __construct(
        private string|null $value = null,
        mixed $attr = null,
        mixed $option = null,
        mixed $property = null,
        mixed $characteristic = null,
    )
    {
        $this->attr = $attr;
        $this->option = $option;
        $this->property = $property;
        $this->characteristic = $characteristic;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isEqual(mixed $other): bool
    {
        $other = new self((string) $other);
        return $this->getValue() === $other->getValue();
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getAttr(): mixed
    {
        return $this->attr;
    }

    public function getOption(): mixed
    {
        return $this->option;
    }


    public function getProperty(): mixed
    {
        return $this->property;
    }

    public function getCharacteristic(): mixed
    {
        return $this->characteristic;
    }
}