<?php

declare(strict_types=1);

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

namespace Plugin\ProductReview44\Tests\Web;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProductReviewConfigControllerTest.
 */
final class ProductReviewConfigControllerTest extends AbstractAdminWebTestCase
{
    protected ?Generator $faker = null;

    /**
     * Setup method.
     */
    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = $this->getFaker();
    }

    /**
     * Config routing.
     */
    public function testRouting(): void
    {
        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        /**
         * @var Crawler
         */
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('product_review44_admin_config'));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $min = $this->eccubeConfig['product_review_display_count_min'];
        $max = $this->eccubeConfig['product_review_display_count_max'];
        $this->assertStringContainsString('レビューの表示件数('.$min.'〜'.$max.')', $crawler->html());
    }

    /**
     * Config submit.
     */
    public function testMin(): void
    {
        $min = $this->eccubeConfig['product_review_display_count_min'];
        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        /**
         * @var Crawler
         */
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('product_review44_admin_config'));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('登録')->form();

        $form['product_review_config[review_max]'] = (string) $this->faker->numberBetween(-10, $min - 1);
        $crawler = $client->submit($form);

        $this->assertStringContainsString($min.'以上', (string) $crawler->html());
    }

    /**
     * Config submit.
     */
    public function testMax(): void
    {
        $max = $this->eccubeConfig['product_review_display_count_max'];
        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        /**
         * @var Crawler
         */
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('product_review44_admin_config'));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('登録')->form();

        $form['product_review_config[review_max]'] = (string) $this->faker->numberBetween($max + 1, 100);
        $crawler = $client->submit($form);

        $this->assertStringContainsString($max.'以下でなければなりません。', (string) $crawler->html());
    }

    /**
     * Config submit.
     */
    public function testSuccess(): void
    {
        $min = $this->eccubeConfig['product_review_display_count_min'];
        $max = $this->eccubeConfig['product_review_display_count_max'];
        /**
         * @var KernelBrowser
         */
        $client = $this->client;
        /**
         * @var Crawler
         */
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('product_review44_admin_config'));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $form = $crawler->selectButton('登録')->form();

        $form['product_review_config[review_max]'] = (string) $this->faker->numberBetween($min, $max);
        $crawler = $client->submit($form);

        $this->assertTrue($client->getResponse()->isRedirect($this->generateUrl('product_review44_admin_config')));

        $crawler = $client->followRedirect();
        $this->assertStringContainsString('登録しました。', (string) $crawler->html());
    }
}
