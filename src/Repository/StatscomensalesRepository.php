<?php

namespace App\Repository;

use App\Entity\Statscomensales;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statscomensales>
 *
 * @method Statscomensales|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statscomensales|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statscomensales[]    findAll()
 * @method Statscomensales[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatscomensalesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statscomensales::class);
    }

    public function add(Statscomensales $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Statscomensales $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findComensalesByTimeRangeAndZone($diaAnterior, $startHour, $endHour, $zone)
    {

        $sql = " SELECT SUM(s.num_comensales) as total_comensales 
                FROM statscomensales s
                JOIN mesas m ON s.mesa_id = m.id
                WHERE DATE(s.fecha) = :diaAnterior AND HOUR(s.fecha) >= :startHour AND HOUR(s.fecha) < :endHour AND m.zonas_id = :zone";
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql,
             ['diaAnterior'=> $diaAnterior,'startHour' => $startHour, 'endHour' => $endHour, 'zone' => $zone]);
            $topComensales = $stmt->fetchAllAssociative();

            return $topComensales;
    }
    public function findComensalesByDayAndZone($day, $zone)
    {

        $sql = "SELECT SUM(s.num_comensales) as promedio_comensales
                FROM statscomensales s
                JOIN mesas m ON s.mesa_id = m.id
                WHERE  DATE(s.fecha) = :day AND m.zonas_id = :zone";
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql,
             [ 'zone' => $zone, 'day' => $day]);
            $topComensales = $stmt->fetchAllAssociative();

            return $topComensales;
    }


    public function allComensalesPerDay()
    {

        $sql = "SELECT id ,DATE(fecha) as fecha, SUM(num_comensales) as total_comensales
                FROM statscomensales
                group by DATE(fecha)";
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
            $topComensales = $stmt->fetchAllAssociative();

            return $topComensales;
    }

//    /**
//     * @return Statscomensales[] Returns an array of Statscomensales objects
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

//    public function findOneBySomeField($value): ?Statscomensales
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
