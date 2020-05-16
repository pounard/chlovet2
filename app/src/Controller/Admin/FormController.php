<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractAppController;
use App\Controller\ControllerTrait;
use App\Repository\FormDataRepository;
use App\Security\FormClientTokenRepository;
use Goat\Mapper\EntityManager;
use Goat\Mapper\Error\EntityDoesNotExistError;
use Goat\Runner\Runner;
use MakinaCorpus\Calista\Bridge\Symfony\DependencyInjection\ViewFactory;
use MakinaCorpus\Calista\Datasource\DatasourceInputDefinition;
use MakinaCorpus\Calista\View\ViewDefinition;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class FormController extends AbstractAppController
{
    use ControllerTrait;

    /**
     * Liste des tokens client.
     */
    public function formDataList(
        Request $request,
        ViewFactory $viewFactory,
        Runner $runner,
        EntityManager $entityManager
    ): Response {
        $datasource = new FormDataDatasource($entityManager);

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
            'templates' => ['default' => 'gestion/form/form-data-list-calista.html.twig'],
            'view_type' => 'twig_page',
        ]);

        $query = $inputDef->createQueryFromRequest($request);

        return $this->render('gestion/form/form-data-list.html.twig', [
            'items' => $datasource->getItems($query),
            'query' => $query,
            'view' => $viewFactory->getView($viewDef->getViewType()),
            'viewDef' => $viewDef,
        ]);
    }

    public function formDataView(string $id, FormDataRepository $repository): Response
    {
        try {
            $formData = $repository->findOne(Uuid::fromString($id));
        } catch (EntityDoesNotExistError $e) {
            throw $this->createNotFoundException();
        } catch (InvalidUuidStringException $e) {
            throw $this->createNotFoundException();
        }

        return $this->render('gestion/form/form-data-view.html.twig', [
            'formData' => $formData,
        ]);
    }

    /**
     * Liste des tokens client.
     */
    public function tokenList(
        Request $request,
        ViewFactory $viewFactory,
        Runner $runner,
        FormClientTokenRepository $tokenRepository
    ): Response {
        // @todo Later, display this into a dialog that does not refresh the page.
        if ($token = $request->get('view')) {
            $target = $tokenRepository->findTargetForToken($token);
            if ($target) {
                $lien = $this->generateLoginLink($token, $target);
                $this->addFlash('success', "Lien de connexion:<br/><code>" . $lien . "</code>");
            }

            return $this->redirectToRoute('form_admin_token_list');
        }

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
    public function tokenAdd(Request $request, FormClientTokenRepository $tokenRepository, LoggerInterface $logger): Response
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
                'choices' => \array_flip(\App\Entity\Form::getAll()),
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

                $lien = $this->generateLoginLink($token, $input['target']);
                $this->addFlash('success', "Lien de connexion créé avec success:<br/><code>" . $lien . "</code>");

                return $this->redirectToRoute('form_admin_token_add');

            } catch (\Throwable $e) {
                $this->handleError($e);
            }
        }

        return $this->render('gestion/form/token-add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function generateLoginLink(string $token, string $target): string
    {
        return $this->generateUrl(
            'form_login',
            [
                'token' => $token,
                'form' => $target,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
