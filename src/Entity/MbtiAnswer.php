<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MbtiAnswerRepository")
 */
class MbtiAnswer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\MbtiTest", inversedBy="answers")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $test;

    /**
     * @ORM\Column(type="integer")
     */
    private $step;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $value;

    /**
     * @ORM\Column(type="integer")
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTest(): ?MbtiTest
    {
        return $this->test;
    }

    public function setTest(?MbtiTest $test): self
    {
        $this->test = $test;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getQuestion(): ?int
    {
        return $this->question;
    }

    public function setQuestion(int $question): self
    {
        $this->question = $question;

        return $this;
    }
}
