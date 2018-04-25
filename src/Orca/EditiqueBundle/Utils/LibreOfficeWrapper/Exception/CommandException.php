<?php
namespace Orca\EditiqueBundle\Utils\LibreOfficeWrapper\Exception;

class CommandException extends \RuntimeException
{
    const ERROR_IMAGE_TYPE = 'Unsupported image type.';
    const ERROR_LANGUAGE = 'Failed loading language';
    
    public function __construct($command, array $output)
    {
        parent::__construct(sprintf('Command %s produced error: %s', $command, \implode("\n", $output)));
    }
    
    public static function factory($command, array $output)
    {
        return new self($command, $output);
    }
}
