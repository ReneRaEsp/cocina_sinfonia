<?php

namespace App\Entity;

use App\Repository\ImpuestoFacturasRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImpuestoFacturasRepository::class)
 */
class ImpuestoFacturas
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Facturas::class, inversedBy="impuestoFacturas", cascade={"persist", "remove"})
     */
    private $factura;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $impuesto;

    /**
     * @ORM\Column(type="float")
     */
    private $cantidad;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFactura(): ?Facturas
    {
        return $this->factura;
    }

    public function setFactura(?Facturas $factura): self
    {
        $this->factura = $factura;

        return $this;
    }

    public function getImpuesto(): ?int
    {
        return $this->impuesto;
    }

    public function setImpuesto(?int $impuesto): self
    {
        $this->impuesto = $impuesto;

        return $this;
    }

    public function getCantidad(): ?float
    {
        return $this->cantidad;
    }

    public function setCantidad(float $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }
}
