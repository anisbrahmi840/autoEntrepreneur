<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Agent;
use App\Form\AdminType;
use App\Form\AgentType;
use App\Entity\Declaration;
use App\Entity\Entrepreneur;
use App\Form\DeclarationAdminType;
use App\Form\AdminEditPasswordType;
use App\Form\EntrepreneurAdminType;
use App\Form\FilterSearchType;
use App\Form\SearchBarType;
use App\Repository\AdminRepository;
use App\Repository\AgentRepository;
use Symfony\Component\Form\FormError;
use App\Repository\DeclarationRepository;
use App\Repository\EntrepreneurRepository;
use App\Repository\FactureRepository;
use App\Repository\RendezvousRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Date;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index", methods={"GET"})
     */
    public function index(AdminRepository $adminRepository, EntrepreneurRepository $entrepreneurRepository, FactureRepository $factureRepository, DeclarationRepository $declarationRepository, RendezvousRepository $rendezvousRepository): Response
    {
        if ($this->isGranted('ROLE_AGENT') && !$this->getUser()->getEtat()){            
            session_destroy();
            return $this->redirectToRoute('agent_login');
        }
        return $this->render('admin/index.html.twig', [
            'admins' => $adminRepository->findAll(),
            'nbEntrepreneurs' => count($entrepreneurRepository->findAll()),
            'entrepreneursNonValid' => $entrepreneurRepository->findBy(['etat' => false], null, 6),
            'ev' => count($entrepreneurRepository->findBy(['etat' => true])),
            'env' => count($entrepreneurRepository->findBy(['etat' => false])),
            'nbFactures' => count($factureRepository->findAll()),
            'Nbdeclarations' => count($declarationRepository->findBy(['etat' => true])),
            'nbrendezVous' => count($rendezvousRepository->findAll()),
        ]);
    }

    /**
     * @Route("/new", name="admin_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $admin = new Admin();
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($admin);
            $entityManager->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/new.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_show", methods={"GET"})
     */
    public function show(Admin $admin): Response
    {
        return $this->render('admin/show.html.twig', [
            'admin' => $admin,
        ]);
    }

    /**
     * @Route("/editpassword/edit", name="admin_editpassword", methods={"GET","POST"})
     */
    public function editPassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $admin = $this->getUser();
        $oldPasswordBd = $admin->getPassword();
        $form = $this->createForm(AdminEditPasswordType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {  
            $oldPassword = $request->request->get('admin_edit_password')['oldPassword'];
            
            if(!password_verify($oldPassword, $oldPasswordBd)){
                $form->get('oldPassword')->addError(new FormError('Mot de passe incorrect!'));
            }else{
                $admin->setPassword($encoder->encodePassword($admin, $admin->getPassword()) );                
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('admin_index');
            }
        }

        return $this->render('admin/editPassword.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Admin $admin, UserPasswordEncoderInterface $encoder): Response
    {
        $form = $this->createForm(AdminType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $admin->setPassword($encoder->encodePassword($admin, $admin->getPassword()));

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_index');
        }

        return $this->render('admin/edit.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Admin $admin): Response
    {
        if ($this->isCsrfTokenValid('delete'.$admin->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($admin);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_index');
    }

// ------------Controle Entrepreneur -----------------------------------------------------------------

    /**
     * @Route("/entrepreneurs/liste", name="entrepreneur_index")
     */
    public function indexEntrepreneur(Request $request, EntrepreneurRepository $entrepreneurRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchBarType::class, null, ['attr' => ['class' => 'd-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search']]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            $EntrepreneurSearched = $entrepreneurRepository->search($request->request->get('search_bar')['text']);
            return $this->render('entrepreneur/admin/index.html.twig', [
                'entrepreneurss' => $EntrepreneurSearched,
                'form' =>$form->createView()
            ]);
        }
        
        $entrepreneurs = $paginator->paginate($entrepreneurRepository->findAll(),
        $request->query->getInt('page', 1),
        10);
        return $this->render('entrepreneur/admin/index.html.twig', [
            'entrepreneurs' => $entrepreneurs,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/entrepreneur/{slug}", name="entrepreneur_show_admin", methods={"GET"})
     */
    public function showEntrepreneur(Entrepreneur $entrepreneur): Response
    {
        return $this->render('entrepreneur/admin/show.html.twig', [
            'entrepreneur' => $entrepreneur,
        ]);
    }

    /**
     * @Route("/entrepreneur/{slug}/verif", name="entrepreneur_verif_admin", methods={"GET"})
     */
    public function verifEntrepreneur(Entrepreneur $entrepreneur)
    {
        $entrepreneur->setEtat(!$entrepreneur->getEtat());
        $this->getDoctrine()->getManager()->flush();
       return $this->redirectToRoute('entrepreneur_index');
    }

    /**
     * @Route("/entrepreneur/{slug}/edit", name="entrepreneur_edit_admin", methods={"GET","POST"})
     */
    public function editEntrepreneur(Request $request, Entrepreneur $entrepreneur): Response
    {
        $form = $this->createForm(EntrepreneurAdminType::class, $entrepreneur, [
            'action' => $this->generateUrl('entrepreneur_edit_admin', ['slug' => $entrepreneur->getSlug()]),
            'method' => 'GET',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entrepreneur_index');
        }

        return $this->render('entrepreneur/admin/editAdmin.html.twig', [
            'entrepreneur' => $entrepreneur,
            'form' => $form->createView(),
        ]);
    }

// -----------------------------Controle Déclarations --------------------------------------------------------------
  
    /**
     * @Route("/declarations/liste", name="admin_declaration_index", methods={"GET", "POST"})
     */
    public function listeDeclaration(DeclarationRepository $declarationRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(FilterSearchType::class);
        $form->handleRequest($request);

                
        if($form->isSubmitted() && $form->isValid()){
            $nom = $request->request->get('filter_search')['nom'];
            $dateStart = $request->request->get('filter_search')['dateStart'] ?: new \DateTime('1950-01-01') ;
            $dateEnd = $request->request->get('filter_search')['dateEnd'] ?:  new \DateTime();
            $etat = $request->request->get('filter_search')['etat'];
            switch ($etat) {
                case 0:
                    $etat = null;
                    break;
                case 1:
                    $etat = true;
                    break;
                case 2:
                    $etat = false;
                    break;
            }
            $declarations = $declarationRepository->filter($nom, $dateStart, $dateEnd, $etat);
            return $this->render('declaration/admin/index.html.twig', [
                'declarations' => '',
                'declarationss' => $declarations,
                'titre' => 'Déclarations',            
                'form' => $form->createView()   
                ]);
        }else{
            $declarations = $declarationRepository->findBy(['etat' => true]);
        }
        return $this->render('declaration/admin/index.html.twig', [
            'declarations' => $paginator->paginate($declarations,
            $request->query->getInt('page', 1),
            10),
            'titre' => 'Déclarations',            
            'form' => $form->createView()   
            ]);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param EntrepreneurRepository $entrepreneurRepository
     * @return void
     * @Route("/declaration/new", name="admin_declaration_new")
     * @IsGranted("ROLE_ADMIN")
     * 
     */
    public function newDeclaratiolAll(Request $request, EntrepreneurRepository $entrepreneurRepository){
        $entrepreneurs = $entrepreneurRepository->findAll();
        foreach ($entrepreneurs as $index => $entrepreneur) {
            $declaration = new Declaration();
            $form = $this->createForm(DeclarationAdminType::class, $declaration);
            $form->handleRequest($request);            
            $em = $this->getDoctrine()->getManager();
            if($form->isSubmitted() && $form->isValid()){
                $declaration
                    ->setEntrepreneur($entrepreneur)                    
                    ->setRef(uniqid('Dec-'))
                    ;
                $em->persist($declaration);
            }
            if ($index === array_key_last($entrepreneurs)){   
                $em->flush();
                $this->redirectToRoute('admin_index');
            }
        }        
    return $this->render('declaration/admin/new.html.twig', [
        'form' => $form->createView(),
    ]);
    }




// -----------------------------Controle Agent --------------------------------------------------------------

    /**
     * @Route("/agents/liste", name="agent_index_admin", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexAgent( Request $request, PaginatorInterface $paginator, AgentRepository $agentRepository): Response
    {
        $form = $this->createForm(SearchBarType::class, null, ['attr' => ['class' => 'd-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search']]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            $agentSearched = $agentRepository->search($request->request->get('search_bar')['text']);
            return $this->render('agent/admin/index.html.twig', [
                'agentss' => $agentSearched,
                'form' =>$form->createView()
            ]);
        }
        return $this->render('agent/admin/index.html.twig', [
            'agents' => $paginator->paginate($agentRepository->findAll(),
            $request->query->getInt('page', 1),
            10),
            'form' =>$form->createView()
        ]);
    }

    /**
     * @Route("/agent/new", name="agent_new_admin", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function newAgent(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $agent = new Agent();
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $agent->setEtat(true);
            $agent->setPassword($encoder->encodePassword($agent, $agent->getPassword()));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($agent);
            $entityManager->flush();

            return $this->redirectToRoute('agent_index_admin');
        }

        return $this->render('agent/admin/new.html.twig', [
            'agent' => $agent,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/agent/{slug}/verif", name="agent_verif_admin", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function verifAgent(Agent $agent)
    {
        $agent->setEtat(!$agent->getEtat());
        $this->getDoctrine()->getManager()->flush();
       return $this->redirectToRoute('agent_index_admin');
    }


     /**
     * @Route("/agent/{slug}", name="agent_show_admin", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function showAgent(Agent $agent): Response
    {
        return $this->render('agent/admin/show.html.twig', [
            'agent' => $agent,
        ]);
    }

    /**
     * @Route("/agent/{slug}/edit", name="agent_edit_admin", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editAgent(Request $request, Agent $agent): Response
    {
        $form = $this->createForm(AgentType::class, $agent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('agent_index');
        }

        return $this->render('agent/admin/edit.html.twig', [
            'agent' => $agent,
            'form' => $form->createView(),
        ]);
    }

//------------------ contole Facturation -------------------------------------------------
    /**
     * Undocumented function
     *@Route("/facture/liste", name="admin_facture_index")
     * @param FactureRepository $factureRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return void
     */
    public function getAllFactures(FactureRepository $factureRepository, PaginatorInterface $paginator, Request $request){

        $form = $this->createForm(FilterSearchType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $nom = $request->request->get('filter_search')['nom'];
            $dateStart = $request->request->get('filter_search')['dateStart'] ?: new \DateTime('1950-01-01') ;
            $dateEnd = $request->request->get('filter_search')['dateEnd'] ?:  new \DateTime();
            $facturess = $factureRepository->filter($nom, $dateStart, $dateEnd, 'facture');
            return $this->render('facture/admin/index.html.twig', [
                'factures' => '',
                'facturess' => $facturess,
                'titre' => 'Facture',            
                'form' => $form->createView()   
                ]);
        }else{
            $factures = $factureRepository->findBy(['type' => 'facture']);
        }
        return $this->render('facture/admin/index.html.twig', [
            'factures' => $paginator->paginate($factures,
            $request->query->getInt('page', 1),
            10),         
            'form' => $form->createView(),
            'titre' => 'Facture'
            ]);
    }

    /**
     * Undocumented function
     *@Route("/facture/devis", name="admin_devis_index")
     * @param FactureRepository $factureRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return void
     */
    public function getAllDevis(FactureRepository $factureRepository, PaginatorInterface $paginator, Request $request){

        $form = $this->createForm(FilterSearchType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $nom = $request->request->get('filter_search')['nom'];
            $dateStart = $request->request->get('filter_search')['dateStart'] ?: new \DateTime('1950-01-01') ;
            $dateEnd = $request->request->get('filter_search')['dateEnd'] ?:  new \DateTime();
            $facturess = $factureRepository->filter($nom, $dateStart, $dateEnd, 'devis');
            return $this->render('facture/admin/index.html.twig', [
                'factures' => '',
                'facturess' => $facturess,
                'titre' => 'Devis',            
                'form' => $form->createView()   
                ]);
        }else{
            $factures = $factureRepository->findBy(['type' => 'devis']);
        }
        return $this->render('facture/admin/index.html.twig', [
            'factures' => $paginator->paginate($factures,
            $request->query->getInt('page', 1),
            10),         
            'form' => $form->createView(),
            'titre' => 'Devis'
            ]);
    }

}
