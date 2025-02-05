<?php
namespace Consolidation\SiteAlias;

use Consolidation\Config\Config;
use Consolidation\Config\ConfigInterface;
use Consolidation\Config\Util\ArrayUtil;
use Consolidation\SiteAlias\Util\FsUtils;

/**
 * An alias record is a configuration record containing well-known items.
 *
 * @see SiteAliasInterface for documentation
 */
class SiteAlias extends Config implements SiteAliasInterface
{
    use SiteAliasTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @inheritdoc
     */
    public function __construct(?array $data = null, $name = '', $env = '')
    {
        parent::__construct($data);
        if (!empty($env)) {
            $name .= ".$env";
        }
        $this->name = $name;
    }

    /**
     * @inheritdoc
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function exportConfig()
    {
        return $this->remap($this->export());
    }

    /**
     * Reconfigure data exported from the form it is expected to be in
     * inside an alias record to the form it is expected to be in when
     * inside a configuration file.
     */
    protected function remap($data)
    {
        foreach ($this->remapOptionTable() as $from => $to) {
            if (isset($data[$from])) {
                unset($data[$from]);
            }
            $value = $this->get($from, null);
            if (isset($value)) {
                $data['options'][$to] = $value;
            }
        }

        return new Config($data);
    }

    /**
     * Fetch the parameter-specific options from the 'alias-parameters' section of the alias.
     * @param string $parameterName
     * @return array
     */
    protected function getParameterSpecificOptions($aliasData, $parameterName)
    {
        if (!empty($parameterName) && $this->has("alias-parameters.{$parameterName}")) {
            return $this->get("alias-parameters.{$parameterName}");
        }
        return [];
    }

    /**
     * Convert the data in this record to the layout that was used
     * in the legacy code, for backwards compatiblity.
     */
    public function legacyRecord()
    {
        $result = $this->exportConfig()->get('options', []);

        // Backend invoke needs a couple of critical items in specific locations.
        if ($this->has('paths.drush-script')) {
            $result['path-aliases']['%drush-script'] = $this->get('paths.drush-script');
        }
        if ($this->has('ssh.options')) {
            $result['ssh-options'] = $this->get('ssh.options');
        }
        return $result;
    }

    /**
     * Conversion table from old to new option names. These all implicitly
     * go in `options`, although they can come from different locations.
     */
    protected function remapOptionTable()
    {
        return [
            'user' => 'remote-user',
            'host' => 'remote-host',
            'root' => 'root',
            'uri' => 'uri',
        ];
    }
}
