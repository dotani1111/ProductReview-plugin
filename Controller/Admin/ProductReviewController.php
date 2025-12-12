<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\ProductReview44\Controller\Admin;

use Eccube\Controller\AbstractController;
use Eccube\Repository\Master\PageMaxRepository;
use Eccube\Service\CsvExportService;
use Eccube\Util\FormUtil;
use Knp\Component\Pager\PaginatorInterface;
use Plugin\ProductReview44\Entity\ProductReview;
use Plugin\ProductReview44\Entity\ProductReviewConfig;
use Plugin\ProductReview44\Form\Type\Admin\ProductReviewSearchType;
use Plugin\ProductReview44\Form\Type\Admin\ProductReviewType;
use Plugin\ProductReview44\Repository\ProductReviewConfigRepository;
use Plugin\ProductReview44\Repository\ProductReviewRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class ProductReviewController admin.
 */
class ProductReviewController extends AbstractController
{
    /**
     * ProductReviewController constructor.
     */
    public function __construct(
        protected PageMaxRepository $pageMaxRepository,
        protected ProductReviewRepository $productReviewRepository,
        protected ProductReviewConfigRepository $productReviewConfigRepository,
        protected CsvExportService $csvExportService,
        private readonly PaginatorInterface $paginator
    )
    {
    }

    /**
     * Search function.
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/product_review/', name: 'product_review_admin_product_review')]
    #[Route(path: '/%eccube_admin_route%/product_review/page/{page_no}', name: 'product_review_admin_product_review_page', requirements: ['page_no' => '\d+'])]
    #[Template(template: '@ProductReview44/admin/index.twig')]
    public function index(Request $request, ?int $page_no = null): array
    {
        $CsvType = $this->productReviewConfigRepository
            ->get()
            ->getCsvType();
        $builder = $this->formFactory->createBuilder(ProductReviewSearchType::class);
        $searchForm = $builder->getForm();

        $pageMaxis = $this->pageMaxRepository->findAll();
        $pageCount = $this->session->get(
            'product_review.admin.product_review.search.page_count',
            $this->eccubeConfig['eccube_default_page_count']
        );
        $pageCountParam = $request->get('page_count');
        if ($pageCountParam && is_numeric($pageCountParam)) {
            foreach ($pageMaxis as $pageMax) {
                if ($pageCountParam == $pageMax->getName()) {
                    $pageCount = $pageMax->getName();
                    $this->session->set('product_review.admin.product_review.search.page_count', $pageCount);
                    break;
                }
            }
        }

        if ('POST' === $request->getMethod()) {
            $searchForm->handleRequest($request);
            if ($searchForm->isValid()) {
                $searchData = $searchForm->getData();
                $page_no = 1;

                $this->session->set('product_review.admin.product_review.search', FormUtil::getViewData($searchForm));
                $this->session->set('product_review.admin.product_review.search.page_no', $page_no);
            } else {
                return [
                    'searchForm' => $searchForm->createView(),
                    'pagination' => [],
                    'pageMaxis' => $pageMaxis,
                    'page_no' => $page_no,
                    'page_count' => $pageCount,
                    'CsvType' => $CsvType,
                    'has_errors' => true,
                ];
            }
        } else {
            if (null !== $page_no || $request->get('resume')) {
                if ($page_no) {
                    $this->session->set('product_review.admin.product_review.search.page_no', (int) $page_no);
                } else {
                    $page_no = $this->session->get('product_review.admin.product_review.search.page_no', 1);
                }
                $viewData = $this->session->get('product_review.admin.product_review.search', []);
            } else {
                $page_no = 1;
                $viewData = FormUtil::getViewData($searchForm);
                $this->session->set('product_review.admin.product_review.search', $viewData);
                $this->session->set('product_review.admin.product_review.search.page_no', $page_no);
            }
            $searchData = FormUtil::submitAndGetData($searchForm, $viewData);
        }

        $qb = $this->productReviewRepository->getQueryBuilderBySearchData($searchData);

        $pagination = $this->paginator->paginate(
            $qb,
            $page_no,
            $pageCount
        );

        return [
            'searchForm' => $searchForm->createView(),
            'pagination' => $pagination,
            'pageMaxis' => $pageMaxis,
            'page_no' => $page_no,
            'page_count' => $pageCount,
            'CsvType' => $CsvType,
            'has_errors' => false,
        ];
    }

    /**
     * 編集.
     *
     * @return array<string, mixed>|RedirectResponse
     */
    #[Route(path: '%eccube_admin_route%/product_review/{id}/edit', name: 'product_review_admin_product_review_edit')]
    #[Template(template: '@ProductReview44/admin/edit.twig')]
    public function edit(Request $request, ProductReview $ProductReview)
    {
        $Product = $ProductReview->getProduct();
        if ($Product === null) {
            $this->addError('product_review.admin.product.not_found', 'admin');

            return $this->redirectToRoute('product_review_admin_product_review', ['resume' => 1]);
        }

        $form = $this->createForm(ProductReviewType::class, $ProductReview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ProductReview = $form->getData();
            $this->entityManager->persist($ProductReview);
            $this->entityManager->flush();

            log_info('Product review edit');

            $this->addSuccess('product_review.admin.save.complete', 'admin');

            return $this->redirectToRoute(
                'product_review_admin_product_review_edit',
                ['id' => $ProductReview->getId()]
            );
        }

        return [
            'form' => $form->createView(),
            'Product' => $Product,
            'ProductReview' => $ProductReview,
        ];
    }

