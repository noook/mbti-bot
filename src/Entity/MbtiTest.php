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
     * @ORM\JoinColumn(nullable=false)
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
    private $EI;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $NS;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $TF;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $PJ;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MbtiAnswer", mappedBy="test", orphanRemoval=true)
     */
    private $answers;

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

    public function getEI(): ?int
    {
        return $this->EI;
    }

    public function setEI(?int $EI): self
    {
        $this->EI = $EI;

        return $this;
    }

    public function getNS(): ?int
    {
        return $this->NS;
    }

    public function setNS(?int $NS): self
    {
        $this->NS = $NS;

        return $this;
    }

    public function getTF(): ?int
    {
        return $this->TF;
    }

    public function setTF(?int $TF): self
    {
        $this->TF = $TF;

        return $this;
    }

    public function getPJ(): ?int
    {
        return $this->PJ;
    }

    public function setPJ(?int $PJ): self
    {
        $this->PJ = $PJ;

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
}
