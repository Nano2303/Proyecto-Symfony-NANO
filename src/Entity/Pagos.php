<?php

namespace App\Entity;

use App\Repository\PagosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PagosRepository::class)]
class Pagos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?float $monto = null;

    #[ORM\Column(length: 100)]
    private ?string $metodo_pago = null;

    #[ORM\OneToOne(mappedBy: 'pagos', cascade: ['persist', 'remove'])]
    private ?Ordenes $ordenes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getMonto(): ?float
    {
        return $this->monto;
    }

    public function setMonto(float $monto): static
    {
        $this->monto = $monto;

        return $this;
    }

    public function getMetodoPago(): ?string
    {
        return $this->metodo_pago;
    }

    public function setMetodoPago(string $metodo_pago): static
    {
        $this->metodo_pago = $metodo_pago;

        return $this;
    }

    public function getOrdenes(): ?Ordenes
    {
        return $this->ordenes;
    }

    public function setOrdenes(Ordenes $ordenes): static
    {
        // set the owning side of the relation if necessary
        if ($ordenes->getPagos() !== $this) {
            $ordenes->setPagos($this);
        }

        $this->ordenes = $ordenes;

        return $this;
    }
}
