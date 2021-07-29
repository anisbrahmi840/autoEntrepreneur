<?php

namespace App\Controller;

use App\Entity\Entrepreneur;
use App\Form\EntrepreneurEditPasswordType;
use App\Form\EntrepreneurEditType;
use App\Form\EntrepreneurType;
use App\Repository\EntrepreneurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;


class EntrepreneurController extends AbstractController
{
    /**
     * Undocumented function
     *
     * @param Entrepreneur $entrepreneur
     * @return void
     * @Route("/entrepreneur/dashboard/", name="entrepreneur_dashboard")
     */
    public function dashboard(){
        if (!$this->getUser()->getCategorie()){
            return $this->redirectToRoute('categorie_new');
        }
        return $this->render('entrepreneur/dashboard.html.twig', [
            'entrepreneur' => $this->getUser()
        ]);
    }
    /**
     * @Route("/new", name="entrepreneur_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $entrepreneur = new Entrepreneur();
        $form = $this->createForm(EntrepreneurType::class, $entrepreneur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entrepreneur->setPassword($encoder->encodePassword($entrepreneur, $entrepreneur->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($entrepreneur);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('entrepreneur/new.html.twig', [
            'entrepreneur' => $entrepreneur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/entrepreneur/carte/{slug}", name="entrepreneur_carte", methods={"GET"})
     */
    public function carte(EntrepreneurRepository $entrepreneurRepository, string $slug): Response
    {
        return $this->render('entrepreneur/carte.html.twig', [
            'entrepreneur' => $entrepreneurRepository->findOneBy(['slug' => $slug]),
        ]);
    }

    /**
     * @Route("/entrepreneur/{slug}", name="entrepreneur_show", methods={"GET"})
     */
    public function show(EntrepreneurRepository $entrepreneurRepository, Entrepreneur $entrepreneur): Response
    {
        if(!$entrepreneur->getCategorie())
            return $this->redirectToRoute(('categorie_new'));
        return $this->render('entrepreneur/show.html.twig', [
            'entrepreneur' => $entrepreneur,
        ]);
    }

    /**
     * @Route("/entrepreneur/{slug}/voir", name="entrepreneur_show_info", methods={"GET"})
     */
    public function showInfo(EntrepreneurRepository $entrepreneurRepository, string $slug): Response
    {
        return $this->render('entrepreneur/voir.html.twig', [
            'entrepreneur' => $entrepreneurRepository->findOneBy(['slug' => $slug]),
        ]);
    }

    /**
     * @Route("/entrepreneur/{slug}/voiractivite", name="entrepreneur_show_info_activite", methods={"GET"})
     */
    public function showInfoActivite(EntrepreneurRepository $entrepreneurRepository, string $slug): Response
    {
        return $this->render('categorie/show.html.twig', [
            'entrepreneur' => $entrepreneurRepository->findOneBy(['slug' => $slug]),
        ]);
    }


    /**
     * @Route("/entrepreneur/{slug}/edit", name="entrepreneur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Entrepreneur $entrepreneur): Response
    {
        $form = $this->createForm(EntrepreneurEditType::class, $entrepreneur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entrepreneur_show', ['slug' => $entrepreneur->getSlug()]);
        }

        return $this->render('entrepreneur/edit.html.twig', [
            'entrepreneur' => $entrepreneur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/entrepreneur/{id}/editpassword", name="entrepreneur_editpassword", methods={"GET","POST"})
     */
    public function editPassword(Request $request, Entrepreneur $entrepreneur, UserPasswordEncoderInterface $encoder): Response
    {
        $oldPasswordBd = $entrepreneur->getPassword();
        $form = $this->createForm(EntrepreneurEditPasswordType::class, $entrepreneur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {     
        
            $oldPassword = $request->request->get('entrepreneur_edit_password')['oldPassword'];
            if(!password_verify($oldPassword, $oldPasswordBd)){
                $form->get('oldPassword')->addError(new FormError('Mot de passe incorrect!'));
            }else{
                $entrepreneur->setPassword($encoder->encodePassword($entrepreneur, $entrepreneur->getPassword()) );
                
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('entrepreneur_show', ['slug' => $entrepreneur->getSlug()]);
            }
        }

        return $this->render('entrepreneur/editPassword.html.twig', [
            'entrepreneur' => $entrepreneur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/entrepreneur/{id}", name="entrepreneur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Entrepreneur $entrepreneur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entrepreneur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($entrepreneur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('entrepreneur_index');
    }
}
