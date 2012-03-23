<?php

namespace Clm\PrayerRequestBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Clm\PrayerRequestBundle\Entity\PrayerRequest;
use Clm\PrayerRequestBundle\Form\PrayerRequestType;

/**
 * PrayerRequest controller.
 *
 * @Route("/prayer-requests")
 */
class PrayerRequestController extends ContainerAware
{
    /**
     * Lists all PrayerRequest entities.
     *
     * @Route("/", name="prayer-requests")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $prayerRequests = $em->getRepository('ClmPrayerRequestBundle:PrayerRequest')->findAll();

        return array('prayerRequests' => $prayerRequests);
    }

    /**
     * Finds and displays a PrayerRequest entity.
     *
     * @Route("/{slug}.{format}", name="prayer-requests_show", defaults={"format" = "html"})
     * @Method("get")
     * @Template()
     */
    public function showAction($slug)
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $entity = $em->getRepository('ClmPrayerRequestBundle:PrayerRequest')->findOneBySlug($slug);

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find the specified prayer request.');
        }

        $deleteForm = $this->createDeleteForm($slug);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        );
    }

    /**
     * Displays a form to create a new PrayerRequest entity.
     *
     * @Route("/new.{format}", name="prayer-requests_new", defaults={"format" = "html"})
     * @Template()
     */
    public function newAction()
    {
        $entity = new PrayerRequest();
        $form   = $this->createForm(new PrayerRequestType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new PrayerRequest entity.
     *
     * @Route("/create.{format}", name="prayer-requests_create", defaults={"format" = "html"})
     * @Method("post")
     * @Template("ClmPrayerRequestBundle:PrayerRequest:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new PrayerRequest();
        $request = $this->container->get('request');
        $form    = $this->createForm(new PrayerRequestType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->container->get('doctrine')->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return new RedirectResponse($this->generateUrl('prayer-requests_show', array('slug' => $entity->getSlug())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing PrayerRequest entity.
     *
     * @Route("/{slug}/edit.{format}", name="prayer-requests_edit", defaults={"format" = "html"})
     * @Template()
     */
    public function editAction($slug)
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $entity = $em->getRepository('ClmPrayerRequestBundle:PrayerRequest')->find($slug);

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find the specified prayer request.');
        }

        $editForm = $this->createForm(new PrayerRequestType(), $entity);
        $deleteForm = $this->createDeleteForm($slug);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing PrayerRequest entity.
     *
     * @Route("/{slug}/update.{format}", name="prayer-requests_update", defaults={"format" = "html"})
     * @Method("post")
     * @Template("ClmPrayerRequestBundle:PrayerRequest:edit.html.twig")
     */
    public function updateAction($slug)
    {
        $em = $this->container->get('doctrine')->getEntityManager();

        $entity = $em->getRepository('ClmPrayerRequestBundle:PrayerRequest')->find($slug);

        if (!$entity) {
            throw new NotFoundHttpException('Unable to find the specified prayer request.');
        }

        $editForm   = $this->createForm(new PrayerRequestType(), $entity);
        $deleteForm = $this->createDeleteForm($slug);

        $request = $this->container->get('request');

        $editForm->bindRequest($request);

        if ($editFormm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return new RedirectResponse($this->generateUrl('prayer-requests_edit', array('slug' => $slug)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a PrayerRequest entity.
     *
     * @Route("/{slug}/delete.{format}", name="prayer-requests_delete", defaults={"format" = "html"})
     * @Method("post")
     */
    public function deleteAction($slug)
    {
        $form = $this->createDeleteForm($slug);
        $request = $this->container->get('request');

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->container->get('doctrine')->getEntityManager();
            $entity = $em->getRepository('ClmPrayerRequestBundle:PrayerRequest')->findOneBySlug($slug);

            if (!$entity) {
                throw new NotFoundHttpException('Unable to find the specified prayer request.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return new RedirectResponse($this->generateUrl('prayer-requests'));
    }

    private function createDeleteForm($slug)
    {
        return $this->createFormBuilder(array('slug' => $slug))
            ->add('slug', 'hidden')
            ->getForm()
        ;
    }
}
