<?php

namespace Vendor\Undermarket\Core\Database\DefaultAction;

use Vendor\Undermarket\Core\Database\ActionInterface;
use Vendor\Undermarket\Core\Database\DataAction;
use Vendor\Undermarket\Core\Database\DataBase;

class UpdateAction implements ActionInterface
{
    public function __construct(
        public string $tableName,
    ) {}

    public function getName(): string
    {
        return 'update';
    }

    public function execute(DataAction $data): mixed
    {
        $setClauses = [];
        $params = [];

        foreach ($data->getColumns() as $key => $value) {
            if (!str_ends_with($key, '_where')) {
                $setClauses[] = "$key = :set_$key";
                $params["set_$key"] = $value;
            }
        }

        $setClause = implode(', ', $setClauses);
        $whereClause = $data->getProperties()['WHERE'] ?? '';

        foreach ($data->getColumns() as $key => $value) {
            if (str_ends_with($key, '_where')) {
                $actualKey = substr($key, 0, -6);

                if (strpos($whereClause, ":$key") !== false) {
                    $whereClause = str_replace(":$key", ":where_$actualKey", $whereClause);
                }

                $params["where_$actualKey"] = $value;
            }
        }

        $sql = "UPDATE {$this->tableName} SET $setClause";

        if (!empty($whereClause)) {
            $sql .= " WHERE $whereClause";
        }

        $result = DataBase::execute($sql, $params);
        return $result;
    }


    public function validate(DataAction $data): bool
    {
        if (empty($data->getColumns())) {
            return false;
        }

        return true;
    }
}
