<?php

namespace App\Repository;

use App\Entity\Mesas;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mesas>
 *
 * @method Mesas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mesas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mesas[]    findAll()
 * @method Mesas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MesasRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mesas::class);
    }

    public function add(Mesas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Mesas $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return Mesas[] Returns an array of Mesas objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Mesas
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findMesasUnidas(): array
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('m.numero, m.union_mesas')
            ->where($qb->expr()->isNotNull('m.union_mesas'))
            ->getQuery();

        return $qb->getQuery()->getResult();
    }

    public function mesasDisponibles(): array
    {
        $sql = "SELECT m1.numero FROM mesas m1 WHERE m1.union_mesas IS NULL AND NOT EXISTS ( SELECT 1 FROM mesas m2 WHERE FIND_IN_SET(m1.numero, m2.union_mesas) > 0 )";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        return array_column($result, 'numero');
    }
    public function mesasConUniones($idZona): array
    {
        $sql = "SELECT m.numero, m.union_mesas FROM mesas m WHERE m.union_mesas IS NOT NULL AND m.zonas_id = :idZona";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, ['idZona' => $idZona]);
        $result = $stmt->fetchAllAssociative();
    
        return array_column($result, 'union_mesas', 'numero');
    }
    public function mesasConUnionesTerraza(): array
    {
        $sql = "SELECT m.numero, m.union_mesas FROM mesas m WHERE m.union_mesas IS NOT NULL AND m.localizacion = 'T' ";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        return array_column($result, 'union_mesas', 'numero');
    }
    public function mesasPrincipalesyLibres(): array
    {
        $sql = "SELECT m1.numero FROM mesas m1 LEFT JOIN mesas m2 ON FIND_IN_SET(m1.numero, m2.union_mesas) > 0 WHERE m2.numero IS NULL";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        return array_column($result, 'numero');
    }

    public function obtenerMesaConUnionesPorId($id): ?array
    {
        $sql = "SELECT m.numero, m.union_mesas FROM mesas m WHERE m.numero = :id";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql, ['id' => $id]);
        $result = $stmt->fetchAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }
    public function totalMesasComedor(): ?array
    {
        $sql = "SELECT count(numero) AS TotalMesas FROM `mesas` WHERE localizacion = 'C'";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }
    public function idMesasComedor(): ?array
    {
        $sql = "SELECT numero, comensales, factura, coord_x, coord_y, icon   FROM `mesas` WHERE localizacion = 'C'";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }

    public function mesasConComensales(): ?array
    {
        $sql = "SELECT numero, comensales FROM mesas WHERE comensales != 0";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }

    public function totalZonas($idZona): ?array
    {
        $sql = "SELECT SUM(por_pagar) AS total_porpagar, zonas_id FROM mesas WHERE zonas_id = $idZona" ;
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }

    public function idMesas($idZona): ?array
    {
        $sql = "SELECT numero, comensales, factura, coord_x, coord_y, icon  FROM `mesas` WHERE zonas_id = $idZona";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }
    public function idMesasTerraza(): ?array
    {
        $sql = "SELECT numero, comensales, factura  FROM `mesas` WHERE localizacion = 'T'";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }

    public function mesasDashboardComedor(): ?array
    {
        $sql = "SELECT DISTINCT t1.numero
                FROM mesas t1
                LEFT JOIN mesas t2 ON FIND_IN_SET(t1.numero, t2.union_mesas) > 0
                WHERE t2.numero IS NULL AND t1.localizacion = 'C'";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }

    public function mesasDashboardTerraza(): ?array
    {
        $sql = "SELECT DISTINCT t1.numero
                FROM mesas t1
                LEFT JOIN mesas t2 ON FIND_IN_SET(t1.numero, t2.union_mesas) > 0
                WHERE t2.numero IS NULL AND t1.localizacion = 'T'";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }

    public function mesasUnidas(): ?array
    {
        $sql = "SELECT numero, union_mesas
                FROM mesas
                WHERE union_mesas IS NOT NULL";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        if (!$result) {
            return null; // Retornar null si no se encuentra la mesa con el ID especificado
        }

        return $result;
    }

    /**
     * Obtiene el último ID de la tabla mesas.
     */
    public function getLastId(): int
    {
        $qb = $this->createQueryBuilder('m');
        $qb->select('MAX(m.id) as max_id');

        $result = $qb->getQuery()->getSingleResult();

        return $result['max_id'] ?? 0;
    }
}
