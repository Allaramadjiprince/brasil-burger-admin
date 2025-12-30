<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    const METHODE_WAVE = 'WAVE';
    const METHODE_OM = 'OM';
    
    const STATUT_EN_ATTENTE = 'EN_ATTENTE';
    const STATUT_PAYE = 'PAYE';
    const STATUT_ECHEC = 'ECHEC';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(length: 50)]
    private ?string $methode = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\OneToOne(targetEntity: Commande::class, inversedBy: 'paiement')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;
        return $this;
    }

    public function getMethode(): ?string
    {
        return $this->methode;
    }

    public function setMethode(string $methode): static
    {
        $this->methode = $methode;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;
        return $this;
    }

    public function getMontantFormatted(): string
    {
        return number_format((float) $this->montant, 2, ',', ' ') . ' XOF';
    }

    public function getMethodeLabel(): string
    {
        $labels = [
            self::METHODE_WAVE => 'Wave',
            self::METHODE_OM => 'Orange Money'
        ];
        
        return $labels[$this->methode] ?? $this->methode;
    }

    public function getStatutLabel(): string
    {
        $labels = [
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_PAYE => 'Payé',
            self::STATUT_ECHEC => 'Échec'
        ];
        
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatutColor(): string
    {
        $colors = [
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_PAYE => 'success',
            self::STATUT_ECHEC => 'danger'
        ];
        
        return $colors[$this->statut] ?? 'secondary';
    }

    public function estPaye(): bool
    {
        return $this->statut === self::STATUT_PAYE;
    }
}