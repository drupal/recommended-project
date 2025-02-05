<?php
namespace Consolidation\SiteAlias\Util;

use Consolidation\SiteAlias\DataFileLoaderInterface;
use Symfony\Component\Yaml\Yaml;

class YamlDataFileLoader implements DataFileLoaderInterface
{
    /**
     * @inheritdoc
     */
    public function load($path)
    {
        return (array) Yaml::parse(file_get_contents($path));
    }
}
