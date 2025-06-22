<?php

namespace App\Entity;

use App\Repository\StatscomensalesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatscomensalesRepository::class)
 */
class Statscomensales
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity=Mesas::class, inversedBy="statscomensales")
     */
    private $mesa;

    /**
     * @ORM\Column(type="integer")
     */
    private $num_comensales;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

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

    public function getNumComensales(): ?int
    {
        return $this->num_comensales;
    }

    public function setNumComensales(int $num_comensales): self
    {
        $this->num_comensales = $num_comensales;

        return $this;
    }
}
