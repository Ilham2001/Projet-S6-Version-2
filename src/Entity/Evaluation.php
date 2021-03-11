<?php

namespace App\Entity;

use App\Repository\EvaluationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvaluationRepository::class)
 */
class Evaluation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="array")
     */
    private $questions_evaluation = [];

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom_evaluation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_evaluation;

    /**
     * @ORM\ManyToMany(targetEntity=Question::class, mappedBy="evaluation_question")
     */
    private $questions;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionsEvaluation(): ?array
    {
        return $this->questions_evaluation;
    }

    public function setQuestionsEvaluation(array $questions_evaluation): self
    {
        $this->questions_evaluation = $questions_evaluation;

        return $this;
    }

    public function getNomEvaluation(): ?string
    {
        return $this->nom_evaluation;
    }

    public function setNomEvaluation(string $nom_evaluation): self
    {
        $this->nom_evaluation = $nom_evaluation;

        return $this;
    }

    public function getDateEvaluation(): ?\DateTimeInterface
    {
        return $this->date_evaluation;
    }

    public function setDateEvaluation(\DateTimeInterface $date_evaluation): self
    {
        $this->date_evaluation = $date_evaluation;

        return $this;
    }

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->addEvaluationQuestion($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            $question->removeEvaluationQuestion($this);
        }

        return $this;
    }
}
