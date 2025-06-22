<?php

namespace App\Repository;

use App\Entity\Statscomida;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Statscomida>
 *
 * @method Statscomida|null find($id, $lockMode = null, $lockVersion = null)
 * @method Statscomida|null findOneBy(array $criteria, array $orderBy = null)
 * @method Statscomida[]    findAll()
 * @method Statscomida[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatscomidaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Statscomida::class);
    }

    public function add(Statscomida $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Statscomida $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function allWithRepetitions(): array
    {

        $sql = "SELECT 
        s.comida_id, 
        s.tipocomida_id, 
        s.mesa_id,
        s.fecha,
        s.tienda_id, 
        COUNT(*) as num_repeticiones 
    FROM 
        statscomida s
    JOIN 
        comida c ON s.comida_id = c.id
    WHERE 
        c.iscomida = true
    GROUP BY 
        s.comida_id
    ORDER BY 
        num_repeticiones DESC
     LIMIT 5";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        // returns an array of arrays (i.e. a raw data set)
        return $result;
    }

    public function allWithRepetitionsSodas(): array
    {

        $sql = "SELECT 
        s.comida_id, 
        s.tipocomida_id, 
        s.mesa_id,
        s.fecha,
        s.tienda_id, 
        COUNT(*) as num_repeticiones 
    FROM 
        statscomida s
    JOIN 
        comida c ON s.comida_id = c.id
    WHERE 
        c.isbebida = true
    GROUP BY 
        s.comida_id
    ORDER BY 
        num_repeticiones DESC
        LIMIT 5";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        // returns an array of arrays (i.e. a raw data set)
        return $result;
    }

    public function getTopDishPerMonth(): array
    {
        $this->getEntityManager()->getConnection()->executeQuery("SET lc_time_names = 'es_ES'");

        $sql = "
            SELECT 
            DATE_FORMAT(s.fecha, '%M') as mes, 
            s.comida_id,
            COUNT(*) as num_repeticiones 
            FROM 
            statscomida s
            JOIN 
            comida c ON s.comida_id = c.id
            WHERE 
            c.iscomida = true
            GROUP BY 
            mes, s.comida_id 
            ORDER BY 
            MONTH(s.fecha), num_repeticiones DESC;
            ";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        $topDishes = [];
        foreach ($result as $row) {
            $month = $row['mes'];
            if (!isset($topDishes[$month])) {
                $topDishes[$month] = $row;
            }
        }

        return array_values($topDishes);
    }

    public function getTopSodaPerMonth(): array
    {
        $this->getEntityManager()->getConnection()->executeQuery("SET lc_time_names = 'es_ES'");

        $sql = "
            SELECT 
            DATE_FORMAT(s.fecha, '%M') as mes, 
            s.comida_id,
            COUNT(*) as num_repeticiones 
            FROM 
            statscomida s
            JOIN 
            comida c ON s.comida_id = c.id
            WHERE 
            c.isbebida = true
            GROUP BY 
            mes, s.comida_id 
            ORDER BY 
            MONTH(s.fecha), num_repeticiones DESC;
            ";
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        $topDishes = [];
        foreach ($result as $row) {
            $month = $row['mes'];
            if (!isset($topDishes[$month])) {
                $topDishes[$month] = $row;
            }
        }

        return array_values($topDishes);
    }

    public function findTop5PlatosLastWeek()
    {
        
        $fechaInicio = (new \DateTime('-7 days'))->format('d-m-Y');

        $sqlTopPlatos = "SELECT  s.comida_id,c.name,  COUNT(s.comida_id) AS total_pedidos FROM statscomida s JOIN comida c ON s.comida_id = c.id WHERE  s.fecha >= :fecha_inicio AND c.iscomida = true
            GROUP BY 
                s.comida_id
            ORDER BY 
                total_pedidos DESC
            LIMIT 5
        ";
        
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sqlTopPlatos, ['fecha_inicio' => $fechaInicio]);
        $topPlatos = $stmt->fetchAllAssociative();

        // Obtener los pedidos diarios de cada uno de los top 5 platos
        $results = [];
        foreach ($topPlatos as $plato) {
            $sqlPedidosDiarios = " SELECT  DATE(s.fecha) as fecha, COUNT(s.comida_id) AS pedidos FROM  statscomida s WHERE  s.fecha >= :fecha_inicio AND s.comida_id = :comida_id
                            GROUP BY 
                            DATE(s.fecha)
            ";
            
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sqlPedidosDiarios, ['fecha_inicio' => $fechaInicio,'comida_id' => $plato['comida_id'],]);
            $pedidosDiarios = $stmt->fetchAllAssociative();

            $results[] = [
                'nombre' => $plato['name'],
                'pedidosDiarios' => $pedidosDiarios,
            ];
        }

        return $results;
    }

    public function findDishLastWeek($idComida)
    {
        
        $fechaInicio = (new \DateTime('-7 days'))->format('d-m-Y');

        $sqlTopPlatos = "SELECT  s.comida_id,c.name,  COUNT(s.comida_id) AS total_pedidos FROM statscomida s JOIN comida c ON s.comida_id = c.id
             WHERE  s.fecha >= :fecha_inicio AND c.iscomida = true AND c.id = :id_comida
            GROUP BY 
                s.comida_id
            ORDER BY 
                total_pedidos DESC
            LIMIT 5
        ";
        
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sqlTopPlatos, ['fecha_inicio' => $fechaInicio, 'id_comida' => $idComida]);
        $topPlatos = $stmt->fetchAllAssociative();

        // Obtener los pedidos diarios de cada uno de los top 5 platos
        $results = [];
        foreach ($topPlatos as $plato) {
            $sqlPedidosDiarios = " SELECT  DATE(s.fecha) as fecha, COUNT(s.comida_id) AS pedidos FROM  statscomida s WHERE  s.fecha >= :fecha_inicio AND s.comida_id = :comida_id
                            GROUP BY 
                    fecha
            ";
            
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sqlPedidosDiarios, ['fecha_inicio' => $fechaInicio,'comida_id' => $idComida,]);
            $pedidosDiarios = $stmt->fetchAllAssociative();

            $results[] = [
                'nombre' => $plato['name'],
                'pedidosDiarios' => $pedidosDiarios,
            ];
        }

        return $results;
    }

    public function findTop5SodasLastWeek()
    {
        
        $fechaInicio = (new \DateTime('-7 days'))->format('d-m-Y');

        $sqlTopPlatos = "SELECT  s.comida_id,c.name,  COUNT(s.comida_id) AS total_pedidos FROM statscomida s JOIN comida c ON s.comida_id = c.id WHERE  s.fecha >= :fecha_inicio AND c.isbebida = true
            GROUP BY 
                s.comida_id
            ORDER BY 
                total_pedidos DESC
            LIMIT 5
        ";
        
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sqlTopPlatos, ['fecha_inicio' => $fechaInicio]);
        $topPlatos = $stmt->fetchAllAssociative();

        // Obtener los pedidos diarios de cada uno de los top 5 platos
        $results = [];
        foreach ($topPlatos as $plato) {
            $sqlPedidosDiarios = " SELECT  DATE(s.fecha) as fecha, COUNT(s.comida_id) AS pedidos FROM  statscomida s WHERE  s.fecha >= :fecha_inicio AND s.comida_id = :comida_id
                            GROUP BY 
                            DATE(s.fecha)
            ";
            
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sqlPedidosDiarios, ['fecha_inicio' => $fechaInicio,'comida_id' => $plato['comida_id'],]);
            $pedidosDiarios = $stmt->fetchAllAssociative();

            $results[] = [
                'nombre' => $plato['name'],
                'pedidosDiarios' => $pedidosDiarios,
            ];
        }

        return $results;
    }

    public function findSodaLastWeek($idComida)
    {
        
        $fechaInicio = (new \DateTime('-7 days'))->format('d-m-Y');

        $sqlTopPlatos = "SELECT  s.comida_id,c.name,  COUNT(s.comida_id) AS total_pedidos FROM statscomida s JOIN comida c ON s.comida_id = c.id
             WHERE  s.fecha >= :fecha_inicio AND c.isbebida = true AND c.id = :id_comida
            GROUP BY 
                s.comida_id
            ORDER BY 
                total_pedidos DESC
            LIMIT 5
        ";
        
        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sqlTopPlatos, ['fecha_inicio' => $fechaInicio, 'id_comida' => $idComida]);
        $topPlatos = $stmt->fetchAllAssociative();

        // Obtener los pedidos diarios de cada uno de los top 5 platos
        $results = [];
        foreach ($topPlatos as $plato) {
            $sqlPedidosDiarios = " SELECT  DATE(s.fecha) as fecha, COUNT(s.comida_id) AS pedidos FROM  statscomida s WHERE  s.fecha >= :fecha_inicio AND s.comida_id = :comida_id
                            GROUP BY 
                    fecha
            ";
            
            $stmt = $this->getEntityManager()->getConnection()->executeQuery($sqlPedidosDiarios, ['fecha_inicio' => $fechaInicio,'comida_id' => $idComida,]);
            $pedidosDiarios = $stmt->fetchAllAssociative();

            $results[] = [
                'nombre' => $plato['name'],
                'pedidosDiarios' => $pedidosDiarios,
            ];
        }

        return $results;
    }

    public function allFoodRepetitions(): array
    {

        $sql = "SELECT
        sc.id,
        c.name,
        sc.comida_id,
        DATE(sc.fecha) AS dia,
        COUNT(*) AS total_registros
    FROM
        statscomida sc
    JOIN
        comida c ON sc.comida_id = c.id
        WHERE c.iscomida = true
    GROUP BY
        c.name,
        sc.comida_id,
        DATE(sc.fecha)
    ORDER BY
        c.name,
        sc.comida_id,
        dia";

        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        // returns an array of arrays (i.e. a raw data set)
        return $result;
    }


    public function allSodaRepetitions(): array
    {

        $sql = "SELECT
        sc.id,
        c.name,
        sc.comida_id,
        DATE(sc.fecha) AS dia,
        COUNT(*) AS total_registros
    FROM
        statscomida sc
    JOIN
        comida c ON sc.comida_id = c.id
        WHERE c.isbebida = true
    GROUP BY
        c.name,
        sc.comida_id,
        DATE(sc.fecha)
    ORDER BY
        c.name,
        sc.comida_id,
        dia";

        $stmt = $this->getEntityManager()->getConnection()->executeQuery($sql);
        $result = $stmt->fetchAllAssociative();

        // returns an array of arrays (i.e. a raw data set)
        return $result;
    }

    //    /**
    //     * @return Statscomida[] Returns an array of Statscomida objects
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

    //    public function findOneBySomeField($value): ?Statscomida
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
