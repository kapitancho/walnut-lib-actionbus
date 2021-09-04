# PHP Action Bus
This is library that can be used as a Command Bus, Query Bus or any other Bus type.

## Examples
In the examples below the following classes are used:
 
- For adding a product:
```php
/**
 * @implements Action<bool>
 */
final class AddProductAction implements Action {
   public function __construct(
      public /*readonly*/ string $productId,
      public /*readonly*/ int $amount
   ) {}
}

/**
 * @implements ActionHandler<bool, AddProductAction>
 */
final class AddProductActionHandler implements ActionHandler {
   /**
    * @param AddProductAction $action
    * @return bool
    */
   public function execute(Action $action): bool {
      return true; //@TODO - implement
   }
}
```
- For retrieving a product:
```php
final class ProductData {
   public function __construct(
      public /*readonly*/ string $productId,
      public /*readonly*/ string $productName,
      public /*readonly*/ int $amount
   ) {}
}

/**
 * @implements Action<ProductData>
 */
final class GetProductAction implements Action {
   public function __construct(
      public /*readonly*/ string $productId
   ) {}
}

/**
 * @implements ActionHandler<ProductData, GetProductAction>
 */
final class GetProductActionHandler implements ActionHandler {
   /**
    * @param GetProductAction $action
    * @return bool
    */
   public function execute(Action $action): ProductData {
      return new ProductData('p1', 'Test Product', 10); //@TODO - implement
   }
}
```

### Basic Example (Explicit Mapping)

```php
use Walnut\Lib\ActionBus\ActionBus;
use Walnut\Lib\ActionBus\Handler\DefaultActionHandler;
use Walnut\Lib\ActionBus\Handler\Mapper\ArrayActionHandlerMapper;
use Walnut\Lib\ActionBus\Handler\Loader\ContainerActionHandlerLoader;

$defaultActionHandler = new DefaultActionHandler(
    new ArrayActionHandlerMapper([
        AddProductAction::class => AddProductActionHandler::class,
        GetProductAction::class => GetProductActionHandler::class,
    ]),
    new ContainerActionHandlerLoader(getPsrContainer())
);

$actionBus = new ActionBus($defaultActionHandler);
$result = $actionBus->execute(new AddProductAction('p2', 5));

$productName = $actionBus->execute(new GetProductAction('p2'))->productName;
```

### Basic Example (Implicit Mapping)
```php
use Walnut\Lib\ActionBus\ActionBus;
use Walnut\Lib\ActionBus\Handler\DefaultActionHandler;
use Walnut\Lib\ActionBus\Handler\Mapper\NameActionHandlerMapper;
use Walnut\Lib\ActionBus\Handler\Loader\ContainerActionHandlerLoader;

$defaultActionHandler = new DefaultActionHandler(
    new NameActionHandlerMapper(),
    new ContainerActionHandlerLoader(getPsrContainer())
);

$actionBus = new ActionBus($defaultActionHandler);
$result = $actionBus->execute(new AddProductAction('p2', 5));

$productName = $actionBus->execute(new GetProductAction('p2'))->productName;
```

### Using middlewares
```php
use Walnut\Lib\ActionBus\Handler\DefaultActionHandler;
use Walnut\Lib\ActionBus\Handler\Mapper\NameActionHandlerMapper;
use Walnut\Lib\ActionBus\Handler\Loader\ContainerActionHandlerLoader;

$defaultActionHandler = new DefaultActionHandler(
    new NameActionHandlerMapper(),
    new ContainerActionHandlerLoader(getPsrContainer())
);

final class TransactionMiddleware implements ActionHandlerMiddleware {

   public function __construct(
      private /*readonly*/ TransactionContext $transactionContext
   ) {}

   /**
    * @throws Exception
    */
   public function process(Action $action, ActionHandler $actionHandler): mixed {
      try {
         $result = $actionHandler->execute($action);
         $this->transactionContext->saveChanges();
         return $result;
      } catch (Exception $e) {
         $this->transactionContext->revertChanges();
         throw $e;
      }
   }
}

final class ErrorLoggerMiddleware implements ActionHandlerMiddleware {

   public function __construct(
      private /*readonly*/ LoggerInterface $logger
   ) {}

   /**
    * @throws Throwable
    */
   public function process(Action $action, ActionHandler $actionHandler): mixed {
      try {
         return $actionHandler->execute($action);
      } catch (Throwable $e) {
         $this->logger->error($e);
         throw $e;
      }
   }
}

$actionBus = new ActionBus($defaultActionHandler, [
   new TransactionMiddleware(getTransactionContext()),
   new ErrorLoggerMiddleware(getPsrLogger())
]);
$result = $actionBus->execute(new AddProductAction('p2', 5));
```
