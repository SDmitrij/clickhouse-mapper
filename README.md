# Маппер объектов для ClickHouse
***
```php
/**
 * @CommonInfo(tableName="order_history")
 */
class Order
{
    /**
     * @PropertyColumn
     * @CustomEvaluationRule(rule="dateTimeToString", class="Entity\Rules\DateTimeEvalRule")
     */
    private DateTime $createdAt;
    /**
     * @PropertyColumn
     */
    private int $orderId;
    /**
     * @PropertyColumn
     */
    private int $personalId;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $isCallEvent = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $isStatusChanged = null;
    /**
     * @PropertyColumn
     */
    private int $countryId;
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
     */
    private int $offerId;
    /**
     * @PropertyColumn
     * @CustomEvaluationRule(rule="dateTimeToString", class="Entity\Rules\DateTimeEvalRule")
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
     * @PropertyColumn
     * @Nullable
     */
    private ?string $city = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?float $price = null;
    /**
     * @PropertyColumn
     * @Nullable
     */
    private ?int $quantity = null;
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
```
***
В примере выше указана некоторая сущность Order и аннотации маппера к ней,
данная сущность имеет вложеннный объект типа Status (помечен аннотацией Embedded):
```php
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
```
***
Давайте соберем наш объект типа Order:
```php
$order = new Order(
    new DateTime('-5 days'),
    new DateTime(),
    10,
    20,
    30,
    40,
    new Status(
        0,
        1,
        0,
        0,
        0,
        0,
        0,
        0
    )
)
```

Теперь, чтобы посчитать и добавить данный объект на запись в ClickHouse, с учетом вложенной сущности Status, достаточно вызвать 
менеджер сущностей:

```php
 // Вызываем менеджер сущностей
 $entityManager = Mapper::getEntityManager();
 // Добавляем нашу сущность $order созданную выше
 $entityManager->attach($order);
 // Для записи в ClickHouse
 $entityManager->release();
```
Внутри метода attach будет произведена обработка сущности с учетом всех аннотаций.

В маппере присутствует 6 типов аннотаций:

1. @CommonInfo(tableName="some_table",viewName="some_view") (Общая информация о сущности, название таблицы и view) 

2. @CustomEvaluationRule(rule="convertDateTime",class="Utils\DateTimeConverter") (Правило переcчета поля сущности, например перед записью в ClickHouse перевести DateTime в строку)

3. @EmbeddedClass (Маркер вложенной сущности)

4. @EmbeddedProperty(embeddedClass="Entity\SomeEmbedded") (Поле ссылается на вложенную сущность)

5. @Nullable (Поле класса и таблица в ClickHouse имеет тип Nullable)

6. @PropertyColumn(tableColumn="occurred_at",viewColumn="occurred") (Соответствие между полем сущности и таблицей в ClickHouse, если не указаны параметры в конструкторе аннотации то по-умолчанию берется название поля сущности через "snake case")