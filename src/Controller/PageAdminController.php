<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\PageRevision;
use App\Repository\PageRepository;
use MakinaCorpus\Calista\Bridge\Symfony\DependencyInjection\ViewFactory;
use MakinaCorpus\Calista\Datasource\DatasourceInputDefinition;
use MakinaCorpus\Calista\View\ViewDefinition;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageAdminController extends Controller
{
    use ControllerTrait;

    public function list(Request $request, ViewFactory $viewFactory, PageRepository $repository): Response
    {
        $datasource = new PageAdminDatasource($repository);

        $inputDef = new DatasourceInputDefinition($datasource, [
            'limit_default' => 30,
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
                'label' => "Text de la page",
                'required' => false,
            ])
        ;
    }

    public function append(Request $request, string $id): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        $revision = $this->repository->current($page->getId()) ?? PageRevision::create($page->getId());

        $form = $this
            ->createRevisionFormBuilder($revision)
            ->add('submit', Form\SubmitType::class, [
                'label' => "Enregistrer",
            ])
            ->getForm()
            ->handleRequest($request)
        ;

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

    public function revisions(Request $request, string $id): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        throw new \Exception("Not implemented yet");
    }

    public function setCurrent(string $id, int $revision): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        throw new \Exception("Not implemented yet");
    }

    public function delete(string $id, int $revision): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        throw new \Exception("Not implemented yet");
    }
}
