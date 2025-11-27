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

namespace BaksDev\Auth\Google\Repository\AllAccountGoogle;

use BaksDev\Auth\Google\Entity\AccountGoogle;
use BaksDev\Auth\Google\Entity\Active\AccountGoogleActive;
use BaksDev\Auth\Google\Entity\Event\AccountGoogleEvent;
use BaksDev\Auth\Google\Entity\Modify\AccountGoogleModify;
use BaksDev\Auth\Google\Entity\Name\AccountGoogleName;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\Paginator;
use BaksDev\Core\Services\Paginator\PaginatorInterface;

final class AllAccountGoogleRepository implements AllAccountGoogleInterface
{
    private ?SearchDTO $search = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function findAll(): Paginator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('google.id ')
            ->addSelect('google.event ')
            ->from(AccountGoogle::class, 'google');

        $dbal
            ->join(
                'google',
                AccountGoogleEvent::class,
                'google_event',
                'google_event.id = google.event'
            );

        $dbal
            ->addSelect('google_active.active AS active')
            ->leftJoin(
                'google_event',
                AccountGoogleActive::class,
                'google_active',
                'google_active.event = google_event.id'
            );

        $dbal
            ->addSelect('google_name.name AS name')
            ->leftJoin(
                'google_event',
                AccountGoogleName::class,
                'google_name',
                'google_name.event = google_event.id'
            );

        $dbal
            ->addSelect('google_modify.mod_date AS update')
            ->leftJoin(
                'google',
                AccountGoogleModify::class,
                'google_modify',
                'google_modify.event = google.event'
            );


        /* Поиск */
        if(($this->search instanceof SearchDTO) && is_string($this->search->getQuery()))
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('google_name.name')
                ->addSearchLike('google.id')
            ;
        }

        $dbal->orderBy('google_active.active', 'ASC');
        $dbal->addOrderBy('google_modify.mod_date', 'DESC');

        return $this->paginator->fetchAllHydrate($dbal, AllAccountGoogleResult::class);
    }
}