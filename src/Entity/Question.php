<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity
 */
class Question
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_categorie", type="integer", nullable=true, options={"default" : NULL})
     */
    public $idCategorie;

    /**
     * @var string|null
     *
     * @ORM\Column(name="question", type="string", length=255, nullable=true, options={"default" : NULL})
     */
    public $question;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="categorie")
     * @ORM\JoinColumn(nullable=true)
     */
    private $categorie= NULL;

    /**
     * @ORM\OneToMany(targetEntity=Reponse::class, mappedBy="question")
     */
    private $reponse= NULL;

    

    public function __construct()
    {
        $this->reponse = new ArrayCollection();
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection|Reponse[]
     */
    public function getReponse(): Collection
    {
        return $this->reponse;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponse->contains($reponse)) {
            $this->reponse[] = $reponse;
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponse->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return 'question';
    }
}
