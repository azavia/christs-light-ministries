<?php

namespace Clm\PrayerRequestBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Gedmo\Mapping\Annotation as Gedmo;
    
/**
 * Clm\PrayerRequestBundle\Entity\PrayerRequest
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Clm\PrayerRequestBundle\Entity\PrayerRequestRepository")
 */
class PrayerRequest
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string $subject
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string $slug
     *
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"subject"})
     */
    private $slug;

    /**
     * @var text $prayer_request
     *
     * @ORM\Column(name="prayer_request", type="text")
     */
    private $prayer_request;

    /**
     * @var integer $ip_address
     *
     * @ORM\Column(name="ip_address", type="integer")
     */
    private $ip_address;
    
    /**
     * @var bool $private
     *
     * @ORM\Column(name="private", type="boolean")
     */
    private $private;


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
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }
    
    /**
     * Get slug
     *
     * @return string
     */    
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set prayer_request
     *
     * @param text $prayerRequest
     */
    public function setPrayerRequest($prayerRequest)
    {
        $this->prayer_request = $prayerRequest;
    }

    /**
     * Get prayer_request
     *
     * @return text 
     */
    public function getPrayerRequest()
    {
        return $this->prayer_request;
    }

    /**
     * Set ip_address
     *
     * @param integer $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ip_address = ip2long($ipAddress);
    }

    /**
     * Get ip_address
     *
     * @return integer 
     */
    public function getIpAddress()
    {
        return long2ip($this->ip_address);
    }

    /**
     * Set private
     *
     * @param boolean $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }

    /**
     * Get private
     *
     * @return boolean 
     */
    public function isPrivate()
    {
        return $this->private;
    }
}