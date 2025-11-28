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

namespace BaksDev\Auth\Google\Controller\Admin;

use BaksDev\Auth\Google\Entity\AccountGoogle;
use BaksDev\Auth\Google\Entity\Event\AccountGoogleEvent;
use BaksDev\Auth\Google\UseCase\Admin\NewEdit\AccountGoogleDTO;
use BaksDev\Auth\Google\UseCase\Admin\NewEdit\AccountGoogleHandler;
use BaksDev\Auth\Google\UseCase\Admin\NewEdit\AccountGoogleForm;
use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
#[RoleSecurity('ROLE_ACCOUNT_GOOGLE_EDIT')]
final class EditController extends AbstractController
{
    #[Route('/admin/account/google/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] AccountGoogleEvent $AccountGoogleEvent,
        AccountGoogleHandler $AccountGoogleHandler,
    ): Response
    {
        $AccountGoogleDTO = new AccountGoogleDTO();
        $AccountGoogleEvent->getDto($AccountGoogleDTO);

        // Форма
        $form = $this
            ->createForm(
                AccountGoogleForm::class,
                $AccountGoogleDTO,
                ['action' => $this->generateUrl(
                    'auth-google:admin.newedit.edit',
                    ['id' => $AccountGoogleDTO->getEvent()]
                )]
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('account_google_newedit_form'))
        {
            $handle = $AccountGoogleHandler->handle($AccountGoogleDTO);

            $this->addFlash
            (
                'page.edit',
                $handle instanceof AccountGoogle ? 'success.edit' : 'danger.edit',
                'auth-google.admin',
                $handle
            );

            return $handle instanceof AccountGoogle ? $this->redirectToRoute('auth-google:'.IndexController::PATH) : $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}