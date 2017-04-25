<?php
declare(strict_types=1);

namespace JDWil\Xsd\Event;

/**
 * Class EventDispatcher
 * @package JDWil\Xsd\Event
 */
class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var EventListenerInterface[]
     */
    private $listeners;

    public function __construct()
    {
        $this->listeners = [];
    }

    /**
     * @return EventDispatcher
     */
    public static function forNormalization(): EventDispatcher
    {
        $globPath = __dir__ . '/../Parser/Normalize/*.php';
        foreach (glob($globPath) as $file) {
            include_once $file;
        }

        $ret = new EventDispatcher();
        foreach (get_declared_classes() as $className) {
            if (strpos($className, 'JDWil\\Xsd\\Parser\\Normalize') === 0 &&
                strpos($className, 'Abstract') === false
            ) {
                if (strpos($className, 'FoundImportListener') !== false) {
                    $ret->registerListener(new $className($ret));
                } else {
                    $ret->registerListener(new $className);
                }
            }
        }

        return $ret;
    }

    /**
     * @param EventInterface $event
     * @return mixed
     */
    public function dispatch(EventInterface $event)
    {
        foreach ($this->listeners as $listener) {
            if ($listener->canHandle($event)) {
                $listener->handle($event);
                if ($event->propagationStopped()) {
                    return;
                }
            }
        }
    }

    /**
     * @param EventListenerInterface $eventListener
     * @return mixed
     */
    public function registerListener(EventListenerInterface $eventListener)
    {
        $this->listeners[] = $eventListener;
    }
}
