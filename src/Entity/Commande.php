<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    const STATUT_EN_ATTENTE = 'EN_ATTENTE';
    const STATUT_VALIDE = 'VALIDE';
    const STATUT_EN_PREPARATION = 'EN_PREPARATION';
    const STATUT_PRET = 'PRET';
    const STATUT_TERMINE = 'TERMINE';
    const STATUT_ANNULE = 'ANNULE';
    const STATUT_LIVRE = 'LIVRE';

    const TYPE_SUR_PLACE = 'SUR_PLACE';
    const TYPE_A_EMPORTER = 'A_EMPORTER';
    const TYPE_LIVRAISON = 'LIVRAISON';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: Livreur::class, inversedBy: 'commandes')]
    private ?Livreur $livreur = null;

    #[ORM\ManyToOne(targetEntity: ZoneLivraison::class)]
    private ?ZoneLivraison $zoneLivraison = null;

    #[ORM\OneToMany(targetEntity: LigneCommande::class, mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private Collection $ligneCommandes;

    #[ORM\OneToOne(targetEntity: Paiement::class, mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private ?Paiement $paiement = null;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->ligneCommandes = new ArrayCollection();
        $this->total = '0.00';
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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(?string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;
        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;
        return $this;
    }

    public function getLivreur(): ?Livreur
    {
        return $this->livreur;
    }

    public function setLivreur(?Livreur $livreur): static
    {
        $this->livreur = $livreur;
        return $this;
    }

    public function getZoneLivraison(): ?ZoneLivraison
    {
        return $this->zoneLivraison;
    }

    public function setZoneLivraison(?ZoneLivraison $zoneLivraison): static
    {
        $this->zoneLivraison = $zoneLivraison;
        return $this;
    }

    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): static
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes->add($ligneCommande);
            $ligneCommande->setCommande($this);
        }
        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): static
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }
        return $this;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): static
    {
        if ($paiement !== null && $paiement->getCommande() !== $this) {
            $paiement->setCommande($this);
        }
        $this->paiement = $paiement;
        return $this;
    }

    public function calculerTotal(): static
    {
        $total = 0;
        foreach ($this->ligneCommandes as $ligne) {
            $total += (float) $ligne->getPrixUnitaire() * $ligne->getQuantite();
        }
        
        if ($this->zoneLivraison && $this->type === self::TYPE_LIVRAISON) {
            $total += (float) $this->zoneLivraison->getPrix();
        }
        
        $this->total = (string) $total;
        return $this;
    }

    public function getTotalFormatted(): string
    {
        return number_format((float) $this->total, 2, ',', ' ') . ' XOF';
    }

    public function getStatutLabel(): string
    {
        $labels = [
            self::STATUT_EN_ATTENTE => 'En attente',
            self::STATUT_VALIDE => 'Validé',
            self::STATUT_EN_PREPARATION => 'En préparation',
            self::STATUT_PRET => 'Prêt',
            self::STATUT_TERMINE => 'Terminé',
            self::STATUT_ANNULE => 'Annulé',
            self::STATUT_LIVRE => 'Livré'
        ];
        
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getTypeLabel(): string
    {
        $labels = [
            self::TYPE_SUR_PLACE => 'Sur place',
            self::TYPE_A_EMPORTER => 'À emporter',
            self::TYPE_LIVRAISON => 'Livraison'
        ];
        
        return $labels[$this->type] ?? $this->type;
    }

    public function getStatutColor(): string
    {
        $colors = [
            self::STATUT_EN_ATTENTE => 'warning',
            self::STATUT_VALIDE => 'info',
            self::STATUT_EN_PREPARATION => 'primary',
            self::STATUT_PRET => 'success',
            self::STATUT_TERMINE => 'secondary',
            self::STATUT_ANNULE => 'danger',
            self::STATUT_LIVRE => 'success'
        ];
        
        return $colors[$this->statut] ?? 'secondary';
    }

    public function peutEtreAnnulee(): bool
    {
        return in_array($this->statut, [
            self::STATUT_EN_ATTENTE,
            self::STATUT_VALIDE,
            self::STATUT_EN_PREPARATION
        ]);
    }

    public function estPayee(): bool
    {
        return $this->paiement && $this->paiement->getStatut() === Paiement::STATUT_PAYE;
    }
}