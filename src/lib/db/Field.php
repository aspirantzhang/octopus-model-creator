<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;
use think\Exception;

class Field extends DbCommon
{
    private $ignoreHandleType = ['category'];

    private function getExistingFields()
    {
        $existingFields = [];
        $columnsQuery = Db::query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = :tableName;", ['tableName' => $this->tableName]);
        if ($columnsQuery) {
            $existingFields = extractValues($columnsQuery, 'COLUMN_NAME');
        }
        return $existingFields;
    }

    public function fieldsHandler(array $fieldsData, array $processedFields, array $reservedFields)
    {
        $existingFields = $this->getExistingFields();
        // group by types
        $delete = array_diff($existingFields, $processedFields);
        $add = array_diff($processedFields, $existingFields);
        $change = array_intersect($processedFields, $existingFields);

        $statements = [];
        foreach ($fieldsData as $field) {
            $type = '';
            $typeAddon = '';
            $default = '';

            if (in_array($field['type'], $this->ignoreHandleType)) {
                continue;
            }

            switch ($field['type']) {
                case 'number':
                    $type = 'INT';
                    $typeAddon = '';
                    $default = 'DEFAULT 0';
                    break;
                case 'parent':
                    $type = 'INT';
                    $typeAddon = ' UNSIGNED';
                    $default = 'DEFAULT 0';
                    break;
                case 'datetime':
                    $type = 'DATETIME';
                    $typeAddon = '';
                    break;
                case 'switch':
                    $type = 'TINYINT';
                    $typeAddon = '(1)';
                    $default = 'DEFAULT 1';
                    break;
                case 'textarea':
                    $type = 'TEXT';
                    $typeAddon = '';
                    $default = '';
                    break;
                case 'textEditor':
                    $type = 'LONGTEXT';
                    $typeAddon = '';
                    $default = '';
                    break;
                default:
                    $type = 'VARCHAR';
                    $typeAddon = '(255)';
                    $default = 'DEFAULT \'\'';
                    break;
            }

            if (in_array($field['name'], $add)) {
                $method = 'ADD';
                $statements[] = " $method `${field['name']}` $type$typeAddon NOT NULL $default";
            }

            if (in_array($field['name'], $change)) {
                $method = 'CHANGE';
                $statements[] = " $method `${field['name']}` `${field['name']}` $type$typeAddon NOT NULL $default";
            }
        }

        foreach ($delete as $field) {
            $method = 'DROP COLUMN';
            if (!in_array($field, $reservedFields)) {
                $statements[] = " $method `$field`";
            }
        }

        $alterTableSql = 'ALTER TABLE `' . $this->tableName . '` ' . implode(',', $statements) . ';';

        try {
            Db::query($alterTableSql);
        } catch (Exception $e) {
            throw new Exception(__('change table structure failed', ['tableName' => $this->tableName]));
        }
    }
}
