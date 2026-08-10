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
use Plugin\ProductReview44\Form\Type\Admin\ProductReviewConfigType;
use Plugin\ProductReview44\Repository\ProductReviewConfigRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class ConfigController.
 */
class ConfigController extends AbstractController
{
    public function __construct(private readonly ProductReviewConfigRepository $configRepository)
    {
    }

    /**
     * @return array<string, mixed>|RedirectResponse
     */
    #[Route(path: '/%eccube_admin_route%/product_review/config', name: 'product_review44_admin_config')]
    #[Template(template: '@ProductReview44/admin/config.twig')]
    public function index(Request $request): RedirectResponse|array
    {
        $Config = $this->configRepository->get();
        $form = $this->createForm(ProductReviewConfigType::class, $Config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Config = $form->getData();
            $this->entityManager->persist($Config);
            $this->entityManager->flush();

            log_info('Product review config', ['status' => 'Success']);
            $this->addSuccess('product_review.admin.save.complete', 'admin');

            return $this->redirectToRoute('product_review44_admin_config');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
