<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Categorie
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity
 */
class Categorie
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true, options={"default" : NULL})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true, options={"default" : NULL})
     */
    public $name;

    /**
     * @ORM\OneToMany(targetEntity=HistoriqueQuizz::class, mappedBy="categorie")
     */
    private $historiqueQuizzs;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="categorie")
     */
    private $categorie= NULL;

    public function __construct()
    {
        $this->historiqueQuizzs = new ArrayCollection();
        $this->question = new ArrayCollection();
        $this->categorie = new ArrayCollection();
    }

    /**
     * @return Collection|HistoriqueQuizz[]
     */
    public function getHistoriqueQuizzs(): Collection
    {
        return $this->historiqueQuizzs;
    }

    public function addHistoriqueQuizz(HistoriqueQuizz $historiqueQuizz): self
    {
        if (!$this->historiqueQuizzs->contains($historiqueQuizz)) {
            $this->historiqueQuizzs[] = $historiqueQuizz;
            $historiqueQuizz->setCategorie($this);
        }

        return $this;
    }

    public function removeHistoriqueQuizz(HistoriqueQuizz $historiqueQuizz): self
    {
        if ($this->historiqueQuizzs->removeElement($historiqueQuizz)) {
            // set the owning side to null (unless already changed)
            if ($historiqueQuizz->getCategorie() === $this) {
                $historiqueQuizz->setCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Question $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
            $categorie->setCategorie($this);
        }

        return $this;
    }

    public function removeCategorie(Question $categorie): self
    {
        if ($this->categorie->removeElement($categorie)) {
            // set the owning side to null (unless already changed)
            if ($categorie->getCategorie() === $this) {
                $categorie->setCategorie(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return 'categorie';
    }

}
