<?php
/**
 * Twitter Status Updater
 *
 * A service to update the Twitter status with the latest track.
 *
 * @author Brandon Olivares <programmer2188@gmail.com>
 * @copyright 2012 Brandon Olivares.
 */

namespace Azavia\RadioBundle;

use Azavia\RadioBundle\Entity\Track;

/**
 * Twitter Status Updater
 *
 * Updates the Twitter status with the currently-playing track.
 * 
 * @copyright 2012 Azavia Technologies, LLC
 * @author Brandon Olivares<bolivares@azavia.com> 
 */
class TwitterStatusUpdater
{

    // {{{ Protected properties

    protected $consumer_key;

    protected $consumer_secret;

    protected $access_token;

    protected $access_token_secret;

    // }}}

    // {{{ Constructor

    /**
     * Constructor for TwitterStatusUpdater service
     *
     * @param string $consumer_key
     * @param string $consumer_secret
     * @param string $access_token
     * @param string $access_token_secret
     */
    public function __construct(
            $consumer_key,
            $consumer_secret,
            $access_token,
            $access_token_secret)
    {
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->access_token = $access_token;
        $this->access_token_secret = $access_token_secret;
    }

    // }}}

    // {{{ updateStatus()

    /**
     * Updates the Twitter status with the latest track.
     *
     * @param Track $track
     */
    public function updateStatus(Track $track)
    {
        $title = $track->getTitle();
        if (count($track->getArtists())) {
            $artist = $track->getArtistString();
        }

        $status = "Now playing: $artist - $title - ";
        if (strlen($status) > 115) {
            $status = "$artist - $title";

            if (strlen($status) > 115) {
                $status = $title;

                if (strlen($status) > 115) {
                    $status = $this->truncate($title, 110);
                    $status .= ' ...';
                }
            }
        }

        $status .= 'http://www.live365.com/stations/christs_light?play';

        $twitter = new \EpiTwitter_EpiTwitter(
                $this->consumer_key,
                $this->consumer_secret,
                $this->access_token,
                $this->access_token_secret);

        $twitter->post('/statuses/update.json', array('status' => $status));

    }

    // }}}

    // {{{ truncate()

    private function truncate($string, $width)
    {
$parts = preg_split('/([\s\n\r]+)/', $string, null,  PREG_SPLIT_DELIM_CAPTURE);
$partsCount = count($parts);
$length = 0;
$lastPart = 0;

for (; $lastPart < $partsCount; $lastPart++)
{
    $length += strlen($parts[$lastPart]);

    if ($length > $width) {
        break;
    }
}

return implode(array_slice($parts, 0, $lastPart));
    }

    // }}}

}
