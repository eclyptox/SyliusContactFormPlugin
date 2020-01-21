<?php

declare(strict_types=1);

namespace MangoSylius\SyliusContactFormPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ContactMessageRepository extends EntityRepository
{
    public function findAllByCustomerId($customerId): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customerId')
            ->setParameter('customerId', $customerId)
            ;
    }
}
