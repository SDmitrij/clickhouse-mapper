<?php /** @noinspection PhpPropertyOnlyWrittenInspection */

namespace Entity;

use Mapper\Annotations\EmbeddedClass;
use Mapper\Annotations\Nullable;
use Mapper\Annotations\PropertyColumn;

/**
 * @EmbeddedClass
 */
class Status
{
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $statusProcessing;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $statusAccepted;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $statusShipped;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $statusDelivered;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $statusBuyout;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $statusReturn;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $statusSpam;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $statusCanceled;

    public function __construct(
        int $statusProcessing,
        int $statusAccepted,
        int $statusShipped,
        int $statusDelivered,
        int $statusBuyout,
        int $statusReturn,
        int $statusSpam,
        int $statusCanceled
    ) {
        $this->statusProcessing = $statusProcessing;
        $this->statusAccepted = $statusAccepted;
        $this->statusShipped = $statusShipped;
        $this->statusDelivered = $statusDelivered;
        $this->statusBuyout = $statusBuyout;
        $this->statusReturn = $statusReturn;
        $this->statusSpam = $statusSpam;
        $this->statusCanceled = $statusCanceled;
    }
}
