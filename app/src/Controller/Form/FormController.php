<?php

declare(strict_types=1);

namespace App\Controller\Form;

use App\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class FormController extends AbstractController
{
    use ControllerTrait;

    public function home(Request $request): Response
    {
        return $this->render('form/form/home.html.twig');
    }

    public function commemoratif(Request $request): Response
    {
        $form = $this
            ->createFormBuilder()
            ->add('email', Form\EmailType::class, [
                'label' => "Adresse e-mail du client",
                'required' => true,
            ])
            ->add('target', Form\ChoiceType::class, [
                'choices' => [
                    "Commémoratif" => 'commemoratif',
                ],
                'label' => "Formulaire cible",
                'required' => true,
            ])
            ->add('submit', Form\SubmitType::class, [
                'label' => "Ajouter",
            ])
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            // $input = $form->getData();

            try {
                $this->addFlash('success', "Merci d'avoir rempli ce formulaire, le cabinet vétérinaire Saint-Clément vous contactera dès que possible.");

                return $this->redirectToRoute('form_home');

            } catch (\Throwable $e) {
                if ($this->isDebug()) {
                    throw $e;
                }

                $this->addFlash('error', "Une erreur est survenue");
            }
        }

        return $this->render('form/form/commemoratif.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
