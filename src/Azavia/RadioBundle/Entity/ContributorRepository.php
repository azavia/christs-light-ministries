<?php
namespace Azavia\RadioBundle\Entity;

use Doctrine\ORM\EntityRepository;

abstract class ContributorRepository extends EntityRepository
{

    /**
     * Get the class of the called entity.
     *
     * This is necessary since the entity repository will be a subclass of ContributorRepository, and we will nee to instantiate entities proper to that repository.
     *
     * @return string The name of the entity class
     */
    protected function getEntityClass()
    {
        $class = get_class($this);
        return str_replace('Repository', '', $class);
    }

    public function parse(array $contributors)
    {
        $contributorObjects = array();
        
        foreach ($contributors as $contributor)
        {
            $contributorList = explode(',', $contributor);
            
            foreach ($contributorList as $contributorListItem)
            {
                $contributorParts = explode('&', $contributorListItem);
                foreach ($contributorParts as $contributorPart)
                {
                    $entityClass = ($this->getEntityClass());
                    $contributorObject = new $entityClass();
                    $contributorObject->setName(trim($contributorPart));
                    $this->getEntityManager()->persist($contributorObject);
                    
                    $contributorObjects[] = $contributorObject;
                }
            }
        }
        
        return $contributorObjects;
    }

}
?>
