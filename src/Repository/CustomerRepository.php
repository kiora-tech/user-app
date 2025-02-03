<?php

namespace App\Repository;

use App\Data\CustomerSearchData;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function search(CustomerSearchData $search): Query
    {
        $query = $this->createQueryBuilder('c');

        $allowedSortFields = ['name', 'siret', 'contactName'];

        if (!in_array($search->sort, $allowedSortFields)) {
            $search->sort = 'name';
        }

        $order = strtoupper($search->order) === 'DESC' ? 'DESC' : 'ASC';

        if (!empty($search->name)) {
            $query = $query
                ->andWhere('c.name LIKE :name')
                ->setParameter('name', '%' . $search->name . '%');
        }

        if ($search->status) {
            $query = $query
                ->andWhere('c.status = :status')
                ->setParameter('status', $search->status);
        }

        if (!empty($search->contactName)) {
            $query = $query
                ->join('c.contacts', 'co')
                ->andWhere('(co.firstName LIKE :contactName OR co.lastName LIKE :contactName)')
                ->setParameter('contactName', '%' . $search->contactName . '%');
        }

        return $query
            ->orderBy('c.' . $search->sort, $order)
            ->getQuery();
    }
}
