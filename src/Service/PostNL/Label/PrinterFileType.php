<?php

namespace PostNL\Shopware6\Service\PostNL\Label;

class PrinterFileType
{
    const GIFPrefix = 'GraphicFile|GIF';
    const JPGPrefix = 'GraphicFile|JPG';
    const PDFPrefix = 'GraphicFile|PDF';
    const ZPLPrefix = 'Zebra|Generic ZPL II';

    const GIF = 'gif';
    const JPG= 'jpg';
    const PDF = 'pdf';
    const ZPL = 'zpl';

    public static function getPrefixForConfigFiletype(string $fileType){
        switch ($fileType){
            case self::GIF:
                return self::GIFPrefix;
            case self::JPG:
                return self::JPGPrefix;
            case self::PDF:
                return self::PDFPrefix;
            case self::ZPL:
                return self::ZPLPrefix;
        }
    }
}
