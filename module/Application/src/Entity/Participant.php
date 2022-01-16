<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;




/**
 * @ORM\Entity
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(columns={"bibNumber", "event_id"})}
 * )
 */
class Participant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /** @ORM\Column(type="string") */
    protected $firstname;

    /** @ORM\Column(type="string") */
    protected $lastname;

    /** @ORM\Column(type="string") */
    protected $sex;

    /** @ORM\Column(type="integer", nullable=true) */
    protected $bibNumber = null;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Event")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var \Application\Entity\Event
     */
    protected $event;

    /**
     * @var null|int $runningTime
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $runningTime = null;

    /** @ORM\Column(type="boolean") */
    protected $hasRun = false;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param mixed $sex
     */
    public function setSex($sex)
    {
        $this->sex = $sex;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param Event $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }

    /**
     * @return ?int $bibNumber
     */
    public function getBibNumber(): ?int
    {
        return $this->bibNumber;
    }

    /**
     * @param int $bibNumber
     */
    public function setBibNumber(int $bibNumber): void
    {
        $this->bibNumber = $bibNumber;
    }

    /**
     * @return null|string
     */
    public function getRunningTime(): ?string
    {
        return $this->runningTime ? $this->getSecondsToString() : null;
    }

    /**
     * @param ?string $runningTime
     */
    public function setRunningTime(?string $runningTime): void
    {
        if (null !== $runningTime && trim($runningTime) !== '') {
            $this->runningTime = $this->convertToSeconds($runningTime);
            $this->hasRun = true;
        }
    }

    /**
     * @return bool
     */
    public function hasRun(): bool
    {
        return $this->hasRun;
    }

    private function convertToSeconds(string $time): int
    {
        [$h, $m, $s] = explode(':', $time);

        return intval($s) + 60 * intval($m) + 3600 * intval($h);
    }

    private function getSecondsToString(): string
    {
        return gmdate("H:i:s", $this->runningTime);
    }
}
