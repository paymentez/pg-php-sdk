<?php

namespace Paymentez;

use Exception;


class Helpers
{
    /**
     * Validate array structure with a schema
     * @param array $schema
     * @param array $arr
     * @return array
     * @throws Exception
     */
    public static function validateArray(array $schema, array $arr): array
    {
        $schemaKeys = array_keys($schema);
        $valueKeys = array_keys($arr);
        $intersect = array_intersect($schemaKeys, $valueKeys);
        $output = [
            'errors' => [
                'total' => 0,
            ]
        ];

        if (count($intersect) < count($schemaKeys)) {
            $missingKeys = array_merge(array_diff($intersect, $schemaKeys), array_diff($schemaKeys, $intersect));
            $output['errors']['missing_keys'] = $missingKeys;
            $output['errors']['total']++;
        }

        $supportValidationType = [
            'int' => 'is_integer',
            'float' => 'is_float',
            'numeric' => 'is_numeric',
            'bool' => 'is_bool',
            'string' => 'is_string',
            'object' => 'is_object',
            'array' => 'is_array'
        ];

        foreach ($schema as $key => $type) {
            if (!key_exists($type, $supportValidationType)) {
                throw new Exception("Unsupported {$type} validation type");
            }

            if (key_exists($key, $arr)) {
                $validation = $supportValidationType[$type];
                $value = $arr[$key];

                $isValid = call_user_func_array($validation, [$value]);

                if (!$isValid) {
                    $output['errors']['values'][$key][$type] = "Not a valid {$type} value.";
                    $output['errors']['total']++;
                }
            }
        }

        return $output;
    }
}