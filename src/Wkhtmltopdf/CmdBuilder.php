<?php

namespace Eleggua\Wkhtmltopdf;

/**
 * Class CmdBuilder
 * @package Eleggua\Wkhtmltopdf
 */
class CmdBuilder
{
    const BINARY = '../../bin/wkhtmltopdf-0.12.2.1';

    /**
     * @var string
     */
    public $orientation = Wkhtmltopdf::ORIENTATION_PORTRAIT;

    /**
     * @var string
     */
    public $pageSize = Wkhtmltopdf::SIZE_A4;

    /**
     * @var bool
     */
    public $toc = false;

    /**
     * @var int
     */
    public $copies = 1;

    /**
     * @var string
     */
    public $autoScaling = '';

    /**
     * @var bool
     */
    public $grayscale = false;

    /**
     * @var string
     */
    public $footerHtml = '';

    /**
     * @var int
     */
    public $marginTop = 0;

    /**
     * @var int
     */
    public $marginBottom = 0;

    /**
     * @var int
     */
    public $marginLeft = 10;

    /**
     * @var int
     */
    public $marginRight = 10;

    /**
     * @var string
     */
    public $pageHeight = '';

    /**
     * @var string
     */
    public $pageWidth = '';

    /**
     * @var string
     */
    private $command;

    public function __construct()
    {
        $this->start();
    }

    public function buildOrientation()
    {
        $this->command = " --orientation " . $this->orientation;
        return $this;
    }

    public function buildPageSize()
    {
        $this->command .= " --page-size " . $this->pageSize;
        return $this;
    }

    public function buildToc()
    {
        $this->command .= $this->toc;
        return $this;
    }

    public function buildCopies()
    {
        $this->command .= " --copies " . $this->copies;
        return $this;
    }

    public function buildDisableAutoScaling()
    {
        $this->command .= $this->autoScaling;
        return $this;
    }

    public function buildGrayscale()
    {
        $this->command .= $this->grayscale;
        return $this;
    }

    public function buildFooterHtml()
    {
        $this->command .= $this->footerHtml;
        return $this;
    }

    public function buildPage()
    {
        if ($this->pageWidth == 0 || $this->pageHeight == 0) {
            return $this->buildPageSize();
        }

        $this->command .=
            " --page-width " . $this->pageWidth .
            " --page-height " . $this->pageHeight
        ;
        return $this;
    }

    public function buildMargins()
    {
        $this->command .=
            " --margin-top " . $this->marginTop .
            " --margin-bottom " . $this->marginBottom .
            " --margin-left " . $this->marginLeft .
            " --margin-right " . $this->marginRight
        ;
        return $this;
    }

    public function build()
    {
        return $this
            ->buildDisableAutoScaling()
            ->buildCopies()
            ->buildOrientation()
            ->buildToc()
            ->buildPage()
            ->buildGrayscale()
            ->buildMargins()
        ;
    }

    public function start()
    {
        $this->command = static::BINARY . ' --image-quality 100';
    }

    public function end()
    {
        $this->command .= ' "%input%" -';
    }

    public function getCommand()
    {
        $this->end();
        return $this->command;
    }
}
