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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Master\CsvType;
use Plugin\ProductReview44\Repository\ProductReviewConfigRepository;

/**
 * ProductReviewConfig
 */
#[ORM\Table(name: 'plg_product_review_config')]
#[ORM\Entity(repositoryClass: ProductReviewConfigRepository::class)]
class ProductReviewConfig extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'review_max', type: Types::SMALLINT, nullable: true, options: ['unsigned' => true, 'default' => 5])]
    private ?int $review_max = null;

    #[ORM\JoinColumn(name: 'csv_type_id', nullable: true, referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: CsvType::class)]
    private ?CsvType $CsvType = null;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private $create_date;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private $update_date;

    /**
     * Set product_review config id.
     */
    public function setId(int $id): ProductReviewConfig
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get ReviewMax.
     */
    public function getReviewMax(): ?int
    {
        return $this->review_max;
    }

    /**
     * Set max.
     */
    public function setReviewMax(?int $max): ProductReviewConfig
    {
        $this->review_max = $max;

        return $this;
    }

    /**
     * Get CsvType
     */
    public function getCsvType(): ?CsvType
    {
        return $this->CsvType;
    }

    /**
     * Set CsvType
     *
     * @return $this
     */
    public function setCsvType(?CsvType $CsvType = null)
    {
        $this->CsvType = $CsvType;

        return $this;
    }

    /**
     * Set create_date.
     *
     * @return $this
     */
    public function setCreateDate(\DateTime $createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date.
     */
    public function getCreateDate(): \DateTime
    {
        return $this->create_date;
    }

    /**
     * Set update_date.
     *
     * @return $this
     */
    public function setUpdateDate(\DateTime $updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date.
     */
    public function getUpdateDate(): \DateTime
    {
        return $this->update_date;
    }
}
