<?php


namespace SIVI\AFD\Processors\Message;

use SIVI\AFD\Models\Message;
use SIVI\AFD\Processors\Entity\Contracts\EntityProcessor;
use SIVI\AFD\Processors\Message\Contracts\MessageProcessor as MessageProcessorContract;
use SIVI\AFD\Resolvers\Contracts\MessageImplementationResolver;

class MessageProcessor implements MessageProcessorContract
{
    /**
     * @var EntityProcessor
     */
    protected $entityProcessor;
    /**
     * @var MessageImplementationResolver
     */
    protected $messageImplementationResolver;

    /**
     * MessageProcessor constructor.
     * @param EntityProcessor $entityProcessor
     * @param array $messageImplementations
     */
    public function __construct(
        EntityProcessor $entityProcessor,
        MessageImplementationResolver $messageImplementationResolver
    ) {
        $this->entityProcessor = $entityProcessor;
        $this->messageImplementationResolver = $messageImplementationResolver;
    }

    public function process(Message $message): Message
    {
        /** @var Message $implementation */
        foreach ($this->messageImplementationResolver->getMessageImplementations() as $implementation) {
            if ($implementation::matchMessage($message)) {
                return $this->transferMessage($message, $implementation);
            }
        }

        return $message;
    }

    public function transferMessage(Message $from, $to)
    {
        /** @var Message $message */
        $message = new $to($from->getLabel(), $from->getEntities(), $from->getSubMessages());

        //Process entities
        foreach ($message->getEntities() as $entities) {
            foreach ($entities as $order => $entity) {
                $message->addEntity($this->entityProcessor->process($entity), $order);
            }
        }

        //Process submessages
        foreach ($message->getSubMessages() as $submessages) {
            foreach ($submessages as $order => $submessage) {
                $message->addSubmessage($this->process($submessage), $order);
            }
        }

        return $message;
    }
}
