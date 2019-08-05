<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MbtiTestRepository")
 */
class MbtiTest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FacebookUser", inversedBy="mbtiTests")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $step;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $E;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $I;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $N;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $S;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $T;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $F;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $P;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $J;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MbtiAnswer", mappedBy="test", orphanRemoval=true)
     */
    private $answers;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completed_at;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?FacebookUser
    {
        return $this->user;
    }

    public function setUser(?FacebookUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStep(): ?int
    {
        return $this->step;
    }

    public function setStep(int $step): self
    {
        $this->step = $step;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getE(): ?int
    {
        return $this->E;
    }

    public function setE(?int $E): self
    {
        $this->E = $E;

        return $this;
    }

    public function getI(): ?int
    {
        return $this->E;
    }

    public function setI(?int $I): self
    {
        $this->I = $I;

        return $this;
    }

    public function getN(): ?int
    {
        return $this->N;
    }

    public function setN(?int $N): self
    {
        $this->N = $N;

        return $this;
    }

    public function getS(): ?int
    {
        return $this->S;
    }

    public function setS(?int $S): self
    {
        $this->S = $S;

        return $this;
    }

    public function getT(): ?int
    {
        return $this->T;
    }

    public function setT(?int $T): self
    {
        $this->T = $T;

        return $this;
    }

    public function getF(): ?int
    {
        return $this->F;
    }

    public function setF(?int $F): self
    {
        $this->F = $F;

        return $this;
    }

    public function getJ(): ?int
    {
        return $this->J;
    }

    public function setJ(?int $J): self
    {
        $this->J = $J;

        return $this;
    }

    public function getP(): ?int
    {
        return $this->P;
    }

    public function setP(?int $P): self
    {
        $this->P = $P;

        return $this;
    }

    /**
     * @return Collection|MbtiAnswer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(MbtiAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setTest($this);
        }

        return $this;
    }

    public function removeAnswer(MbtiAnswer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getTest() === $this) {
                $answer->setTest(null);
            }
        }

        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completed_at;
    }

    public function setCompletedAt(?\DateTimeInterface $completed_at): self
    {
        $this->completed_at = $completed_at;

        return $this;
    }
}
