<?php

namespace App\Entity;

use App\Repository\VentasRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VentasRepository::class)
 */
class Ventas
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Mesas::class, inversedBy="ventas")
     * @ORM\JoinColumn(nullable=true)
     */
    private $mesa;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $pagado;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pago;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $num_mesa;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comesales;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $num_ticket;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pedidos_ref;

    /**
     * @ORM\Column(type="string", length=400, nullable=true)
     */
    private $observaciones;

    /**
     * @ORM\Column(type="integer")
     */
    private $iva;

    /**
     * @ORM\Column(type="float")
     */
    private $importe_iva;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMesa(): ?mesas
    {
        return $this->mesa;
    }

    public function setMesa(?mesas $mesa): self
    {
        $this->mesa = $mesa;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getPagado(): ?float
    {
        return $this->pagado;
    }

    public function setPagado(?float $pagado): self
    {
        $this->pagado = $pagado;

        return $this;
    }

    public function getPago(): ?string
    {
        return $this->pago;
    }

    public function setPago(string $pago): self
    {
        $this->pago = $pago;

        return $this;
    }

    public function getNumMesa(): ?string
    {
        return $this->num_mesa;
    }

    public function setNumMesa(?string $num_mesa): self
    {
        $this->num_mesa = $num_mesa;

        return $this;
    }

    public function getComesales(): ?int
    {
        return $this->comesales;
    }

    public function setComesales(?int $comesales): self
    {
        $this->comesales = $comesales;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): self
    {
        $this->ref = $ref;

        return $this;
    }

    public function getNumTicket(): ?string
    {
        return $this->num_ticket;
    }

    public function setNumTicket(?string $num_ticket): self
    {
        $this->num_ticket = $num_ticket;

        return $this;
    }
    
    public function getPedidosRef(): ?string
    {
        return $this->pedidos_ref;
    }

    public function setPedidosRef(?string $pedidos_ref): self
    {
        $this->pedidos_ref = $pedidos_ref;

        return $this;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function setObservaciones(?string $observaciones): self
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    public function getIva(): ?int
    {
        return $this->iva;
    }

    public function setIva(int $iva): self
    {
        $this->iva = $iva;

        return $this;
    }

    public function getImporteIva(): ?float
    {
        return $this->importe_iva;
    }

    public function setImporteIva(float $importe_iva): self
    {
        $this->importe_iva = $importe_iva;

        return $this;
    }
    
}
