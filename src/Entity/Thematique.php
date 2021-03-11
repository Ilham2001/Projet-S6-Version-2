<?php

namespace App\Entity;

use App\Repository\ThematiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ThematiqueRepository::class)
 */
class Thematique
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
    private $libelle_thematique;

    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="thematique_question")
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

    public function getLibelleThematique(): ?string
    {
        return $this->libelle_thematique;
    }

    public function setLibelleThematique(string $libelle_thematique): self
    {
        $this->libelle_thematique = $libelle_thematique;

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
            $question->setThematiqueQuestion($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getThematiqueQuestion() === $this) {
                $question->setThematiqueQuestion(null);
            }
        }

        return $this;
    }
}