    /**
     * Product review delete function.
     */
    #[Route(path: '%eccube_admin_route%/product_review/{id}/delete', name: 'product_review_admin_product_review_delete', methods: ['DELETE'])]
    public function delete(ProductReview $ProductReview): RedirectResponse
    {
        $this->isTokenValid();

        $this->entityManager->remove($ProductReview);
        $this->entityManager->flush();
        $this->addSuccess('product_review.admin.delete.complete', 'admin');

        log_info('Product review delete', ['id' => $ProductReview->getId()]);

        return $this->redirectToRoute('product_review_admin_product_review_page', ['resume' => 1]);
    }

    /**
     * 商品レビューCSVの出力.
     */
    #[Route(path: '%eccube_admin_route%/product_review/download', name: 'product_review_admin_product_review_download')]
    public function download(Request $request): StreamedResponse
    {
        // タイムアウトを無効にする.
        set_time_limit(0);

        // sql loggerを無効にする.
        $em = $this->entityManager;
        $em->getConfiguration()->setSQLLogger();
        $response = new StreamedResponse();
        $response->setCallback(function () use ($request) {
            /** @var ProductReviewConfig $Config */
            $Config = $this->productReviewConfigRepository->get();
            $csvType = $Config->getCsvType();

            /* @var $csvService CsvExportService */
            $csvService = $this->csvExportService;

            /* @var $repo ProductReviewRepository */
            $repo = $this->productReviewRepository;

            // CSV種別を元に初期化.
            $csvService->initCsvType($csvType);

            // ヘッダ行の出力.
            $csvService->exportHeader();

            $session = $request->getSession();
            $searchForm = $this->createForm(ProductReviewSearchType::class);

            $viewData = $session->get('product_review.admin.product_review.search', []);
            $searchData = FormUtil::submitAndGetData($searchForm, $viewData);

            $qb = $repo->getQueryBuilderBySearchData($searchData);

            // データ行の出力.
            $csvService->setExportQueryBuilder($qb);
            $csvService->exportData(function ($entity, CsvExportService $csvService) {
                $arrCsv = $csvService->getCsvs();

                $row = [];
                // CSV出力項目と合致するデータを取得.
                foreach ($arrCsv as $csv) {
                    // 受注データを検索.
                    $data = $csvService->getData($csv, $entity);
                    $row[] = $data;
                }
                // 出力.
                $csvService->fputcsv($row);
            });
        });

        $now = new \DateTime();
        $filename = 'product_review_'.$now->format('YmdHis').'.csv';
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);

        log_info('商品レビューCSV出力ファイル名', [$filename]);

        return $response;
    }
}
