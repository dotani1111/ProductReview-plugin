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

namespace Plugin\ProductReview44;

use Eccube\Entity\Product;
use Eccube\Event\TemplateEvent;
use Eccube\Repository\Master\ProductStatusRepository;
use Plugin\ProductReview44\Entity\ProductReviewStatus;
use Plugin\ProductReview44\Repository\ProductReviewConfigRepository;
use Plugin\ProductReview44\Repository\ProductReviewRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductReviewEvent implements EventSubscriberInterface
{
    /**
     * ProductReview constructor.
     */
    public function __construct(protected ProductReviewConfigRepository $productReviewConfigRepository, protected ProductStatusRepository $productStatusRepository, protected ProductReviewRepository $productReviewRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Product/detail.twig' => 'detail',
        ];
    }

    public function detail(TemplateEvent $event): void
    {
        $event->addSnippet('ProductReview44/Resource/template/default/review.twig');

        $Config = $this->productReviewConfigRepository->get();

        /** @var Product $Product */
        $Product = $event->getParameter('Product');

        $reviewMax = $Config?->getReviewMax() ?? 5;
        $ProductReviews = $this->productReviewRepository->findBy(['Status' => ProductReviewStatus::SHOW, 'Product' => $Product], ['id' => 'DESC'], $reviewMax);

        $rate = $this->productReviewRepository->getAvgAll($Product);
        $avg = round($rate['recommend_avg']);
        $count = intval($rate['review_count']);

        $parameters = $event->getParameters();
        $parameters['ProductReviews'] = $ProductReviews;
        $parameters['ProductReviewAvg'] = $avg;
        $parameters['ProductReviewCount'] = $count;
        $event->setParameters($parameters);
    }
}
