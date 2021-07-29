<?php

namespace App\Controller;

use App\Entity\Temoignage;
use App\Form\SearchBarType;
use App\Form\TemoignageType;
use App\Repository\TemoignageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;

/**
 * @Route("/admin/temoignage")
 */
class TemoignageController extends AbstractController
{
    /**
     * @Route("/liste", name="temoignage_index", methods={"GET", "POST"})
     */
    public function index(TemoignageRepository $temoignageRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(SearchBarType::class, null, ['attr' => ['class' => 'd-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search']]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            $actualiteSearched = $temoignageRepository->search($request->request->get('search_bar')['text']);
            return $this->render('temoignage/index.html.twig', [
                'temoignagess' => $actualiteSearched,
                'form' =>$form->createView()
            ]);
        }
        return $this->render('temoignage/index.html.twig', [
            'temoignages' => $paginator->paginate($temoignageRepository->findAll(), $request->query->getInt('page', 1), 10),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="temoignage_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $temoignage = new Temoignage();
        $form = $this->createForm(TemoignageType::class, $temoignage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $url = substr($temoignage->getUrl(), strpos($temoignage->getUrl(),'=')+1);
            $curl = curl_init();
            // set our url with curl_setopt()
            curl_setopt($curl, CURLOPT_URL, "https://www.googleapis.com/youtube/v3/videos?id=$url&key=AIzaSyAI9fQMpCNugGfweumSD_p8SLpGaF9ZhSo");

            // return the transfer as a string, also with setopt()
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            // curl_exec() executes the started curl session
            // $output contains the output string
            $output = curl_exec($curl);

            // close curl resource to free up system resources
            // (deletes the variable made by curl_init)
            $o =json_decode($output);
            curl_close($curl);            
            $verif = substr($output, strpos($output,"totalResults")+15, 1);    
            if($verif == "0"){
                $form->get('url')->addError(new FormError('Url non valide'));
                return $this->render('temoignage/new.html.twig', [
                    'temoignage' => $temoignage,
                    'form' => $form->createView(),
                ]);
            }
            $temoignage->setRef(uniqid('Tem-'));
            $temoignage->setUrl("<iframe width='600' height='400' src='https://www.youtube.com/embed/$url' title='YouTube video player' frameborder='0' allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture' allowfullscreen></iframe>");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($temoignage);
            $entityManager->flush();

            return $this->redirectToRoute('temoignage_index');
        }

        return $this->render('temoignage/new.html.twig', [
            'temoignage' => $temoignage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{ref}", name="temoignage_show", methods={"GET"})
     */
    public function show(Temoignage $temoignage): Response
    {
        return $this->render('temoignage/show.html.twig', [
            'temoignage' => $temoignage,
        ]);
    }


    /**
     * @Route("/{ref}/delete", name="temoignage_delete")
     */
    public function delete(Request $request, Temoignage $temoignage): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($temoignage);
            $entityManager->flush();

        return $this->redirectToRoute('temoignage_index');
    }
}
