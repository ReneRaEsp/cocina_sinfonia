<?php

namespace App\Repository;

use App\Entity\TipoComida;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipoComida>
 *
 * @method TipoComida|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoComida|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoComida[]    findAll()
 * @method TipoComida[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoComidaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipoComida::class);
    }

    public function add(TipoComida $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TipoComida $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TipoComida[] Returns an array of TipoComida objects
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

//    public function findOneBySomeField($value): ?TipoComida
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function getPorIds($id_min,$id_max){
    
    $query = $this->createQueryBuilder('e')
        ->where('e.id BETWEEN :id_min AND :id_max')
        ->setParameter('id_min', $id_min)
        ->setParameter('id_max', $id_max)
        ->getQuery();
    
    return $query->getResult();

}
}
