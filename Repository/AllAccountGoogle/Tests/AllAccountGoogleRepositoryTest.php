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

namespace BaksDev\Auth\Google\Repository\AllAccountGoogle\Tests;

use BaksDev\Auth\Google\Repository\AllAccountGoogle\AllAccountGoogleInterface;
use BaksDev\Auth\Google\Repository\AllAccountGoogle\AllAccountGoogleRepository;
use BaksDev\Auth\Google\Repository\AllAccountGoogle\AllAccountGoogleResult;
use PHPUnit\Framework\Attributes\Group;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[Group('auth-google')]
#[Group('auth-google-repository')]
#[When(env: 'test')]
final class AllAccountGoogleRepositoryTest extends KernelTestCase
{
    public function testFindAll(): void
    {
        $AllAccountGoogleRepository = self::getContainer()->get(AllAccountGoogleInterface::class);

        /** @var AllAccountGoogleRepository $AllAccountGoogleRepositoryTest */
        $result = $AllAccountGoogleRepository
            ->findAll()
            ->getData();

        foreach($result as $allAccountGoogleResult)
        {
            self::assertInstanceOf(AllAccountGoogleResult::class, $allAccountGoogleResult);

            // Вызываем все геттеры
            $ReflectionClass = new ReflectionClass(AllAccountGoogleResult::class);
            $methods = $ReflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach($methods as $method)
            {
                // Методы без аргументов
                if($method->getNumberOfParameters() === 0)
                {
                    // Вызываем метод
                    $data = $method->invoke($allAccountGoogleResult);
                    //dump($data);
                }
            }

            break;
        }
    }
}