<?php
/**
 * Metadata Updater service
 *
 * Updates the metadata at Live365.
 *
 * @author Brandon Olivares <bolivares@azavia.com>
 * @copyright 2012 Azavia Technologies, LLC.
 */

namespace Azavia\RadioBundle;

use Azavia\RadioBundle\Entity\Track;
use Symfony\Bundle\DoctrineBundle\Registry;

/**
 * Metadata Updater 
 *
 * Updates the metadata on Live365.
 * 
 * @copyright 2012 Azavia Technologies, LLC
 * @author Brandon Olivares<bolivares@azavia.com> 
 */
class MetadataUpdater
{

    private $live365_username;

    private $live365_password;

    // {{{ Constructor

    /**
     * Constructor for MetadataUpdater service
     * 
     * @param string $username
     * @param string $password
     * @access public
     */
    public function __construct($username, $password)
    {
        $this->live365_username = $username;
        $this->live365_password = $password;
    }

    // }}}

    // {{{ updateMetadata()

    /**
     * Updates the metadata on Live365.
     *
     * @param Track $track The track whose metadata to send.
     * @access public
     * @return void
     */
    public function updateMetadata(Track $track)
    {
        $url = 'http://www.live365.com/cgi-bin/add_song.cgi?version=2&' .
            'member_name=' . $this->live365_username .
            '&password=' . $this->live365_password .
            '&filename=' .
            urlencode($track->getId() . '.mp3');

        if ($track->getTitle()) {
            $url .= '&title=' . urlencode($track->getTitle());
        }

        if ($track->getAlbum()) {
            $url .= '&album=' . urlencode($track->getAlbum()->getName());
        }

        if (count($track->getArtists())) {
            $url .= '&artist=' . urlencode($track->getArtistString());
        }

        $url .= '&seconds=' . intval($track->getLength());

        file_get_contents($url);
    }

    // }}}

}
