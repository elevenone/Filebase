<?php  namespace Filebase;


class Validate
{

    /**
     * valid
     *
     * @param object document $object
     * @return bool (true/false)
     */
    public static function valid(Document $object)
    {
        $document = $object->toArray();

        self::validateLoop($document,$object,self::getValidateRules($object));

        return true;
    }


    //--------------------------------------------------------------------


    /**
    * getValidateRules
    *
    * @return database->config
    */
    public static function getValidateRules(Document $object)
    {
        return $object->getDatabase()->getConfig()->validate;
    }


    //--------------------------------------------------------------------


    /**
    * validateLoop
    */
    public static function validateLoop($document,$object,$rules)
    {
        foreach($rules as $key => $rule)
        {
            if ( (!isset($rule['valid.type']) ) && isset($document[$key]))
            {
                self::validateLoop($document[$key],$object,$rules[$key]);

                continue;
            }

            self::validateRules($document,$key,$rules[$key],$object);
        }
    }


    /**
    * validateRules
    */
    public static function validateRules($document,$key,$rules,$object)
    {
        // checks variable type
        if (isset($document[$key],$rules['valid.type']))
        {
            if (!self::checkType($document[$key],$rules['valid.type']))
            {
                throw new \Exception('Validation Failed setting variable on '.$object->getId().' - ['.$key.'] does not match type '.$rules['valid.type']);
            }
        }

        // check if variable is required
        if (isset($rules['valid.required']) && $rules['valid.required']===true)
        {
            if (!isset($document[$key]))
            {
                throw new \Exception('Validation Failed setting variable on '.$object->getId().' - ['.$key.'] is required');
            }
        }

        return $object;
    }


    //--------------------------------------------------------------------


    /**
    * checkType
    *
    */
    private static function checkType($variable, $type)
    {
        switch($type)
        {
            case 'string':
            case 'str':
                if (is_string($variable))
                {
                    return true;
                }

                break;

            case 'integer':
            case 'int':
                if (is_integer($variable))
                {
                    return true;
                }

                break;

            case 'array':
            case 'arr':
                if (is_array($variable))
                {
                    return true;
                }

                break;

            default:
                return true;
        }

        return false;
    }


    //--------------------------------------------------------------------


}
