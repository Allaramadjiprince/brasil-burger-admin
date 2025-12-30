<?php

namespace App\Entity;

use App\Repository\LivreurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivreurRepository::class)]
class Livreur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $vehicule = null;

    #[ORM\Column]
    private bool $disponible = true;

    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'livreur')]
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getVehicule(): ?string
    {
        return $this->vehicule;
    }

    public function setVehicule(?string $vehicule): static
    {
        $this->vehicule = $vehicule;
        return $this;
    }

    public function isDisponible(): bool
    {
        return $this->disponible;
    }

    public function setDisponible(bool $disponible): static
    {
        $this->disponible = $disponible;
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
            $commande->setLivreur($this);
        }
        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            if ($commande->getLivreur() === $this) {
                $commande->setLivreur(null);
            }
        }
        return $this;
    }

    public function getNomComplet(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getCommandesEnCours(): Collection
    {
        return $this->commandes->filter(function(Commande $commande) {
            return in_array($commande->getStatut(), [
                Commande::STATUT_EN_PREPARATION,
                Commande::STATUT_PRET
            ]);
        });
    }

    public function getNombreCommandesEnCours(): int
    {
        return $this->getCommandesEnCours()->count();
    }

    public function peutPrendreNouvelleCommande(): bool
    {
        return $this->disponible && $this->getNombreCommandesEnCours() < 3;
    }
}