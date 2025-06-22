<?php

namespace App\Entity;

use App\Repository\HistorialPedidosRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistorialPedidosRepository::class)
 */
class HistorialPedidos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $mesa;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $comida;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comentarios;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $extras = [];

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $invitacion;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $descuento;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $descuento_eur;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $num_ref;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $precio;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $precio_total;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $comensales;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $iva;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMesa(): ?string
    {
        return $this->mesa;
    }

    public function setMesa(?string $mesa): self
    {
        $this->mesa = $mesa;

        return $this;
    }

    public function getComida(): ?string
    {
        return $this->comida;
    }

    public function setComida(?string $comida): self
    {
        $this->comida = $comida;

        return $this;
    }

    public function getComentarios(): ?string
    {
        return $this->comentarios;
    }

    public function setComentarios(?string $comentarios): self
    {
        $this->comentarios = $comentarios;

        return $this;
    }

    public function getExtras(): ?array
    {
        return $this->extras;
    }

    public function setExtras(?array $extras): self
    {
        $this->extras = $extras;

        return $this;
    }

    public function isInvitacion(): ?bool
    {
        return $this->invitacion;
    }

    public function setInvitacion(?bool $invitacion): self
    {
        $this->invitacion = $invitacion;

        return $this;
    }

    public function getDescuento(): ?float
    {
        return $this->descuento;
    }

    public function setDescuento(float $descuento): self
    {
        $this->descuento = $descuento;

        return $this;
    }

    public function getDescuentoEur(): ?float
    {
        return $this->descuento_eur;
    }

    public function setDescuentoEur(float $descuento_eur): self
    {
        $this->descuento_eur = $descuento_eur;

        return $this;
    }

    public function getNumRef(): ?string
    {
        return $this->num_ref;
    }

    public function setNumRef(string $num_ref): self
    {
        $this->num_ref = $num_ref;

        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;

        return $this;
    }

    public function getPrecioTotal(): ?float
    {
        return $this->precio_total;
    }

    public function setPrecioTotal(float $precio_total): self
    {
        $this->precio_total = $precio_total;

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

    public function getComensales(): ?int
    {
        return $this->comensales;
    }

    public function setComensales(?int $comensales): self
    {
        $this->comensales = $comensales;

        return $this;
    }
    
    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIva(): ?int
    {
        return $this->iva;
    }

    public function setIva(?int $iva): self
    {
        $this->iva = $iva;

        return $this;
    }
}
