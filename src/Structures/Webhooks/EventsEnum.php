<?php

namespace Garissman\Printify\Structures\Webhooks;

enum EventsEnum: string
{
    case ShopDisconnected = "shop:disconnected";
    case ProductDeleted = "product:deleted";
    case ProductPublishStarted = "product:publish:started";
    case OrderCreated = "order:created";
    case OrderUpdated = "order:updated";
    case OrderSentToProduction = "order:sent-to-production";
    case OrderShipmentCreated = "order:shipment:created";
    case OrderShipmentDelivered = "order:shipment:delivered";
}
