<?php

namespace App\Repository;

use App\Entity\RegistroUsuarios;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RegistroUsuarios>
 *
 * @method RegistroUsuarios|null find($id, $lockMode = null, $lockVersion = null)
 * @method RegistroUsuarios|null findOneBy(array $criteria, array $orderBy = null)
 * @method RegistroUsuarios[]    findAll()
 * @method RegistroUsuarios[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RegistroUsuariosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistroUsuarios::class);
    }

    public function add(RegistroUsuarios $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RegistroUsuarios $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return RegistroUsuarios[] Returns an array of RegistroUsuarios objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RegistroUsuarios
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
