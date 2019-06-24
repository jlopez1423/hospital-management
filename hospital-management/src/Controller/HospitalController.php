<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Hospital;
use App\Form\ContactType;
use App\Form\HospitalType;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/hospital")
 */
class HospitalController extends AbstractController
{
    /**
     * List all hospitals
     *
     * @param HospitalRepository $hospitalRepository
     * @return Response
     *
     * @Route("/", name="hospital_index", methods={"GET"})
     */
    public function index(HospitalRepository $hospitalRepository): Response
    {
        return $this->render('hospital/index.html.twig', [
            'hospitals' => $hospitalRepository->findAll(),
        ]);
    }

    /**
     * Create a new hospital entry
     *
     * @param Request $request
     * @return Response
     *
     * @Route("/new", name="hospital_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        // Make sure user is an admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $hospital = new Hospital();
        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hospital);
            $entityManager->flush();

            return $this->redirectToRoute('hospital_index');
        }

        return $this->render('hospital/new.html.twig', [
            'hospital' => $hospital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Display single hospital
     *
     * @param Request $request
     * @param Hospital $hospital
     * @return Response
     *
     * @Route("/{id}", name="hospital_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Hospital $hospital, $id): Response
    {
       // grab hospital object
        /** @var Hospital $hospital */
        $hospital  = $this->getDoctrine()->getRepository(Hospital::class)->findOneBy([
            'id' => $id
        ]);

        // display the contact form in hospital
        $contact = new Contact();
        $contact->setHospital($hospital);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('contact_index');
        }

        return $this->render('hospital/show.html.twig', [
            'hospital' => $hospital,
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit and existing hospital entry
     *
     * @param Request $request
     * @param Hospital $hospital
     * @return Response
     *
     * @Route("/{id}/edit", name="hospital_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Hospital $hospital): Response
    {
        // Make sure user is an admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('hospital_index', [
                'id' => $hospital->getId(),
            ]);
        }

        return $this->render('hospital/edit.html.twig', [
            'hospital' => $hospital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a hospital entry
     *
     * @param Request $request
     * @param Hospital $hospital
     * @return Response
     *
     * @Route("/{id}", name="hospital_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Hospital $hospital): Response
    {
        // Make sure user is an admin
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$hospital->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($hospital);
            $entityManager->flush();
        }

        return $this->redirectToRoute('hospital_index');
    }
}
