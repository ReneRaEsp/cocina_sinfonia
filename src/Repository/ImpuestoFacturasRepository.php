<?php

namespace App\Repository;

use App\Entity\ImpuestoFacturas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImpuestoFacturas>
 *
 * @method ImpuestoFacturas|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImpuestoFacturas|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImpuestoFacturas[]    findAll()
 * @method ImpuestoFacturas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImpuestoFacturasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImpuestoFacturas::class);
    }

    public function add(ImpuestoFacturas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImpuestoFacturas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ImpuestoFacturas[] Returns an array of ImpuestoFacturas objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ImpuestoFacturas
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
