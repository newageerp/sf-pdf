<?php
namespace Newageerp\SfPdf\Event;

use Symfony\Contracts\EventDispatcher\Event;

class SfPdfPreGenerateEvent extends Event
{
    public const NAME = 'sfpdf.pregenerate';

    protected array $data = [];

    public function __construct(array $data) {
        $this->data = $data;
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
}