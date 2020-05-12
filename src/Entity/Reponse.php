<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReponseRepository")
 */
class Reponse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vrai;

    /**
     * @ORM\Column(type="boolean")
     */
    private $faux;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\question", inversedBy="reponses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVrai(): ?bool
    {
        return $this->vrai;
    }

    public function setVrai(bool $vrai): self
    {
        $this->vrai = $vrai;

        return $this;
    }

    public function getFaux(): ?bool
    {
        return $this->faux;
    }

    public function setFaux(bool $faux): self
    {
        $this->faux = $faux;

        return $this;
    }

    public function getQuestion(): ?question
    {
        return $this->question;
    }

    public function setQuestion(?question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
