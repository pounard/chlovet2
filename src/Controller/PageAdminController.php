<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PageRevision;
use MakinaCorpus\Calista\Bridge\Symfony\DependencyInjection\ViewFactory;
use MakinaCorpus\Calista\Datasource\DatasourceInputDefinition;
use MakinaCorpus\Calista\View\ViewDefinition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageAdminController extends AbstractController
{
    use ControllerTrait;

    public function list(Request $request, ViewFactory $viewFactory): Response
    {
        $datasource = new PageAdminListDatasource($this->repository);

        $inputDef = new DatasourceInputDefinition($datasource, [
            'limit_default' => 20,
            'pager_enable' => true,
            'sort_default_field' => 'p.created_at',
            'sort_default_order' => 'desc',
        ]);
        $viewDef = new ViewDefinition([
            'properties' => ['disable' => []],
            'show_filters' => true,
            'show_pager' => true,
            'show_sort' => true,
            'templates' => ['default' => 'gestion/page/list-calista.html.twig'],
            'view_type' => 'twig_page',
        ]);

        $query = $inputDef->createQueryFromRequest($request);

        return $this->render('gestion/page/list.html.twig', [
            'items' => $datasource->getItems($query),
            'query' => $query,
            'view' => $viewFactory->getView($viewDef->getViewType()),
            'viewDef' => $viewDef,
        ]);
    }

    public function revisions(Request $request, ViewFactory $viewFactory, string $id): Response
    {
        if (!$page = $this->repository->info($id = $this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        $datasource = new PageAdminRevisionsDatasource($this->repository);

        $inputDef = new DatasourceInputDefinition($datasource, [
            'base_query' => ['id' => [(string)$id]],
            'limit_default' => 30,
            'pager_enable' => true,
            'sort_default_field' => 'pr.created_at',
            'sort_default_order' => 'desc',
        ]);
        $viewDef = new ViewDefinition([
            'properties' => ['disable' => []],
            'show_filters' => true,
            'show_pager' => true,
            'show_sort' => true,
            'templates' => ['default' => 'gestion/page/revisions-calista.html.twig'],
            'view_type' => 'twig_page',
        ]);

        $query = $inputDef->createQueryFromRequest($request);

        return $this->render('gestion/page/revisions.html.twig', [
            'items' => $datasource->getItems($query),
            'page' => $page,
            'query' => $query,
            'view' => $viewFactory->getView($viewDef->getViewType()),
            'viewDef' => $viewDef,
        ]);
    }

    private function createRevisionFormBuilder(PageRevision $revision): FormBuilderInterface
    {
        return $this
            ->createFormBuilder()
            ->add('title', Form\TextType::class, [
                'data' => $revision->getTitle(),
                'label' => "Titre de la page",
                'required' => true,
            ])
            ->add('teaser', Form\TextareaType::class, [
                'attr' => ['data-editor' => "true"],
                'data' => $revision->get('teaser', [])['value'] ?? null,
                'label' => "Introduction",
                'required' => false,
            ])
            ->add('body', Form\TextareaType::class, [
                'attr' => ['data-editor' => "true"],
                'data' => $revision->get('body', [])['value'] ?? null,
                'label' => "Texte de la page",
                'required' => false,
            ])
            ->add('biblio', Form\TextareaType::class, [
                'attr' => ['data-editor' => "true"],
                'data' => $revision->get('biblio', [])['value'] ?? null,
                'label' => "Bibliographie",
                'required' => false,
            ])
            ->add('children_display', Form\ChoiceType::class, [
                'choices' => [
                    "Ne pas afficher" => null,
                ],
                'data' => $revision->get('display', [])['children'] ?? null,
                'label' => "Affichage des enfants",
                'required' => true,
            ])
        ;
    }

    public function append(Request $request, string $id): Response
    {
        if (!$page = $this->repository->info($id = $this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        $revision = $this->repository->current($id) ?? PageRevision::create($id);

        $form = $this
            ->createRevisionFormBuilder($revision)
            ->add('submit', Form\SubmitType::class, [
                'label' => "Enregistrer",
            ])
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $input = $form->getData();

            $data = $revision->getData();
            $data['teaser'] = [
                'value' => $input['teaser'] ?? '',
                'format' => 'full_html',
            ];
            $data['body'] = [
                'value' => $input['body'] ?? '',
                'format' => 'full_html',
            ];

            try {
                $this->repository->append($id, $input['title'], $data);

                return $this->redirectToRoute('page', ['id' => $id]);

            } catch (\Throwable $e) {
                if ($this->isDebug()) {
                    throw $e;
                }

                $this->addFlash('error', "Une erreur est survenue");
            }
        }

        return $this->render('gestion/page/edit.html.twig', [
            'form' => $form->createView(),
            'page' => $page,
            'revision' => $revision,
        ]);
    }

    public function create(Request $request): Response
    {
        throw new \Exception("Not implemented yet");
    }

    public function setCurrent(string $id, int $revision): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        throw new \Exception("Not implemented yet");
    }

    public function delete(Request $request, string $id): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        $form = $this
            ->createFormBuilder()
            ->add('are_you_sure', Form\CheckboxType::class, [
                'label' => "Veuillez cocher cette case si vous êtes sûr de vouloir supprimer cette page",
                'required' => true,
            ])
            ->add('submit', Form\SubmitType::class, [
                'attr' => ['class' => 'btn btn-danger'],
                'label' => "Supprimer",
            ])
            ->getForm()
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->repository->delete($page->getId());

                $this->addFlash('success', "La page a été supprimée.");

                return $this->redirectToRoute('page_admin_list');

            } catch (\Exception $e) {
                if ($this->isDebug()) {
                    throw $e;
                }

                $this->addFlash('error', "Une erreur est survenue, merci de réessayer plus tard.");
            }
        }

        return $this->render('gestion/page/delete.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }
}
