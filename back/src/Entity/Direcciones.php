<?php

namespace App\Entity;

use App\Repository\DireccionesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DireccionesRepository::class)]
class Direcciones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $calle = null;

    #[ORM\Column(length: 50)]
    private ?string $ciudad = null;

    #[ORM\Column(length: 100)]
    private ?string $provincia = null;

    #[ORM\Column(length: 10)]
    private ?string $codigo_postal = null;

    #[ORM\Column(length: 20)]
    private ?string $pais = null;

    #[ORM\ManyToOne(inversedBy: 'direcciones')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $usuarios = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalle(): ?string
    {
        return $this->calle;
    }

    public function setCalle(string $calle): static
    {
        $this->calle = $calle;

        return $this;
    }

    public function getCiudad(): ?string
    {
        return $this->ciudad;
    }

    public function setCiudad(string $ciudad): static
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): static
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getCodigoPostal(): ?string
    {
        return $this->codigo_postal;
    }

    public function setCodigoPostal(string $codigo_postal): static
    {
        $this->codigo_postal = $codigo_postal;

        return $this;
    }

    public function getPais(): ?string
    {
        return $this->pais;
    }

    public function setPais(string $pais): static
    {
        $this->pais = $pais;

        return $this;
    }

    public function getUsuarios(): ?Usuarios
    {
        return $this->usuarios;
    }

    public function setUsuarios(?Usuarios $usuarios): static
    {
        $this->usuarios = $usuarios;

        return $this;
    }
}
