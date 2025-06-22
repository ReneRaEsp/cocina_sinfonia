<?php

namespace App\Entity;

use App\Repository\ImpresorasRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImpresorasRepository::class)
 */
class Impresoras
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sn_cocina;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sn_barra;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSnCocina(): ?string
    {
        return $this->sn_cocina;
    }

    public function setSnCocina(?string $sn_cocina): self
    {
        $this->sn_cocina = $sn_cocina;

        return $this;
    }

    public function getSnBarra(): ?string
    {
        return $this->sn_barra;
    }

    public function setSnBarra(?string $sn_barra): self
    {
        $this->sn_barra = $sn_barra;

        return $this;
    }
}
