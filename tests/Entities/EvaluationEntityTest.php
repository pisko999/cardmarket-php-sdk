<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Tests\Entities;

use PHPUnit\Framework\TestCase;
use Pisko\CardMarket\Entities\EvaluationEntity;

class EvaluationEntityTest extends TestCase
{
    public function testCreateEvaluationEntity()
    {
        $evaluation = new EvaluationEntity(
            EvaluationEntity::GRADE_VERY_GOOD,
            EvaluationEntity::GRADE_GOOD,
            EvaluationEntity::GRADE_VERY_GOOD,
            'Great seller!',
            [EvaluationEntity::COMPLAINT_BAD_COMMUNICATION],
        );

        $this->assertInstanceOf(EvaluationEntity::class, $evaluation);
    }

    public function testEvaluationEntityGetXML()
    {
        $evaluation = new EvaluationEntity(
            EvaluationEntity::GRADE_VERY_GOOD,
            EvaluationEntity::GRADE_VERY_GOOD,
            EvaluationEntity::GRADE_VERY_GOOD,
            'Excellent!',
            [],
        );

        $xml = $evaluation->getXML();

        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8" ?>', $xml);
        $this->assertStringContainsString('<request>', $xml);
        $this->assertStringContainsString('<evaluationGrade>1</evaluationGrade>', $xml);
        $this->assertStringContainsString('<comment>Excellent!</comment>', $xml);
        $this->assertStringContainsString('</request>', $xml);
    }

    public function testValidComplaintTypes()
    {
        $this->expectNotToPerformAssertions();

        new EvaluationEntity(
            EvaluationEntity::GRADE_VERY_GOOD,
            EvaluationEntity::GRADE_VERY_GOOD,
            EvaluationEntity::GRADE_VERY_GOOD,
            'Test',
            [
                EvaluationEntity::COMPLAINT_BAD_COMMUNICATION,
                EvaluationEntity::COMPLAINT_INCOMPLETE_SHIPMENT,
                EvaluationEntity::COMPLAINT_SHIP_DAMAGE,
            ],
        );
    }
}
