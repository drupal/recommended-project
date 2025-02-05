<?php
namespace Consolidation\SiteAlias;

interface DataFileLoaderInterface
{
    /**
     * @return array
     */
    public function load($path);
}
