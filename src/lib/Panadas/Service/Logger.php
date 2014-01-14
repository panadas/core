<?php
namespace Panadas\Service;

class Logger extends \Panadas\ServiceAbstract
{

    private $logger;

    public function __construct(\Panadas\App $app, \Panadas\Module\LoggerAbstract $logger)
    {
        parent::__construct($app);

        $this->setLogger($logger);
    }

    public function __toArray()
    {
        $logger = $this->getLogger();

        if ($logger instanceof \Panadas\Module\Logger\Buffered) {
            $message = $logger->getAll();
        } else {
            $messages = [];
        }

        return (
            parent::__toArray()
            + [
                "start_timestamp" => $logger->getStartTimestamp(),
                "messages" => $messages
            ]
        );
    }

    public function __subscribe()
    {
        $events = parent::__subscribe();

        $events["forward"][] = function(\Panadas\Event $event) {
            $this->info("Forwarding to action: {$event->get("action_name")}");
        };

        if ($this->getApp()->isDebugMode()) {

            $events["send"][] = function(\Panadas\Event $event) {

                $event->get("response")->set("logger", $this->__toArray());

            };

        }

        return $events;
    }

    public function getLogger()
    {
    	return $this->logger;
    }

    protected function setLogger(\Panadas\Module\LoggerAbstract $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    public function debug($message, $timer = null)
    {
        $this->getLogger()->debug($message, $timer);

        return $this;
    }

    public function info($message, $timer = null)
    {
        $this->getLogger()->info($message, $timer);

        return $this;
    }

    public function warn($message, $timer = null)
    {
        $this->getLogger()->warn($message, $timer);

        return $this;
    }

    public function error($message, $timer = null)
    {
        $this->getLogger()->error($message, $timer);

        return $this;
    }

}
