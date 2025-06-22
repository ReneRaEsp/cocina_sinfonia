<?php

namespace App\Repository;

use App\Entity\Tickettofactura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tickettofactura>
 *
 * @method Tickettofactura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tickettofactura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tickettofactura[]    findAll()
 * @method Tickettofactura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TickettofacturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tickettofactura::class);
    }

    public function add(Tickettofactura $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tickettofactura $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findInvoiceDate()
    {

        $sql = "SELECT * FROM tickettofactura WHERE YEAR(fecha) = YEAR(CURDATE())";
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
            $invoiceDates = $stmt->fetchAllAssociative();

            return $invoiceDates;
    }

//    /**
//     * @return Tickettofactura[] Returns an array of Tickettofactura objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tickettofactura
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
