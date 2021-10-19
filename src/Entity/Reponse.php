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
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="id_question", type="integer", nullable=true)
     */
    public $idQuestion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reponse", type="string", length=255, nullable=true)
     */
    public $reponse;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="reponse_expected", type="boolean", nullable=true)
     */
    public $reponseExpected;


}
