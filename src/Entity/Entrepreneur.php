<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EntrepreneurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EntrepreneurRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email", message="l'email existe déjà.")
 * @UniqueEntity("carte", message="numéro existe déjà.")
 * 
 */
class Entrepreneur implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="integer")
     */
    private $cin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $genre;

    /**
     * @ORM\Column(type="date")
     */
    private $datenais;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $villenais;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $paynais = 'Tunisie';

    /**
     * @ORM\Column(type="date")
     */
    private $dateexpcin;

    /**
     * @ORM\Column(type="integer")
     */
    private $tel;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat = false;

    /**
     * @ORM\OneToMany(targetEntity=Facture::class, mappedBy="entrepreneur")
     */
    private $factures;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Rendezvous::class, mappedBy="entrepreneur")
     */
    private $rendezvouses;

    /**
     * @ORM\OneToOne(targetEntity=Categorie::class, mappedBy="entrepreneur", cascade={"persist", "remove"})
     */
    private $categorie;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $carte;

    /**
     * @ORM\OneToMany(targetEntity=Declaration::class, mappedBy="entrepreneur")
     */
    private $declarations;

    /**
     * @ORM\OneToMany(targetEntity=AttestationChiffreAffaire::class, mappedBy="entrepreneur")
     */
    private $attestationChiffreAffaires;

    /**
     * @ORM\OneToMany(targetEntity=AttestationFiscale::class, mappedBy="entrepreneur")
     */
    private $attestationFiscales;

    public function __construct()
    {
        $this->factures = new ArrayCollection();
        $this->rendezvouses = new ArrayCollection();
        $this->declarations = new ArrayCollection();
        $this->attestationChiffreAffaires = new ArrayCollection();
        $this->attestationFiscales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function getPassword2(): string
    {
        return (string) $this->password;
    }

    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }
    public function setPassword2(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(?int $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getDatenais()
    {
        return $this->datenais;
    }

    public function setDatenais($datenais): self
    {
        $this->datenais = $datenais;

        return $this;
    }

    public function getVillenais(): ?string
    {
        return $this->villenais;
    }

    public function setVillenais(?string $villenais): self
    {
        $this->villenais = $villenais;

        return $this;
    }

    public function getPaynais(): ?string
    {
        return $this->paynais;
    }

    public function setPaynais(?string $paynais): self
    {
        $this->paynais = $paynais;

        return $this;
    }

    public function getDateexpcin()
    {
        return $this->dateexpcin;
    }

    public function setDateexpcin($dateexpcin): self
    {
        $this->dateexpcin = $dateexpcin;

        return $this;
    }

    public function getTel(): ?int
    {
        return $this->tel;
    }

    public function setTel(?int $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|Facture[]
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
            $facture->setEntrepreneur($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): self
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getEntrepreneur() === $this) {
                $facture->setEntrepreneur(null);
            }
        }

        return $this;
    }


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setSlug(): string
    {
        $slug = new Slugify();
        return $this->slug = $slug->slugify($this->prenom.$this->nom.date("dis"));
    }

    /**
     * @return Collection|Rendezvous[]
     */
    public function getRendezvouses(): Collection
    {
        return $this->rendezvouses;
    }

    public function addRendezvouse(Rendezvous $rendezvouse): self
    {
        if (!$this->rendezvouses->contains($rendezvouse)) {
            $this->rendezvouses[] = $rendezvouse;
            $rendezvouse->setEntrepreneur($this);
        }

        return $this;
    }

    public function removeRendezvouse(Rendezvous $rendezvouse): self
    {
        if ($this->rendezvouses->removeElement($rendezvouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezvouse->getEntrepreneur() === $this) {
                $rendezvouse->setEntrepreneur(null);
            }
        }

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        // unset the owning side of the relation if necessary
        if ($categorie === null && $this->categorie !== null) {
            $this->categorie->setEntrepreneur(null);
        }

        // set the owning side of the relation if necessary
        if ($categorie !== null && $categorie->getEntrepreneur() !== $this) {
            $categorie->setEntrepreneur($this);
        }

        $this->categorie = $categorie;

        return $this;
    }

    public function getCarte(): ?int
    {
        return $this->carte;
    }

    public function setCarte(?int $carte): self
    {
        $this->carte = $carte;

        return $this;
    }

    /**
     * @return Collection|Declaration[]
     */
    public function getDeclarations(): Collection
    {
        return $this->declarations;
    }

    public function addDeclaration(Declaration $declaration): self
    {
        if (!$this->declarations->contains($declaration)) {
            $this->declarations[] = $declaration;
            $declaration->setEntrepreneur($this);
        }

        return $this;
    }

    public function removeDeclaration(Declaration $declaration): self
    {
        if ($this->declarations->removeElement($declaration)) {
            // set the owning side to null (unless already changed)
            if ($declaration->getEntrepreneur() === $this) {
                $declaration->setEntrepreneur(null);
            }
        }

        return $this;
    }

    public function getFullName(){
        return "$this->nom $this->prenom";
    }

    /**
     * @return Collection|AttestationChiffreAffaire[]
     */
    public function getAttestationChiffreAffaires(): Collection
    {
        return $this->attestationChiffreAffaires;
    }

    public function addAttestationChiffreAffaire(AttestationChiffreAffaire $attestationChiffreAffaire): self
    {
        if (!$this->attestationChiffreAffaires->contains($attestationChiffreAffaire)) {
            $this->attestationChiffreAffaires[] = $attestationChiffreAffaire;
            $attestationChiffreAffaire->setEntrepreneur($this);
        }

        return $this;
    }

    public function removeAttestationChiffreAffaire(AttestationChiffreAffaire $attestationChiffreAffaire): self
    {
        if ($this->attestationChiffreAffaires->removeElement($attestationChiffreAffaire)) {
            // set the owning side to null (unless already changed)
            if ($attestationChiffreAffaire->getEntrepreneur() === $this) {
                $attestationChiffreAffaire->setEntrepreneur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttestationFiscale[]
     */
    public function getAttestationFiscales(): Collection
    {
        return $this->attestationFiscales;
    }

    public function addAttestationFiscale(AttestationFiscale $attestationFiscale): self
    {
        if (!$this->attestationFiscales->contains($attestationFiscale)) {
            $this->attestationFiscales[] = $attestationFiscale;
            $attestationFiscale->setEntrepreneur($this);
        }

        return $this;
    }

    public function removeAttestationFiscale(AttestationFiscale $attestationFiscale): self
    {
        if ($this->attestationFiscales->removeElement($attestationFiscale)) {
            // set the owning side to null (unless already changed)
            if ($attestationFiscale->getEntrepreneur() === $this) {
                $attestationFiscale->setEntrepreneur(null);
            }
        }

        return $this;
    }
}
