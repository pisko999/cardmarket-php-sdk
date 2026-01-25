<?php

declare(strict_types=1);

namespace Pisko\CardMarket\Entities;

class EvaluationEntity extends BaseEntity
{
    private int $evaluationGrade = self::GRADE_VERY_GOOD;

    private int $itemDescription = self::GRADE_VERY_GOOD;

    private int $packaging = self::GRADE_VERY_GOOD;

    private string $comment = '';

    private array $complaints = [];

    // Order complaint types
    public const COMPLAINT_BAD_COMMUNICATION = 'badCommunication';

    public const COMPLAINT_INCOMPLETE_SHIPMENT = 'incompleteShipment';

    public const COMPLAINT_REFUSED_CANCEL = 'refusedCancel';

    public const COMPLAINT_SHIP_DAMAGE = 'shipDamage';

    public const COMPLAINT_UNORDERED_SHIPMENT = 'unorderedShipment';

    public const COMPLAINT_WRONG_ED = 'wrongEd';

    public const COMPLAINT_WRONG_LANG = 'wrongLang';

    public const COMPLAINT_WRONG_GRADING = 'wrongGrading';

    public const COMPLAINT_REFUND_ISSUED = 'refundIssued';

    // Order grades
    public const GRADE_VERY_GOOD = 1; // very good

    public const GRADE_GOOD = 2; // good

    public const GRADE_NEUTRAL = 3; // neutral

    public const GRADE_BAD = 4; // bad

    public const GRADE_NA = 10; // n/a

    /**
     * Constructor.
     *
     * @param int $evaluationGrade Evaluation grade constant
     * @param int $itemDescription Item description grade
     * @param int $packaging Packaging grade
     * @param string $comment Comment
     * @param array<string> $complaints Array of complaint codes
     */
    public function __construct(
        int $evaluationGrade,
        int $itemDescription,
        int $packaging,
        string $comment,
        array $complaints,
    ) {
        if (!in_array($evaluationGrade, [self::GRADE_VERY_GOOD, self::GRADE_GOOD, self::GRADE_NEUTRAL, self::GRADE_BAD, self::GRADE_NA])) {
            throw new \InvalidArgumentException(sprintf('Invalid evaluation grade "%s".', $evaluationGrade));
        }
        if (!in_array($itemDescription, [self::GRADE_VERY_GOOD, self::GRADE_GOOD, self::GRADE_NEUTRAL, self::GRADE_BAD, self::GRADE_NA])) {
            throw new \InvalidArgumentException(sprintf('Invalid item description grade "%s".', $itemDescription));
        }
        if (!in_array($packaging, [self::GRADE_VERY_GOOD, self::GRADE_GOOD, self::GRADE_NEUTRAL, self::GRADE_BAD, self::GRADE_NA])) {
            throw new \InvalidArgumentException(sprintf('Invalid packaging grade "%s".', $packaging));
        }
        if (!empty($complaints)) {
            foreach ($complaints as $complaint) {
                if (!in_array($complaint, [
                    self::COMPLAINT_BAD_COMMUNICATION,
                    self::COMPLAINT_INCOMPLETE_SHIPMENT,
                    self::COMPLAINT_REFUSED_CANCEL,
                    self::COMPLAINT_SHIP_DAMAGE,
                    self::COMPLAINT_UNORDERED_SHIPMENT,
                    self::COMPLAINT_WRONG_ED,
                    self::COMPLAINT_WRONG_LANG,
                    self::COMPLAINT_WRONG_GRADING,
                    self::COMPLAINT_REFUND_ISSUED,
                ])) {
                    throw new \InvalidArgumentException(sprintf('Invalid complaint type "%s".', $complaint));
                }
            }
        }
        parent::__construct();
        $this->evaluationGrade = $evaluationGrade;
        $this->itemDescription = $itemDescription;
        $this->packaging = $packaging;
        $this->comment = $comment;
        $this->complaints = $complaints;
    }

    /**
     * Return entity as XML.
     *
     * @return string
     */
    public function getPureXML(): string
    {
        $complaints = [];
        foreach ($this->complaints as $complaint) {
            $complaints[] = '<complaint>' . htmlspecialchars($complaint, ENT_XML1, 'UTF-8') . '</complaint>';
        }

        return '<evaluationGrade>' . $this->evaluationGrade . '</evaluationGrade>' .
               '<itemDescription>' . $this->itemDescription . '</itemDescription>' .
               '<packaging>' . $this->packaging . '</packaging>' .
               '<comment>' . htmlspecialchars($this->comment, ENT_XML1, 'UTF-8') . '</comment>' .
               implode('', $complaints);
    }
}
