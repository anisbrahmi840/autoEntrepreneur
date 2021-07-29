<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Form\AgentType;
use App\Form\AgentEditPasswordType;
use App\Repository\AgentRepository;
use Symfony\Component\Form\FormError;
use App\Form\EntrepreneurEditPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AgentController extends AbstractController
{
    /**
     * @Route("/agent/{slug}/editpassword", name="agent_editpassword", methods={"GET","POST"})
     */
    public function editPassword(Request $request, Agent $agent, UserPasswordEncoderInterface $encoder): Response
    {
        $oldPasswordBd = $agent->getPassword();
        $form = $this->createForm(AgentEditPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {  
            $oldPassword = $request->request->get('agent_edit_password')['oldPassword'];
            
            if(!password_verify($oldPassword, $oldPasswordBd)){
                $form->get('oldPassword')->addError(new FormError('Mot de passe incorrect!'));
            }else{
                $agent->setPassword($encoder->encodePassword($agent, $form->getData()['password']) );
                
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('admin_index');
            }
        }

        return $this->render('agent/editPassword.html.twig', [
            'agent' => $agent,
            'form' => $form->createView(),
        ]);
    }
}
