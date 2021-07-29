<?php

namespace App\Controller;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Form\SearchBarType;
use App\Repository\ActualiteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/actualite")
 * @IsGranted("ROLE_ADMIN")
 */
class ActualiteController extends AbstractController
{
    /**
     * @Route("/liste", name="actualite_index", methods={"GET", "POST"})
     */
    public function index(ActualiteRepository $actualiteRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(SearchBarType::class, null, ['attr' => ['class' => 'd-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search']]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            $actualiteSearched = $actualiteRepository->search($request->request->get('search_bar')['text']);
            return $this->render('actualite/index.html.twig', [
                'actualitess' => $actualiteSearched,
                'form' =>$form->createView()
            ]);
        }

        return $this->render('actualite/index.html.twig', [
            'actualites' => $paginator->paginate($actualiteRepository->findAll(),
            $request->query->getInt('page', 1),
            10),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="actualite_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $actualite = new Actualite();
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $actualite->setRef(uniqid('Act-'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($actualite);
            $entityManager->flush();

            return $this->redirectToRoute('actualite_index');
        }

        return $this->render('actualite/new.html.twig', [
            'actualite' => $actualite,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{ref}", name="actualite_show", methods={"GET"})
     */
    public function show(Actualite $actualite): Response
    {
        return $this->render('actualite/show.html.twig', [
            'actualite' => $actualite,
        ]);
    }

    /**
     * @Route("/{ref}/delete", name="actualite_delete")
     */
    public function delete(Request $request, Actualite $actualite): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($actualite);
            $entityManager->flush();

        return $this->redirectToRoute('actualite_index');
    }
}
