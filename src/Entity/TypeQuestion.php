<?php

namespace App\Entity;

use App\Repository\TypeQuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeQuestionRepository::class)
 */
class TypeQuestion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $libelle_type;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="type_question")
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

    public function getLibelleType(): ?string
    {
        return $this->libelle_type;
    }

    public function setLibelleType(string $libelle_type): self
    {
        $this->libelle_type = $libelle_type;

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
            $question->setTypeQuestion($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getTypeQuestion() === $this) {
                $question->setTypeQuestion(null);
            }
        }

        return $this;
    }
}
