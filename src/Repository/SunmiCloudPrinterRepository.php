<?php

namespace App\Repository;

use App\Entity\SunmiCloudPrinter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SunmiCloudPrinter>
 *
 * @method SunmiCloudPrinter|null find($id, $lockMode = null, $lockVersion = null)
 * @method SunmiCloudPrinter|null findOneBy(array $criteria, array $orderBy = null)
 * @method SunmiCloudPrinter[]    findAll()
 * @method SunmiCloudPrinter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SunmiCloudPrinterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SunmiCloudPrinter::class);
    }

    public function add(SunmiCloudPrinter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SunmiCloudPrinter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SunmiCloudPrinter[] Returns an array of SunmiCloudPrinter objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SunmiCloudPrinter
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
