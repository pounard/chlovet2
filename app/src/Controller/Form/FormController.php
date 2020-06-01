<?php

declare(strict_types=1);

namespace App\Controller\Form;

use App\Controller\ControllerTrait;
use App\Controller\Form\Type\CommemoratifFormType;
use App\Entity\Form;
use App\Repository\FormDataRepository;
use App\Security\FormClientUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

final class FormController extends AbstractController
{
    use ControllerTrait;

    const GROUP_GENERAL_DEJA_CONSULTE = 'GeneralDejaConsulte';

    public function home(Request $request): Response
    {
        return $this->render('form/form/home.html.twig');
    }

    public function commemoratif(Request $request, FormDataRepository $repository, Security $security): Response
    {
        $form = $this
            ->createFormBuilder()
            ->add('data', CommemoratifFormType::class, [
                'html5' => true,
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer",
            ])
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->addFlash(
                    'success',
                    <<<TXT
                    Merci d'avoir rempli ce formulaire, le cabinet vétérinaire
                    Nantes Saint-Clément vous contactera dès que possible.
                    TXT
                );

                $user = $security->getUser();
                \assert($user instanceof FormClientUser);

                $data = FormHelper::cleanupPostData($form->getData());
                $humanReadableVersion = FormHelper::humanReadableFormData($form, $data);

                $repository->insert(
                    Form::TYPE_COMMEMORATIF,
                    $data,
                    $humanReadableVersion,
                    $user->getClientId()
                );

                return $this->redirectToRoute('form_home');

            } catch (\Throwable $e) {
                if ($this->isDebug()) {
                    throw $e;
                }

                $this->addFlash(
                    'error',
                    <<<TXT
                    Une erreur est survenue, veuillez ré-essayer plus tard.
                    Si le problème persiste, contactez le cabinet vétérinaire
                    Nantes Saint-Clément par mail ou par téléphone.
                    TXT
                );
            }
        }

        return $this->render('form/form/commemoratif.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
