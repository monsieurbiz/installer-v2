<?php
/**
 * This file is part of Installer version 2.
 *
 * Installer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Installer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Installer.  If not, see <http://www.gnu.org/licenses/>.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/gpl-3.0.txt>.
 *
 */

namespace Mbiz\Installer;


class Helper {
    
    public function _getLocalXml()
    {
        if (!is_file($filename = $this->getAppDir() . 'etc/local.xml')) {
            echo red() . 'local.xml not found' . "\n";
            return false;
        }

        return simplexml_load_file($filename);
    }

    public function setLast($name = null, $params = array())
    {
        $this->_lastMethod = $name;
        if (!is_array($params)) {
            $params = array($params);
        }
        $this->_lastParams = $params;
    }

    public function _rmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . $object) == "dir") $this->_rmdir($dir . $object . "/");
                    else @unlink($dir . $object);
                }
            }
            reset($objects);
            @rmdir($dir);
        }
    }

    public function _createModelDir()
    {
        list($dir, $created) = $this->getModuleDir('Model', true);

        if ($created) {
            $config = $this->getConfig();
            if (!isset($config->global)) {
                $config->addChild('global');
            }
            $global = $config->global;
            if (!isset($global['models'])) {
                $global->addChild('models')->addChild(strtolower($this->getModuleName()))->addChild('class', $this->getModuleName() . '_Model');
            }
            $this->writeConfig();
        }

        return array($dir, $created);
    }

    public function _getClassName($content)
    {
        $reg = '`^.+class ([a-zA-Z_]*) .+$`isU';
        return preg_replace($reg, '\1', $content);
    }

    public function replaceVarsAndMethods(&$content, $params, $type = false)
    {
        while (!empty($params)) {
            $name = trim(array_shift($params));
            if (!empty($name) && $name !== "-") {
                $match = array();
                if (preg_match('`^([A-Z0-9_][A-Z0-9_]+)(?:=(.+))?$`', $name, $match)) { // const
                    $name = $match[1];
                    list($type, $value) = $this->getVarTypeAndValue(isset($match[2]) ? $match[2] : '');
                    $shortDesc = 'short_description_here';
                    if ($name == 'TEMPLATE') {
                        $shortDesc = 'Template filename for this block';
                    }
                    $const = $this->getTemplate('const_var', array(
                            '{name}' => $name,
                            '{type}' => ($type == 'this' ? '{this}' : $type),
                            '{value}' => $value,
                            'short_description_here' => $shortDesc
                        )) . "\n\n" . $this->getTag('new_const');
                    $content = str_replace($this->getTag('new_const'), $const, $content);
                } elseif (preg_match('`^\$([a-z_][a-z0-9_]+)(?:=(.+))?$`i', $name, $match)) { // var
                    $name = $match[1];
                    list($type, $value) = $this->getVarTypeAndValue(isset($match[2]) ? $match[2] : '');
                    if (strpos($name, '_') === 0) {
                        $var = $this->getTemplate('public_var', array(
                                '{name}' => $name,
                                '{type}' => ($type == 'this' ? '{this}' : $type),
                                '{value}' => $value
                            )) . "\n\n" . $this->getTag('new_var');
                    } else {
                        $var = $this->getTemplate('public_var', array(
                                '{name}' => $name,
                                '{type}' => ($type == 'this' ? '{this}' : $type),
                                '{value}' => $value
                            )) . "\n\n" . $this->getTag('new_var');
                    }
                    $content = str_replace($this->getTag('new_var'), $var, $content);
                } elseif (preg_match('`^(_?)(_[a-z][a-z0-9_]*|[a-z][a-z0-9_]*)(\(\))?(?::([0-9a-zA-Z._]*))?(?:/(p))?$`i', $name, $match)) { // Method
                    $vars = false;
                    $return = '';
                    $name = $match[2];
                    if (isset($match[3]) && $match[3] == '()') {
                        $vars = $this->prompt('Params for ' . red() . $name . '()' . white() . '?');
                    }
                    if (isset($match[4])) {
                        $return = $match[4];
                        if (empty($return)) {
                            $return = $this->prompt('Return for ' . red() . $name . '()' . white() . '?');
                        }
                    }
                    $useParent = isset($match[5]) && $match[5] === 'p';
                    $description = 'short_description_here';
                    if ($name == '_construct' && $match[1] == '_') {
                        $method = $this->getTemplate('constructor_method', array(
                                '{params}' => $vars,
                                'short_description_here' => $description,
                                'return' => '{this}',
                                '// Code here' => (!$useParent ? '' : "parent::$name($vars);\n        ") . '// Code here'
                            )) . "\n\n" . $this->getTag('new_method');
                    } elseif ($match[1] == '_') {
                        switch ($name) {
                            case 'construct':
                                $description = 'Secondary constructor';
                                break;
                            case 'prepareLayout':
                                $description = 'Prepare layout';
                                $return = 'this';
                                $useParent = true;
                                break;
                            case 'toHtml':
                                $description = 'To HTML';
                                $return = 'string';
                                $useParent = true;
                                break;
                        }
                        $method = $this->getTemplate('public_method', array(
                                '{name}' => $name,
                                '{params}' => $vars,
                                '{return}' => ($return == 'this' ? '{this}' : $return),
                                'short_description_here' => $description,
                                '// Code here' => (!$useParent ? '' : "parent::_$name($vars);\n        ") . '// Code here'
                            )) . "\n\n" . $this->getTag('new_method');
                    } else {
                        $method = $this->getTemplate('public_method', array(
                                '{name}' => $name . ($type == 'action' ? 'Action' : ''),
                                '{params}' => ($type == 'observer' && !$vars) ? 'Varien_Event_Observer $observer' : $vars,
                                '{return}' => ($return == 'this' ? '{this}' : $return),
                                'short_description_here' => $description,
                                '// Code here' => (!$useParent ? '' : "parent::$name($vars);\n        ") . '// Code here'
                            )) . "\n\n" . $this->getTag('new_method');
                    }
                    $content = str_replace($this->getTag('new_method'), $method, $content);
                } else {
                    echo "Bad syntax for " . red() . $name . white() . ".\n";
                }
            }
        }

        if (strpos($content, '{this}') !== false) {
            $tmpContent = &$content;
            $className = $this->_getClassName($tmpContent);
            $content = str_replace('{this}', $className, $content);
        }
    }

    public function getVarTypeAndValue($value)
    {
        if (strtolower($value) === 'null') {
            $type = 'null';
            $value = 'null';
        } elseif (strtolower($value) === 'array') {
            $type = 'array';
            $value = 'array()';
        } elseif (strtolower($value) === 'true' || strtolower($value) === 'false') {
            $type = 'bool';
            $value = strtolower($value) === 'true' ? 'true' : 'false';
        } elseif (is_numeric($value)) {
            if (intval($value) == $value) {
                $type = 'int';
            } else {
                $type = 'float';
            }
        } elseif (!empty($value)) {
            $value = "'" . str_replace("'", "\'", $value) . "'";
            $type = 'string';
        } else {
            $value = "null";
            $type = '';
        }
        return array($type, $value);
    }

    public function getLocales()
    {
        return explode(',', self::$_config->locales);
    }

    public function getConfigFilename()
    {
        return $this->getModuleDir('etc') . 'config.xml';
    }

    public function getConfig()
    {
        if (is_null($this->_mageConfig)) {
            $this->_mageConfig = simplexml_load_file($this->getConfigFilename());
        }
        return $this->_mageConfig;
    }

    public function reloadConfig() {
        $this->_mageConfig = null;
    }

    public function getConfigVersion()
    {
        $v = $this->getConfig()->modules->{$this->getModuleName()}->version;
        $this->reloadConfig();
        return $v;
    }

    public function writeConfig()
    {
        if (!is_null($this->_mageConfig)) {
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = 4;
            if (!$dom->loadXML($this->_mageConfig->asXML())) {
                echo red() . "Invalid XML. Could not write config.\n";
                return false;
            }
            $tidy = tidy_parse_string($dom->saveXml(), array(
                'indent' => true,
                'input-xml' => true,
                'output-xml' => true,
                'add-xml-space' => false,
                'indent-spaces' => 4,
                'wrap' => 0,
                'escape-cdata' => false,
                'wrap-sections' => false,
                'indent-cdata' => false,
                'output-encoding' => 'utf8',
            ));
            $tidy->cleanRepair();
            file_put_contents($this->getConfigFilename(), (string) $tidy);
            unset($dom);
            $this->_mageConfig = null;
        }
    }

    public function getTemplate($name, array $vars = array())
    {
        if (!$this->_templates) {
            $fp = fopen(__FILE__, 'r');
            fseek($fp, __COMPILER_HALT_OFFSET__);
            $this->_templates = stream_get_contents($fp);
            fclose($fp);
        }
        $template = preg_replace('`^(?:.+)?BEGIN ' . $name . "\n(.+)\nEND " . $name . '(?:.+)?$`is', '$1', $this->_templates);

        $searchAndReplace = array(
            '<_?php'            => '<?php',
            '<_?xml'            => '<?xml',
            '{Module_Name}'     => $this->getModuleName(),
            '{module_name}'     => strtolower($this->getModuleName()),
            '{LICENSE}'         => self::$_config->license,
            '{USER_NAME}'       => self::$_config->user_name,
            '{USER_EMAIL}'      => self::$_config->user_email,
            '{USER_TWITTER}'    => self::$_config->user_twitter,
            '{Namespace}'       => $this->_namespace,
            '{date_year}'       => date('Y'),
            '{COMPANY_NAME}'    => self::$_config->company_name,
            '{COMPANY_URL}'     => self::$_config->company_url
        );

        if ($name !== 'copyright') {
            $searchAndReplace['{COPYRIGHT}'] = $this->getTemplate('copyright');
        }

        $template = strtr($template, $searchAndReplace);

        return strtr($template, $vars);
    }

    public function getMiscDir()
    {
        return self::$_config->pwd . '/misc/';
    }

    public function getAppDir()
    {
        $path = trim(self::$_config->path, '/');
        return self::$_config->pwd . (!empty($path) ? '/' . $path : '') . '/app/';
    }

    public function getPoolDir()
    {
        $dir = $this->getAppDir() . 'code/' . $this->_pool . '/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        return $dir;
    }

    public function getNamespaceDir()
    {
        $dir = $this->getPoolDir() . $this->_namespace . '/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        return $dir;
    }

    public function getModuleDir($name = null, $getCreated = false)
    {
        if (is_null($name)) {
            $dir = $this->getNamespaceDir() . $this->_module . '/';
        } else {
            $dir = $this->getModuleDir() . $name . '/';
        }
        $created = false;
        if (!is_dir($dir)) {
            mkdir($dir);
            $created = true;
        }
        return (is_null($name) || !$getCreated) ? $dir : array($dir, $created);
    }

    public function getDesignDir($where, $child = '')
    {
        $dir = $this->getAppDir() . 'design/' . $where . '/';
        $names = explode('_', self::$_config->design);

        if ($child) {
            $names[] = strtolower($child);
        }

        foreach ($names as $name) {
            $dir .= $name . '/';
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }

        return $dir;
    }

    public function getModuleTitle()
    {
        if (!$this->_namespace) {
            return null;
        }
        return sprintf('%s %s', $this->_namespace, $this->_module);
    }

    public function getModuleName()
    {
        if (!$this->_namespace) {
            return null;
        }
        return $this->_namespace . '_' . $this->_module;
    }

    public function getTag($name)
    {
        return '// ' . self::$_config->company_name . ' Tag ' . strtoupper($name);
    }

    public function prompt($text)
    {
        echo white() . $text . "\n" . blue() . '> ' . white();
        return $this->_read(false);
    }

    /**
     * Returns the name in camel case
     * @param string $name The string to transform
     * @return string
     */
    public function _camelize($str)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
    }

    public function _read($usePrompt = true)
    {
        $prompt = null;
        if ($usePrompt) {
            $prompt = white() . $this->getModuleName() . red() . '> ' . white();
        }
        $line = trim(readline($prompt));
        if (!empty($line)) {
            readline_add_history($line);
        }
        return $line;
    }
} 