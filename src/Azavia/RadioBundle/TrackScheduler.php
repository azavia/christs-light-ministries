<?php
/**
 * Track Scheduler service
 *
 * Schedules the tracks in the playlist so that they do not violate the DMCA
 * rules.
 *
 * @author Brandon Olivares <bolivares@azavia.com>
 * @copyright 2012 Azavia Technologies, LLC.
 */

namespace Azavia\RadioBundle;

use Azavia\RadioBundle\Entity\Performance;
use Symfony\Bundle\DoctrineBundle\Registry;

/**
 * TrackScheduler 
 *
 * Schedules the tracks in the playlist in order to avoid violating the DMCA
 * rules.
 * 
 * @copyright 2012 Azavia Technologies, LLC
 * @author Brandon Olivares<bolivares@azavia.com> 
 */
class TrackScheduler
{

    /**
     * Doctrine registry
     *
     * Allows access to the Doctrine objects such as the entity manager,
     * repositories, etc.
     * 
     * @var Registry
     * @access protected
     */
    protected $doctrine;

    /**
     * Processed track dir
     *
     * @var string
     */
    protected $processed_track_dir;

    // {{{ Constructor

    /**
     * Constructor for TrackScheduler service
     * 
     * @param Registry $doctrine 
     * @access public
     */
    public function __construct(Registry $doctrine, $processed_track_dir)
    {
        $this->doctrine = $doctrine;
        $this->processed_track_dir = $processed_track_dir;
    }

    // }}}

    // {{{ scheduleTrack()

    /**
     * Gets the next track in the playlist.
     *
     * This method returns a trac that, if played after the currently
     * scheduled tracks, would not violate the DMCA rules.
     * 
     * @access public
     * @return Track
     */
    public function scheduleTrack()
    {
        $track = $this->doctrine->getRepository('AzaviaRadioBundle:Track')
            ->scheduleTrack();

        $performance = new Performance();
        $performance->setPlayedAt(new \DateTime());
        $performance->setTrack($track);

$this->doctrine->getEntityManager()->persist($performance);
$this->doctrine->getEntityManager()->flush();

return $track;
    }

    // }}}

    // {{{ getProcessedTrackDir()

    /**
     * Get the processed track directory.
     *
     * @return string The path to the processed track directory.
     */
    public function getProcessedTrackDir()
    {
        return $this->processed_track_dir;
    }

    // }}}

}
