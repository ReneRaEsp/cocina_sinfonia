<?php

namespace App\Repository;

use App\Entity\Ventas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ventas>
 *
 * @method Ventas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ventas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ventas[]    findAll()
 * @method Ventas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VentasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ventas::class);
    }

    public function add(Ventas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ventas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function obtenerVentasDelMesActual()
    {
        $fechaActual = new \DateTime('now');
        $primerDiaMes = new \DateTime('first day of ' . $fechaActual->format('Y-m'));
        $ultimoDiaMes = new \DateTime('last day of ' . $fechaActual->format('Y-m'));

        $qb = $this->createQueryBuilder('v');
        $qb->select('SUM(v.pagado) as total')
            ->where($qb->expr()->between('v.fecha', ':primerDiaMes', ':ultimoDiaMes'))
            ->setParameter('primerDiaMes', $primerDiaMes)
            ->setParameter('ultimoDiaMes', $ultimoDiaMes);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function obtenerVentasDelMesAnterior()
    {
        $fechaActual = new \DateTime('now');
        $primerDiaMesAnterior = new \DateTime('first day of ' . $fechaActual->format('Y-m-01') . ' -1 month');
        $ultimoDiaMesAnterior = new \DateTime('last day of ' . $fechaActual->format('Y-m-01') . ' -1 month');

        $qb = $this->createQueryBuilder('v');
        $qb->select('SUM(v.pagado) as total')
            ->where($qb->expr()->between('v.fecha', ':primerDiaMesAnterior', ':ultimoDiaMesAnterior'))
            ->setParameter('primerDiaMesAnterior', $primerDiaMesAnterior)
            ->setParameter('ultimoDiaMesAnterior', $ultimoDiaMesAnterior);

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function obtenerVentasDeDosMesesAnteriores()
    {
        $fechaActual = new \DateTime('now');
        $primerDiaDosMesesAnteriores = new \DateTime('first day of ' . $fechaActual->format('Y-m-01') . ' -2 months');
        $ultimoDiaDosMesesAnteriores = new \DateTime('last day of ' . $fechaActual->format('Y-m-01') . ' -2 months');

        $qb = $this->createQueryBuilder('v');
        $qb->select('SUM(v.pagado) as total')
            ->where($qb->expr()->between('v.fecha', ':primerDiaDosMesesAnteriores', ':ultimoDiaDosMesesAnteriores'))
            ->setParameter('primerDiaDosMesesAnteriores', $primerDiaDosMesesAnteriores)
            ->setParameter('ultimoDiaDosMesesAnteriores', $ultimoDiaDosMesesAnteriores);

        return $qb->getQuery()->getSingleScalarResult();
    }

//    /**
//     * @return Ventas[] Returns an array of Ventas objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ventas
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
