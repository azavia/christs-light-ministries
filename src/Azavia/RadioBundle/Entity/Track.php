<?php

namespace Azavia\RadioBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Azavia\RadioBundle\Entity\Track
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Azavia\RadioBundle\Entity\TrackRepository")
 * @ORM\HasLifeCycleCallbacks
 */
class Track
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
     * @var string $filename
     */
    private $filename;
    
    /**
     * @Assert\File(maxSize="30000000")
     */
    private $file;

    /**
     * @var decimal $length
     *
     * @ORM\Column(name="length", type="decimal", precision="25", scale="5")
     */
    private $length;

    /**
     * Amazon Standard Identification Number (ASIN)
     *
     * This is used both for generating links to Amazon, as well as for Live365 to obtain album and track information, including the cover art.
     *
     * @var string $asin
     *
     * @ORM\Column(name="asin", type="string", length=255, nullable="true")
     */
    private $asin;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable="true")
     */
    private $title;

    /**
     * @var ArrayCollection $artists
     *
     * @ORM\ManyToMany(targetEntity="Artist", inversedBy="tracks")
     */
    private $artists;

    /**
     * @var Album $album
     *g.
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="tracks")
     */
    private $album;

    /**
     * @var ArrayCollection $composers
     *
     * @ORM\ManyToMany(targetEntity="Composer", inversedBy="tracks")
     */
    private $composers;

    /**
     * @var integer $year
     *
     * @ORM\Column(name="year", type="integer", nullable="true")
     */
    private $year;

    /**
     * @var datetime $created_at
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var datetime $updated_at
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated_at;

    /**
     * @var ArrayCollection $genres
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="tracks")
     */
    private $genres;

    /**
     * @var integer $play_count
     *
     * @ORM\Column(name="play_count", type="integer")
     */
    private $play_count;

    /**
     * @var datetime $last_played_at
     *
     * @ORM\Column(name="last_played_at", type="datetime", nullable="true")
     */
    private $last_played_at;

    /**
     * @var ArrayCollection $performances
     *
     * @ORM\OneToMany(targetEntity="Performance", mappedBy="track")
     */
    private $performances;
    
    private $unprocessed_track_dir;
    private $processed_track_dir;

    public function __construct()
    {
        $this->artists = new \Doctrine\Common\Collections\ArrayCollection();
    $this->genres = new \Doctrine\Common\Collections\ArrayCollection();
    $this->play_count = 0;
    }
    
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
     * Set filename
     *
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        $this->file = new File($this->unprocessed_track_dir . '/' . $filename);
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * Set file
     *
     * @param File $file
     */
    public function setFile(\Symfony\Component\HttpFoundation\File\File $file)
    {
        $this->file = $file;
    }
    
    /**
     * Get the file for this track.
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set playtime
     *
     * @param decimal $playtime
     */
    public function setPlaytime($playtime)
    {
        $this->playtime = $playtime;
    }

    /**
     * Get playtime
     *
     * @return decimal 
     */
    public function getPlaytime()
    {
        return $this->playtime;
    }

    /**
     * Set asin
     *
     * @param string $asin
     */
    public function setAsin($asin)
    {
        $this->asin = $asin;
    }

    /**
     * Get asin
     *
     * @return string 
     */
    public function getAsin()
    {
        return $this->asin;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set year
     *
     * @param integer $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Get created_at
     *
     * @return datetime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Get updated_at
     *
     * @return datetime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set play_count
     *
     * @param integer $playCount
     */
    public function setPlayCount($playCount)
    {
        $this->play_count = $playCount;
    }

    /**
     * Get play_count
     *
     * @return integer 
     */
    public function getPlayCount()
    {
        return $this->play_count;
    }

    /**
     * Set last_played_at
     *
     * @param datetime $lastPlayedAt
     */
    public function setLastPlayedAt($lastPlayedAt)
    {
        $this->last_played_at = $lastPlayedAt;
    }

    /**
     * Get last_played_at
     *
     * @return datetime 
     */
    public function getLastPlayedAt()
    {
        return $this->last_played_at;
    }

    /**
     * Add artists
     *
     * @param Azavia\RadioBundle\Entity\Artist $artists
     */
    public function addArtist(\Azavia\RadioBundle\Entity\Artist $artists)
    {
        $this->artists[] = $artists;
    }

    /**
     * Get artists
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getArtists()
    {
        return $this->artists;
    }

    /**
     * Set album
     *
     * @param Azavia\RadioBundle\Entity\Album $album
     */
    public function setAlbum(\Azavia\RadioBundle\Entity\Album $album)
    {
        $this->album = $album;
    }

    /**
     * Get album
     *
     * @return Azavia\RadioBundle\Entity\Album 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Add genres
     *
     * @param Azavia\RadioBundle\Entity\Genre $genres
     */
    public function addGenre(\Azavia\RadioBundle\Entity\Genre $genres)
    {
        $this->genres[] = $genres;
    }

    /**
     * Get genres
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getGenres()
    {
        return $this->genres;
    }


    /**
     * Set length
     *
     * @param decimal $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * Get length
     *
     * @return decimal 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Add composers
     *
     * @param Azavia\RadioBundle\Entity\Composer $composers
     */
    public function addComposer(\Azavia\RadioBundle\Entity\Composer $composers)
    {
        $this->composers[] = $composers;
    }

    /**
     * Get composers
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getComposers()
    {
        return $this->composers;
    }
    
    public function setUnprocessedTrackDir($unprocessed_track_dir)
    {
        $this->unprocessed_track_dir = $unprocessed_track_dir;
    }
    
    public function getUnprocessedTrackDir()
    {
        return $this->unprocessed_track_dir;
    }
    
    public function setProcessedTrackDir($processed_track_dir)
    {
        $this->processed_track_dir = $processed_track_dir;
    }
    
    public function getProcessedTrackDir()
    {
        return $this->processed_track_dir;
    }
    
    /**
     * @ORM\PostPersist()
     */
    public function upload()
    {
        if ($this->file)
        {
            $this->file->move($this->processed_track_dir, $this->id . '.mp3');
        }
    }
    
    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
        unlink($this->processed_track_dir . DIRECTORY_SEPARATOR . $this->id . '.mp3');
    }

    /**
     * Add performances
     *
     * @param Azavia\RadioBundle\Entity\Performance $performances
     */
    public function addPerformance(\Azavia\RadioBundle\Entity\Performance $performances)
    {
        $this->performances[] = $performances;
    }

    /**
     * Get performances
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getPerformances()
    {
        return $this->performances;
    }

    // {{{ getArtistString()

    public function getArtistString()
    {
        $artistString = '';
        $artistCount = count($this->getArtists());

        foreach ($this->getArtists() as $i => $artist)
        {
if ($i > 0 && $i < $artistCount-1) {
    $artistString .= ', ';
}
elseif ($i == $artistCount-1 && $artistCount > 1) {
    $artistString .= ' & ';
}

$artistString .= $artist->getName();
        }

return $artistString;
    }

    // }}}
}
