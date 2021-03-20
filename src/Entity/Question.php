<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QuestionRepository::class)
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contenuQuestion;

    /**
     * @ORM\Column(type="array")
     */
    private $propositionsQuestion = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $reponsesQuestion = [];

    /**
     * @ORM\ManyToOne(targetEntity=TypeQuestion::class, inversedBy="questions")
     */
    private $typeQuestion;

    /**
     * @ORM\ManyToOne(targetEntity=Thematique::class, inversedBy="questions")
     */
    private $thematiqueQuestion;

    /**
     * @ORM\ManyToOne(targetEntity=Matiere::class, inversedBy="questions")
     */
    private $matiereQuestion;

    /**
     * @ORM\ManyToMany(targetEntity=Evaluation::class, inversedBy="questions")
     */
    private $evaluation_question;

    public function __construct()
    {
        $this->evaluation_question = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenuQuestion(): ?string
    {
        return $this->contenuQuestion;
    }

    public function setContenuQuestion(string $contenuQuestion): self
    {
        $this->contenuQuestion = $contenuQuestion;

        return $this;
    }

    public function getPropositionsQuestion(): ?array
    {
        return $this->propositionsQuestion;
    }

    public function setPropositionsQuestion(array $propositionsQuestion): self
    {
        $this->propositionsQuestion = $propositionsQuestion;

        return $this;
    }

    public function getReponsesQuestion(): ?array
    {
        return $this->reponsesQuestion;
    }

    public function setReponsesQuestion(?array $reponsesQuestion): self
    {
        $this->reponsesQuestion = $reponsesQuestion;

        return $this;
    }

    public function getTypeQuestion(): ?TypeQuestion
    {
        return $this->typeQuestion;
    }

    public function setTypeQuestion(?TypeQuestion $typeQuestion): self
    {
        $this->typeQuestion = $typeQuestion;

        return $this;
    }

    public function getThematiqueQuestion(): ?Thematique
    {
        return $this->thematiqueQuestion;
    }

    public function setThematiqueQuestion(?Thematique $thematiqueQuestion): self
    {
        $this->thematiqueQuestion = $thematiqueQuestion;

        return $this;
    }

    public function getMatiereQuestion(): ?Matiere
    {
        return $this->matiereQuestion;
    }

    public function setMatiereQuestion(?Matiere $matiereQuestion): self
    {
        $this->matiereQuestion = $matiereQuestion;

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluationQuestion(): Collection
    {
        return $this->evaluationQuestion;
    }

    public function addEvaluationQuestion(Evaluation $evaluation_question): self
    {
        if (!$this->evaluation_question->contains($evaluation_question)) {
            $this->evaluation_question[] = $evaluation_question;
        }

        return $this;
    }

    public function removeEvaluationQuestion(Evaluation $evaluationQuestion): self
    {
        $this->evaluationQuestion->removeElement($evaluationQuestion);

        return $this;
    }

}
