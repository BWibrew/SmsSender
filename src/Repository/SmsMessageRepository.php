<?php

namespace App\Repository;

use App\Entity\SmsMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SmsMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsMessage[]    findAll()
 * @method SmsMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsMessage::class);
    }

    /**
     * @param string $direction
     * @return SmsMessage[] Returns an array of SmsMessage objects
     */
    public function findAllSortedByCreatedAt($direction = 'DESC'): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.created_at', $direction)
            ->getQuery()
            ->getResult()
        ;
    }
}
