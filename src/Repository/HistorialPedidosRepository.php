<?php

namespace App\Repository;

use App\Entity\HistorialPedidos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistorialPedidos>
 *
 * @method HistorialPedidos|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistorialPedidos|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistorialPedidos[]    findAll()
 * @method HistorialPedidos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistorialPedidosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistorialPedidos::class);
    }

    public function add(HistorialPedidos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(HistorialPedidos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return HistorialPedidos[] Returns an array of HistorialPedidos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistorialPedidos
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
