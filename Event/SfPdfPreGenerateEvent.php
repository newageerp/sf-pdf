<?php
namespace Newageerp\SfPdf\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SfPdfPreGenerateEvent extends Event
{
    public const NAME = 'sfpdf.pregenerate';

    protected array $data = [];

    protected string $fileName = '';

    public function __construct(array $data, string $fileName) {
        $this->data = $data;
        $this->fileName = $fileName;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }
}