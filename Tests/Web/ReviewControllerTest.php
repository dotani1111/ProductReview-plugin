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

use Eccube\Entity\Master\Sex;
use Eccube\Entity\Product;
use Eccube\Repository\Master\SexRepository;
use Eccube\Repository\ProductRepository;
use Eccube\Tests\Web\AbstractWebTestCase;
use Faker\Generator;
use Plugin\ProductReview44\Entity\ProductReview;
use Plugin\ProductReview44\Entity\ProductReviewStatus;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ReviewControllerTest front.
 */
final class ReviewControllerTest extends AbstractWebTestCase
{
    protected ?Generator $faker = null;

    protected ?ProductRepository $productRepo = null;

    protected ?SexRepository $sexMasterRepo = null;

    /**
     * Setup method.
     */
    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = $this->getFaker();
        $this->deleteAllRows(['plg_product_review']);

        /** @var ProductRepository $productRepo */
        $productRepo = $this->entityManager->getRepository(Product::class);
        $this->productRepo = $productRepo;
        /** @var SexRepository $sexMasterRepo */
        $sexMasterRepo = $this->entityManager->getRepository(Sex::class);
        $this->sexMasterRepo = $sexMasterRepo;
    }

    /**
     * Add product review.
     */
    public function testProductReviewAddConfirmComplete(): void
    {
        $productId = 1;
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('product_review_index', ['id' => $productId]),
            [
                'product_review' => [
                    'comment' => $this->faker->text(2999),
                    'title' => $this->faker->word,
                    'sex' => 1,
                    'recommend_level' => $this->faker->numberBetween(1, 5),
                    'reviewer_url' => $this->faker->url,
                    'reviewer_name' => $this->faker->word,
                    '_token' => 'dummy',
                ],
                'mode' => 'confirm',
            ]
        );
        $this->assertStringContainsString('投稿する', $crawler->html());

        // Complete
        $form = $crawler->selectButton('投稿する')->form();
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('product_review_complete', ['id' => $productId])));

        // Verify back to product detail link.
        /**
         * @var Crawler
         */
        $crawler = $this->client->followRedirect();
        $link = $crawler->selectLink('商品ページへ戻る')->link();

        $this->actual = $link->getUri();

        $this->expected = $this->generateUrl('product_detail', ['id' => $productId], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->verify();
    }

    /**
     * Back test.
     */
    public function testProductReviewAddConfirmBack(): void
    {
        $productId = 1;
        $inputForm = [
            'comment' => $this->faker->text(2999),
            'title' => $this->faker->word,
            'sex' => 1,
            'recommend_level' => $this->faker->numberBetween(1, 5),
            'reviewer_url' => $this->faker->url,
            'reviewer_name' => $this->faker->word,
            '_token' => 'dummy',
        ];
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('product_review_index', ['id' => $productId]),
            ['product_review' => $inputForm,
                'mode' => 'confirm',
            ]
        );
        $this->assertStringContainsString('投稿する', $crawler->html());

        // Back click
        $form = $crawler->selectButton('戻る')->form();
        $crawlerConfirm = $this->client->submit($form);
        $html = $crawlerConfirm->html();
        $this->assertStringContainsString('確認ページへ', $html);

        // Verify data
        $this->assertStringContainsString($inputForm['comment'], $html);
    }

    /**
     * review list.
     */
    public function testProductReview(): void
    {
        $productId = 1;
        $ProductReview = $this->createProductReviewData($productId);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        $this->client->getResponse()->getStatusCode();

        // review area
        $this->assertStringContainsString('id="product_review_area"', $crawler->html());

        // review content
        $reviewArea = $crawler->filter('#product_review_area');
        $this->assertStringContainsString($ProductReview->getComment(), $reviewArea->html());

        // review total
        $totalNum = $reviewArea->filter('.ec-rectHeading')->html();
        $this->assertStringContainsString('(1)', $totalNum);
    }

    /**
     * review list.
     */
    public function testProductReviewMaxNumber(): void
    {
        $max = 31;
        $Product = $this->createProduct();
        $productId = $Product->getId();
        $this->createProductReviewByNumber($max, $productId);
        $crawler = $this->client->request(
            Request::METHOD_GET,
            $this->generateUrl('product_detail', ['id' => $productId])
        );

        // review area
        $this->assertStringContainsString('id="product_review_area"', $crawler->html());

        // review content
        $reviewArea = $crawler->filter('#product_review_area');

        // review total
        $totalHtml = $reviewArea->filter('.ec-rectHeading')->html();
        $this->assertStringContainsString((string) $max, $totalHtml);
    }

    private function createProductReviewByNumber(int $number, int $productId = 1): void
    {
        $Product = $this->productRepo->find($productId);
        if (!$Product) {
            $Product = $this->createProduct();
        }

        for ($i = 0; $i < $number; ++$i) {
            $this->createProductReviewData($Product);
        }
    }

    /**
     * Create data.
     */
    private function createProductReviewData(int|Product $product = 1): ProductReview
    {
        if ($product instanceof Product) {
            $Product = $product;
        } else {
            $Product = $this->productRepo->find($product);
        }

        $Display = $this->entityManager->find(ProductReviewStatus::class, ProductReviewStatus::SHOW);
        $Sex = $this->sexMasterRepo->find(1);
        $Customer = $this->createCustomer();

        $Review = new ProductReview();
        $Review->setComment($this->faker->word);
        $Review->setTitle($this->faker->word);
        $Review->setProduct($Product);
        $Review->setRecommendLevel($this->faker->numberBetween(1, 5));
        $Review->setReviewerName($this->faker->word);
        $Review->setReviewerUrl($this->faker->url);
        $Review->setStatus($Display);
        $Review->setSex($Sex);
        $Review->setCustomer($Customer);

        $this->entityManager->persist($Review);
        $this->entityManager->flush();

        return $Review;
    }
}
