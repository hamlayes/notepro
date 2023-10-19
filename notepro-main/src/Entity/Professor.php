<?php

namespace App\Entity;

use App\Repository\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfessorRepository::class)]
class Professor extends User
{
    #[ORM\ManyToMany(targetEntity: ClassLevel::class, inversedBy: 'professors')]
    private Collection $classLevels;

    #[ORM\OneToMany(mappedBy: 'professor', targetEntity: Evaluation::class, fetch: 'EAGER', orphanRemoval: true)]
    private Collection $evaluations;

    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'professors', fetch: 'EAGER', cascade: ['persist', 'remove'])]
    private Collection $subjects;

    public function __construct()
    {
        parent::__construct();
        $this->classLevels = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->subjects = new ArrayCollection();
        $this->students = new ArrayCollection();
    }

    /**
     * @return Collection<int, ClassLevel>
     */
    public function getClassLevels(): Collection
    {
        return $this->classLevels;
    }

    public function addClassLevel(ClassLevel $classLevel): static
    {
        if (!$this->classLevels->contains($classLevel)) {
            $this->classLevels->add($classLevel);
        }

        return $this;
    }

    public function removeClassLevel(ClassLevel $classLevel): static
    {
        $this->classLevels->removeElement($classLevel);

        return $this;
    }

    /**
     * @return Collection<int, Evaluation>
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(Evaluation $evaluation): static
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations->add($evaluation);
            $evaluation->setProfessor($this);
        }

        return $this;
    }

    public function removeEvaluation(Evaluation $evaluation): static
    {
        if ($this->evaluations->removeElement($evaluation)) {
            // set the owning side to null (unless already changed)
            if ($evaluation->getProfessor() === $this) {
                $evaluation->setProfessor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
            $subject->addProfessor($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        if ($this->subjects->removeElement($subject)) {
            $subject->removeProfessor($this);
        }

        return $this;
    }

    public function calculMoyenneEval()
    {
        $total = 0;
        $count = 0;

        foreach ($this->students as $student) {
            $grades = $student->getGrades();
            foreach ($grades as $grade) {
                $total += $grade->getGrade();
                $count++;
            }
        }

        return $count > 0 ? $total / $count : 0;
    }

}

