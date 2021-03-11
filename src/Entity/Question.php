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
    private $contenu_question;

    /**
     * @ORM\Column(type="array")
     */
    private $propositions_question = [];

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $reponses_question = [];

    /**
     * @ORM\ManyToOne(targetEntity=TypeQuestion::class, inversedBy="questions")
     */
    private $type_question;

    /**
     * @ORM\ManyToOne(targetEntity=Thematique::class, inversedBy="questions")
     */
    private $thematique_question;

    /**
     * @ORM\ManyToOne(targetEntity=Matiere::class, inversedBy="questions")
     */
    private $matiere_question;

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
        return $this->contenu_question;
    }

    public function setContenuQuestion(string $contenu_question): self
    {
        $this->contenu_question = $contenu_question;

        return $this;
    }

    public function getPropositionsQuestion(): ?array
    {
        return $this->propositions_question;
    }

    public function setPropositionsQuestion(array $propositions_question): self
    {
        $this->propositions_question = $propositions_question;

        return $this;
    }

    public function getReponsesQuestion(): ?array
    {
        return $this->reponses_question;
    }

    public function setReponsesQuestion(?array $reponses_question): self
    {
        $this->reponses_question = $reponses_question;

        return $this;
    }

    public function getTypeQuestion(): ?TypeQuestion
    {
        return $this->type_question;
    }

    public function setTypeQuestion(?TypeQuestion $type_question): self
    {
        $this->type_question = $type_question;

        return $this;
    }

    public function getThematiqueQuestion(): ?Thematique
    {
        return $this->thematique_question;
    }

    public function setThematiqueQuestion(?Thematique $thematique_question): self
    {
        $this->thematique_question = $thematique_question;

        return $this;
    }

    public function getMatiereQuestion(): ?Matiere
    {
        return $this->matiere_question;
    }

    public function setMatiereQuestion(?Matiere $matiere_question): self
    {
        $this->matiere_question = $matiere_question;

        return $this;
    }

    /**
     * @return Collection|Evaluation[]
     */
    public function getEvaluationQuestion(): Collection
    {
        return $this->evaluation_question;
    }

    public function addEvaluationQuestion(Evaluation $evaluationQuestion): self
    {
        if (!$this->evaluation_question->contains($evaluationQuestion)) {
            $this->evaluation_question[] = $evaluationQuestion;
        }

        return $this;
    }

    public function removeEvaluationQuestion(Evaluation $evaluationQuestion): self
    {
        $this->evaluation_question->removeElement($evaluationQuestion);

        return $this;
    }

}
