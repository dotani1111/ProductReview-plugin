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

namespace Plugin\ProductReview44\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\AbstractMasterEntity;
use Plugin\ProductReview44\Repository\ProductReviewStatusRepository;

/**
 * ProductReviewStatus
 */
#[ORM\Table(name: 'plg_product_review_status')]
#[ORM\Entity(repositoryClass: ProductReviewStatusRepository::class)]
class ProductReviewStatus extends AbstractMasterEntity
{
    /**
     * 表示
     */
    public const SHOW = 1;

    /**
     * 非表示
     */
    public const HIDE = 2;
}
