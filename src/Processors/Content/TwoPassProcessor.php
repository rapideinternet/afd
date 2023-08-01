<?php

namespace SIVI\AFD\Processors\Content;

use SIVI\AFD\Exceptions\InvalidParseException;
use SIVI\AFD\Models\Message;
use SIVI\AFD\Processors\Content\Contracts\TwoPassProcessor as TwoPassProcessorContract;
use SIVI\AFD\Processors\Message\Contracts\MessageProcessor;
use SIVI\AFD\Services\Contracts\ParserService;

class TwoPassProcessor extends ContentProcessor implements TwoPassProcessorContract
{
    /**
     * @var ParserService
     */
    protected $parserService;
    /**
     * @var MessageProcessor
     */
    protected $messageProcessor;

    /**
     * TwoPassProcessor constructor.
     */
    public function __construct(ParserService $parserService, MessageProcessor $messageProcessor)
    {
        $this->parserService    = $parserService;
        $this->messageProcessor = $messageProcessor;
    }

    /**
     * @throws InvalidParseException
     */
    public function process($extension, $content): Message
    {
        //Determine parsers type
        $parser = $this->parserService->getParserByExtension($extension);

        //Parse string of context
        $message = $parser->parse($content);

        //Start 2 pass for replacing specific versions
        return $this->messageProcessor->process($message);
        //Return the message with replaced
    }

    /**
     * @param callable(Message):void $callback
     */
    public function stream(string $extension, string $content, callable $callback): void
    {
        //Determine parsers type
        $parser = $this->parserService->getParserByExtension($extension);

        //Parse string of context
        $parser->stream($content, function (Message $message) use ($callback) {
            //Start 2 pass for replacing specific versions
            $message = $this->messageProcessor->process($message);

            $callback($message);
        });
    }
}
