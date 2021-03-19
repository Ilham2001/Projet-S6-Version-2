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
    private $questionsEvaluation = [];

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nomEvaluation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateEvaluation;

    /**
     * @ORM\ManyToMany(targetEntity=Question::class, mappedBy="evaluation_question")
     */
    private $questions;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="evaluations")
     */
    private $user;

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
        return $this->questionsEvaluation;
    }

    public function setQuestionsEvaluation(array $questionsEvaluation): self
    {
        $this->questionsEvaluation = $questionsEvaluation;

        return $this;
    }

    public function getNomEvaluation(): ?string
    {
        return $this->nomEvaluation;
    }

    public function setNomEvaluation(string $nomEvaluation): self
    {
        $this->nomEvaluation = $nomEvaluation;

        return $this;
    }

    public function getDateEvaluation(): ?\DateTimeInterface
    {
        return $this->dateEvaluation;
    }

    public function setDateEvaluation(\DateTimeInterface $dateEvaluation): self
    {
        $this->dateEvaluation = $dateEvaluation;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
