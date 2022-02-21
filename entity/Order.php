<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Entity;

use DateTime;
use Mapper\Annotations\CommonInfo;
use Mapper\Annotations\CustomEvaluationRule;
use Mapper\Annotations\EmbeddedProperty;
use Mapper\Annotations\Nullable;
use Mapper\Annotations\PropertyColumn;

/**
 * @CommonInfo(tableName="order_history")
 */
class Order
{
    /**
     * @PropertyColumn
     * @CustomEvaluationRule(rule="dateTimeToString", class="Mapper\Helper\StringHelper")
     */
    private DateTime $createdAt;
    /**
     * @PropertyColumn
     */
    private int $orderId;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $personalId = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $isCallEvent = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $countryId = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?float $bill = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?string $currency = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?float $exchangedUsdBill = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $offerId = null;
    /**
     * @PropertyColumn
     * @CustomEvaluationRule(rule="dateTimeToString", class="Mapper\Helper\StringHelper")
     */
    private DateTime $occurredAt;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?string $webmasterLanding = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $webmasterId = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $webmasterSource = null;
    /**
     * @EmbeddedProperty(embeddedClass="Entity\Status")
     */
    private Status $status;

    public function __construct(
        DateTime $createdAt,
        DateTime $occurredAt,
        int $orderId,
        int $personalId,
        int $countryId,
        int $offerId,
        Status $status
    ) {
        $this->createdAt = $createdAt;
        $this->occurredAt = $occurredAt;
        $this->orderId = $orderId;
        $this->personalId = $personalId;
        $this->countryId = $countryId;
        $this->offerId = $offerId;
        $this->status = $status;
    }
}
