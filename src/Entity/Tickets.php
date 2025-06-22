<?php

namespace App\Entity;

use App\Repository\TicketsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketsRepository::class)
 */
class Tickets
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=mesas::class, inversedBy="tickets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $mesaid;

    /**
     * @ORM\Column(type="integer")
     */
    private $numeroticket;

    /**
     * @ORM\Column(type="simple_array")
     */
    private $pedidos = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMesaid(): ?mesas
    {
        return $this->mesaid;
    }

    public function setMesaid(?mesas $mesaid): self
    {
        $this->mesaid = $mesaid;

        return $this;
    }

    public function getNumeroticket(): ?int
    {
        return $this->numeroticket;
    }

    public function setNumeroticket(int $numeroticket): self
    {
        $this->numeroticket = $numeroticket;

        return $this;
    }

    public function getPedidos(): ?array
    {
        return $this->pedidos;
    }

    public function setPedidos(array $pedidos): self
    {
        $this->pedidos = $pedidos;

        return $this;
    }

}
