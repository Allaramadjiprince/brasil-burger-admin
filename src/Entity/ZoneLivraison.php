<?php

namespace App\Entity;

use App\Repository\ZoneLivraisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ZoneLivraisonRepository::class)]
class ZoneLivraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $quartiers = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'zoneLivraison')]
    private Collection $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getQuartiers(): ?string
    {
        return $this->quartiers;
    }

    public function setQuartiers(string $quartiers): static
    {
        $this->quartiers = $quartiers;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setZoneLivraison($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getZoneLivraison() === $this) {
                $commande->setZoneLivraison(null);
            }
        }
        return $this;
    }

    public function getPrixFormatted(): string
    {
        return number_format((float) $this->prix, 2, ',', ' ') . ' XOF';
    }

    public function getQuartiersList(): array
    {
        return array_map('trim', explode(',', $this->quartiers));
    }

    public function quartierEstDansZone(string $quartier): bool
    {
        $quartiers = $this->getQuartiersList();
        $quartier = strtolower(trim($quartier));
        
        foreach ($quartiers as $q) {
            if (strtolower(trim($q)) === $quartier) {
                return true;
            }
        }
        
        return false;
    }

    public function getNombreCommandes(): int
    {
        return $this->commandes->count();
    }
}