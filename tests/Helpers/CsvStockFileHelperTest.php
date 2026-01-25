<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Helpers\CsvStockFileHelper;

class CsvStockFileHelperTest extends TestCase
{
    private string $testCsvContent;

    private string $encodedContent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testCsvContent = "idArticle,idProduct,English Name,Count,Price\n1,100,Test Card,10,5.00\n2,101,Another Card,5,10.00";
        $this->encodedContent = base64_encode(gzencode($this->testCsvContent));
    }

    public function testCreateCsvStockFileHelper()
    {
        $helper = new CsvStockFileHelper($this->encodedContent);

        $this->assertInstanceOf(CsvStockFileHelper::class, $helper);
    }

    public function testStoreStockFileOnDisk()
    {
        $helper = new CsvStockFileHelper($this->encodedContent);
        $tempFile = sys_get_temp_dir() . '/test_stock_' . uniqid() . '.csv';

        $result = $helper->storeStockFileOnDisk($tempFile);

        $this->assertTrue($result);
        $this->assertFileExists($tempFile);

        $content = file_get_contents($tempFile);
        $this->assertStringContainsString('Test Card', $content);
        $this->assertStringContainsString('Another Card', $content);

        // Cleanup
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function testDecodeAndUnzipContent()
    {
        $helper = new CsvStockFileHelper($this->encodedContent);

        // Test through storing to disk and reading back
        $tempFile = sys_get_temp_dir() . '/test_decode_' . uniqid() . '.csv';
        $helper->storeStockFileOnDisk($tempFile);

        $decodedContent = file_get_contents($tempFile);

        $this->assertStringContainsString('idArticle', $decodedContent);
        $this->assertStringContainsString('English Name', $decodedContent);

        // Cleanup
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }
}
