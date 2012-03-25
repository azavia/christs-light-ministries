<?php

namespace Azavia\RadioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Azavia\RadioBundle\Entity\Performance
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Azavia\RadioBundle\Entity\PerformanceRepository")
 */
class Performance
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var datetime $played_at
     *
     * @ORM\Column(name="played_at", type="datetime")
     */
    private $played_at;

    /**
     * @var Track $track
     *
     * @ORM\ManyToOne(targetEntity="Track", inversedBy="performances")
     */
    private $track;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set played_at
     *
     * @param datetime $playedAt
     */
    public function setPlayedAt($playedAt)
    {
        $this->played_at = $playedAt;
    }

    /**
     * Get played_at
     *
     * @return datetime 
     */
    public function getPlayedAt()
    {
        return $this->played_at;
    }

    /**
     * Set track
     *
     * @param Azavia\RadioBundle\Entity\Track $track
     */
    public function setTrack(\Azavia\RadioBundle\Entity\Track $track)
    {
        $this->track = $track;
    }

    /**
     * Get track
     *
     * @return Azavia\RadioBundle\Entity\Track 
     */
    public function getTrack()
    {
        return $this->track;
    }
}
