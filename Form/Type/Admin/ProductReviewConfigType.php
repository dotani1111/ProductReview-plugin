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

namespace Plugin\ProductReview44\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Plugin\ProductReview44\Entity\ProductReviewConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ProductReviewConfigType.
 */
class ProductReviewConfigType extends AbstractType
{
    /**
     * ProductReviewConfigType constructor.
     */
    public function __construct(protected EccubeConfig $eccubeConfig)
    {
    }

    /**
     * Build form.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $min = $this->eccubeConfig['product_review_display_count_min'];
        $max = $this->eccubeConfig['product_review_display_count_max'];

        $builder
            ->add('review_max', IntegerType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(['min' => $min, 'max' => $max]),
                ],
            ]);
    }

    /**
     * Config.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductReviewConfig::class,
        ]);
    }
}
