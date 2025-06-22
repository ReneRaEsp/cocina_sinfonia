<?php

namespace App\Entity;

use App\Repository\PapeleraRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PapeleraRepository::class)
 */
class Papelera
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $numero;

    /**
     * @ORM\Column(type="float")
     */
    private $pagado;

    /**
     * @ORM\Column(type="float")
     */
    private $por_pagar;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $union_mesas = [];

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $localizacion;

    /**
     * @ORM\Column(type="integer")
     */
    private $zonas_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $comensales;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $factura;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pedidos;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getPagado(): ?float
    {
        return $this->pagado;
    }

    public function setPagado(float $pagado): self
    {
        $this->pagado = $pagado;

        return $this;
    }

    public function getPorPagar(): ?float
    {
        return $this->por_pagar;
    }

    public function setPorPagar(float $por_pagar): self
    {
        $this->por_pagar = $por_pagar;

        return $this;
    }

    public function getUnionMesas(): ?array
    {
        return $this->union_mesas;
    }

    public function setUnionMesas(?array $union_mesas): self
    {
        $this->union_mesas = $union_mesas;

        return $this;
    }

    public function getLocalizacion(): ?string
    {
        return $this->localizacion;
    }

    public function setLocalizacion(string $localizacion): self
    {
        $this->localizacion = $localizacion;

        return $this;
    }

    public function getZonasId(): ?int
    {
        return $this->zonas_id;
    }

    public function setZonasId(int $zonas_id): self
    {
        $this->zonas_id = $zonas_id;

        return $this;
    }

    public function getComensales(): ?int
    {
        return $this->comensales;
    }

    public function setComensales(int $comensales): self
    {
        $this->comensales = $comensales;

        return $this;
    }

    public function getFactura(): ?int
    {
        return $this->factura;
    }

    public function setFactura(?int $factura): self
    {
        $this->factura = $factura;

        return $this;
    }

    public function getPedidos(): ?string
    {
        return $this->pedidos;
    }

    public function setPedidos(?string $pedidos): self
    {
        $this->pedidos = $pedidos;

        return $this;
    }

}
