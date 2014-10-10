<?php
namespace Nexway\SetupManager\Util\Processor;

/**
 * @category    Nexway
 * @package     Nexway_SetupManager
 * @author      Łukasz Lach <llach@nexway.com>
 */
class Action extends \Varien_Object
{
    const F_ACTION      = 'action';
    const F_PARAMETERS  = 'parameters';

    /**
     * Factory method to get proper action instance
     *
     * @param array $config
     * @throws \Exception
     */
    public function assign(array $config)
    {
        $action = $parameters = NULL;
        if (!isset($config[self::F_PARAMETERS]) && sizeof($config) >= 2) {
            $parameters = array_pop($config);
            if (!is_array($parameters)) {
                $parameters = (array)$parameters;
            }
        } elseif (isset($config[self::F_PARAMETERS])) {
            $parameters = $config[self::F_PARAMETERS];
        }
        $action = (string)array_shift($config);
        if (NULL === $action || NULL === $parameters) { 
            throw new \Exception(sprintf('Invalid action entry - %s', var_export($config, TRUE)));
        }
        $raw = $action;
        list($scope, $action) = explode('/', $raw, 2);
        // drop all hard spaces
        $action = str_replace('_', '', $action);
        // unlike Mage::helper() this one returns FALSE when model is not found

        // change whole config to use new action name and remove this ugly hack
        // we can't have default namespace, but in config we have such entry
        $scope = ($scope == 'default') ? $scope . 'scope' : $scope;

        $className = '\\Nexway\\SetupManager\\Util\\Processor\\Action\\' . ucfirst($scope) . '\\' . ucfirst($action);
        $base = new $className();
        if (FALSE === $base) {
            throw new \Exception(sprintf('Invalid action - %s/%s', $scope, $action));
        }
        $parametersObject = new \Varien_Object();
        $parametersObject->setData($parameters);

        return $base->
            setAction($action)->
            setScope($scope)->
            setParameters($parametersObject);
    }
}
