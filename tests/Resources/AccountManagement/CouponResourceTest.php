<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Resources\AccountManagement;

use Pisko\CardMarket\Resources\AccountManagement\CouponResource;
use Pisko\CardMarket\Tests\ResourceTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;

class CouponResourceTest extends ResourceTestCase
{
    private CouponResource $couponResource;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupHttpClientCreatorMock();
        $this->couponResource = new CouponResource($this->httpClientCreatorMock);
    }

    public function testRedeemSingleCoupon()
    {
        $response = $this->couponResource->redeemCoupons('TESTCOUPON123');

        $this->assertArrayHasKey('coupon', $response);
        $this->assertArrayHasKey('api', $response);
    }

    public function testRedeemMultipleCoupons()
    {
        $response = $this->couponResource->redeemCoupons(['COUPON1', 'COUPON2']);

        $this->assertArrayHasKey('coupon', $response);
        $this->assertIsArray($response['coupon']);
    }

    protected function getMockResponses(): array
    {
        $singleCoupon = json_encode([
            'coupon' => [
                'code' => 'TESTCOUPON123',
                'value' => 5.00,
                'currency' => 'EUR',
            ],
        ]);

        $multipleCoupons = json_encode([
            'coupon' => [
                ['code' => 'COUPON1', 'value' => 5.00, 'currency' => 'EUR'],
                ['code' => 'COUPON2', 'value' => 10.00, 'currency' => 'EUR'],
            ],
        ]);

        return [
            new MockResponse($singleCoupon, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 5,
                ],
            ]),
            new MockResponse($multipleCoupons, [
                'response_headers' => [
                    'X-Request-Limit-Max' => 5000,
                    'X-Request-Limit-Count' => 6,
                ],
            ]),
        ];
    }
}
