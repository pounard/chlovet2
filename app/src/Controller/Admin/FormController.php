<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\ControllerTrait;
use App\Security\FormClientTokenRepository;
use Goat\Runner\Runner;
use MakinaCorpus\Calista\Bridge\Symfony\DependencyInjection\ViewFactory;
use MakinaCorpus\Calista\Datasource\DatasourceInputDefinition;
use MakinaCorpus\Calista\View\ViewDefinition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class FormController extends AbstractController
{
    use ControllerTrait;

    /**
     * Liste des tokens client.
     */
    public function tokenList(Request $request, ViewFactory $viewFactory, Runner $runner): Response
    {
        $datasource = new FormClientTokenDatasource($runner);

        $inputDef = new DatasourceInputDefinition($datasource, [
            'limit_default' => 100,
            'pager_enable' => true,
            'sort_default_field' => 'created_at',
            'sort_default_order' => 'desc',
        ]);
        $viewDef = new ViewDefinition([
            'properties' => ['disable' => []],
            'show_filters' => true,
            'show_pager' => true,
            'show_sort' => true,
            'templates' => ['default' => 'gestion/form/token-list-calista.html.twig'],
            'view_type' => 'twig_page',
        ]);

        $query = $inputDef->createQueryFromRequest($request);

        return $this->render('gestion/form/token-list.html.twig', [
            'items' => $datasource->getItems($query),
            'query' => $query,
            'view' => $viewFactory->getView($viewDef->getViewType()),
            'viewDef' => $viewDef,
        ]);
    }

    /**
     * Ajouter un token client.
     */
    public function tokenAdd(Request $request, FormClientTokenRepository $tokenRepository): Response
    {
        $form = $this
            ->createFormBuilder()
            ->add('email', Form\EmailType::class, [
                'label' => "Adresse e-mail du client",
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "L'adresse e-mail du client est obligatoire.",
                    ])
                ],
            ])
            ->add('target', Form\ChoiceType::class, [
                'choices' => [
                    "Commémoratif" => 'commemoratif',
                ],
                'label' => "Formulaire cible",
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le formulaire cible est obligatoire.",
                    ])
                ],
            ])
            ->add('submit', Form\SubmitType::class, [
                'label' => "Ajouter",
            ])
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $input = $form->getData();

            try {
                $token = $tokenRepository->create($input['email'], $input['target']);

                $lien = $this->generateUrl('form_login', [
                    'token' => $token,
                    'form' => $input['target'],
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                $this->addFlash('success', "Lien de connexion créé avec success:<br/><code>" . $lien . "</code>");

                return $this->redirectToRoute('form_admin_token_add');

            } catch (\Throwable $e) {
                if ($this->isDebug()) {
                    throw $e;
                }

                $this->addFlash('error', "Une erreur est survenue");
            }
        }

        return $this->render('gestion/form/token-add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
