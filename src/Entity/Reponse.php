<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reponse
 *
 * @ORM\Table(name="reponse")
 * @ORM\Entity
 */
class Reponse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_question", type="integer", nullable=true, options={"default" : NULL})
     */
    public $idQuestion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reponse", type="string", length=255, nullable=true, options={"default" : NULL})
     */
    public $reponse;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="reponse_expected", type="boolean", nullable=true, options={"default" : NULL})
     */
    public $reponseExpected;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="reponse")
     * @ORM\JoinColumn(nullable=true)
     */
    private $question = NULL;
    

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function __toString(): string
    {
        return 'reponse';
    }
}
