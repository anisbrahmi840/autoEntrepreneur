<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\ActiviteType;
use App\Form\SearchBarType;
use App\Repository\ActiviteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/activite")
 * @IsGranted("ROLE_ADMIN")
 */
class ActiviteController extends AbstractController
{
    /**
     * @Route("/liste", name="activite_index", methods={"GET", "POST"})
     */
    public function index(ActiviteRepository $activiteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(SearchBarType::class, null, ['attr' => ['class' => 'd-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search']]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            $activateSearched = $activiteRepository->search($request->request->get('search_bar')['text']);
            return $this->render('activite/index.html.twig', [
                'activitess' => $activateSearched,
                'form' =>$form->createView()
            ]);
        }
        return $this->render('activite/index.html.twig', [
            'activites' => $paginator->paginate($activiteRepository->findAll(),
            $request->query->getInt('page', 1),
            10),
            'form' =>$form->createView()
        ]);
    }

    /**
     * @Route("/new", name="activite_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activite->setRef(uniqid('Activite-'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activite);
            $entityManager->flush();

            return $this->redirectToRoute('activite_index');
        }

        return $this->render('activite/new.html.twig', [
            'activite' => $activite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{ref}", name="activite_show", methods={"GET"})
     */
    public function show(Activite $activite): Response
    {
        return $this->render('activite/show.html.twig', [
            'activite' => $activite,
        ]);
    }

    /**
     * @Route("/{ref}/edit", name="activite_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Activite $activite): Response
    {
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('activite_index');
        }

        return $this->render('activite/edit.html.twig', [
            'activite' => $activite,
            'form' => $form->createView(),
        ]);
    }
}
