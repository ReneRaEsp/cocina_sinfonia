<?php

namespace App\Entity;

use App\Repository\StatscomidaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatscomidaRepository::class)
 */
class Statscomida
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Comida::class, inversedBy="statscomidas")
     */
    private $comida;

    /**
     * @ORM\ManyToOne(targetEntity=TipoComida::class, inversedBy="statscomidas")
     */
    private $tipocomida;

    /**
     * @ORM\ManyToOne(targetEntity=Mesas::class, inversedBy="statscomidas")
     */
    private $mesa;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity=Productostienda::class, inversedBy="statscomidas")
     */
    private $tienda;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComida(): ?comida
    {
        return $this->comida;
    }

    public function setComida(?comida $comida): self
    {
        $this->comida = $comida;

        return $this;
    }

    public function getTipocomida(): ?TipoComida
    {
        return $this->tipocomida;
    }

    public function setTipocomida(?TipoComida $tipocomida): self
    {
        $this->tipocomida = $tipocomida;

        return $this;
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

    public function getTienda(): ?Productostienda
    {
        return $this->tienda;
    }

    public function setTienda(?Productostienda $tienda): self
    {
        $this->tienda = $tienda;

        return $this;
    }
}
