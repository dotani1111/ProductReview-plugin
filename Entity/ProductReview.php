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
use Eccube\Entity\Customer;
use Eccube\Entity\Master\Sex;
use Eccube\Entity\Product;
use Plugin\ProductReview44\Repository\ProductReviewRepository;

/**
 * ProductReview
 */
#[ORM\Table(name: 'plg_product_review')]
#[ORM\Entity(repositoryClass: ProductReviewRepository::class)]
class ProductReview extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'reviewer_name', type: Types::STRING)]
    private ?string $reviewer_name = null;

    #[ORM\Column(name: 'reviewer_url', type: Types::TEXT, nullable: true)]
    private ?string $reviewer_url = null;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 50)]
    private ?string $title = null;

    #[ORM\Column(name: 'comment', type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\JoinColumn(name: 'sex_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Sex::class)]
    private ?Sex $Sex = null;

    #[ORM\Column(name: 'recommend_level', type: Types::SMALLINT)]
    private ?int $recommend_level = null;

    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $Product = null;

    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Customer::class)]
    private ?Customer $Customer = null;

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

    #[ORM\JoinColumn(name: 'status_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: ProductReviewStatus::class)]
    private ?ProductReviewStatus $Status = null;

    /**
     * Get id.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get reviewer_name.
     */
    public function getReviewerName(): ?string
    {
        return $this->reviewer_name;
    }

    /**
     * Set reviewer_name.
     */
    public function setReviewerName(?string $reviewer_name): ProductReview
    {
        $this->reviewer_name = $reviewer_name;

        return $this;
    }

    /**
     * Get reviewer_url.
     */
    public function getReviewerUrl(): ?string
    {
        return $this->reviewer_url;
    }

    /**
     * Set reviewer_url.
     */
    public function setReviewerUrl(?string $reviewer_url): ProductReview
    {
        $this->reviewer_url = $reviewer_url;

        return $this;
    }

    /**
     * Get recommend_level.
     */
    public function getRecommendLevel(): ?int
    {
        return $this->recommend_level;
    }

    /**
     * Set recommend_level.
     */
    public function setRecommendLevel(?int $recommend_level): ProductReview
    {
        $this->recommend_level = $recommend_level;

        return $this;
    }

    /**
     * Set Sex.
     */
    public function setSex(?Sex $Sex = null): ProductReview
    {
        $this->Sex = $Sex;

        return $this;
    }

    /**
     * Get Sex.
     */
    public function getSex(): ?Sex
    {
        return $this->Sex;
    }

    /**
     * Get title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set title.
     */
    public function setTitle(?string $title): ProductReview
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get comment.
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * Set comment.
     */
    public function setComment(?string $comment): ProductReview
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Set Product.
     *
     * @return $this
     */
    public function setProduct(Product $Product)
    {
        $this->Product = $Product;

        return $this;
    }

    /**
     * Get Product.
     */
    public function getProduct(): ?Product
    {
        return $this->Product;
    }

    /**
     * Set Customer.
     *
     * @return $this
     */
    public function setCustomer(?Customer $Customer)
    {
        $this->Customer = $Customer;

        return $this;
    }

    /**
     * Get Customer.
     */
    public function getCustomer(): ?Customer
    {
        return $this->Customer;
    }

    public function getStatus(): ?ProductReviewStatus
    {
        return $this->Status;
    }

    public function setStatus(?ProductReviewStatus $Status): self
    {
        $this->Status = $Status;

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
