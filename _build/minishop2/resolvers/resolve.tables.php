<?php

/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx = $transport->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            if ($options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_INSTALL) {
                $modx->addPackage('minishop2', MODX_CORE_PATH . 'components/minishop2/model/');
            }

            $manager = $modx->getManager();
            $objects = [];
            $schemaFile = MODX_CORE_PATH . 'components/minishop2/model/schema/minishop2.mysql.schema.xml';
            if (is_file($schemaFile)) {
                $schema = new SimpleXMLElement($schemaFile, 0, true);
                if (isset($schema->object)) {
                    foreach ($schema->object as $obj) {
                        $objects[] = (string)$obj['class'];
                    }
                }
                unset($schema);
            }
            foreach ($objects as $class) {
                $table = $modx->getTableName($class);
                $sql = "SHOW TABLES LIKE '" . trim($table, '`') . "'";
                $stmt = $modx->prepare($sql);
                $newTable = true;
                if ($stmt->execute() && $stmt->fetchAll()) {
                    $newTable = false;
                }
                // If the table is just created
                if ($newTable) {
                    $manager->createObjectContainer($class);
                } else {
                    // If the table exists

                    // 1. Operate with tables
                    $tableFields = [];
                    $c = $modx->prepare("SHOW COLUMNS IN {$modx->getTableName($class)}");
                    $c->execute();
                    while ($cl = $c->fetch(PDO::FETCH_ASSOC)) {
                        $tableFields[$cl['Field']] = $cl['Field'];
                    }
                    foreach ($modx->getFields($class) as $field => $v) {
                        if (in_array($field, $tableFields)) {
                            unset($tableFields[$field]);
                            $manager->alterField($class, $field);
                            $modx->log(modX::LOG_LEVEL_INFO, "Altered field \"{$field}\" of the table \"{$class}\"");
                        } else {
                            $manager->addField($class, $field);
                            $modx->log(modX::LOG_LEVEL_INFO, "Added field \"{$field}\" of the table \"{$class}\"");
                        }
                    }
                    foreach ($tableFields as $field) {
                        $manager->removeField($class, $field);
                        $modx->log(modX::LOG_LEVEL_INFO, "Removed field \"{$field}\" of the table \"{$class}\"");
                    }

                    // 2. Operate with indexes
                    $indexes = [];
                    $c = $modx->prepare("SHOW INDEX FROM {$modx->getTableName($class)}");
                    $c->execute();
                    while ($row = $c->fetch(PDO::FETCH_ASSOC)) {
                        $name = $row['Key_name'];
                        if (!isset($indexes[$name])) {
                            $indexes[$name] = [$row['Column_name']];
                        } else {
                            $indexes[$name][] = $row['Column_name'];
                        }
                    }
                    foreach ($indexes as $name => $values) {
                        sort($values);
                        $indexes[$name] = implode(':', $values);
                    }
                    $map = $modx->getIndexMeta($class);

                    // Remove old indexes
                    foreach ($indexes as $key => $index) {
                        if (!isset($map[$key])) {
                            if ($manager->removeIndex($class, $key)) {
                                $modx->log(modX::LOG_LEVEL_INFO, "Removed index \"{$key}\" of the table \"{$class}\"");
                            }
                        }
                    }

                    // Add or alter existing
                    foreach ($map as $key => $index) {
                        ksort($index['columns']);
                        $index = implode(':', array_keys($index['columns']));
                        if (!isset($indexes[$key])) {
                            if ($manager->addIndex($class, $key)) {
                                $modx->log(modX::LOG_LEVEL_INFO, "Added index \"{$key}\" in the table \"{$class}\"");
                            }
                        } elseif ($index != $indexes[$key]) {
                            if ($manager->removeIndex($class, $key) && $manager->addIndex($class, $key)) {
                                $modx->log(
                                    modX::LOG_LEVEL_INFO,
                                    "Updated index \"{$key}\" of the table \"{$class}\""
                                );
                            }
                        }
                    }
                }
            }

            if ($options[xPDOTransport::PACKAGE_ACTION] === xPDOTransport::ACTION_UPGRADE) {
                /** @var miniShop2 $miniShop2 */
                $miniShop2 = $modx->getService('minishop2', 'miniShop2', MODX_CORE_PATH . 'components/minishop2/');
                if ($miniShop2->version < '3.0.5') {
                    $sql = "ALTER TABLE {$modx->getTableName('msOrder')} CHANGE COLUMN `comment` `order_comment` TEXT NULL";
                    $modx->exec($sql);
                }

                if ($miniShop2->version < '4.0.0') {
                    $sql = "ALTER TABLE {$modx->getTableName('msOrder')} DROP COLUMN `address`";
                    $modx->exec($sql);
                    $sql = "ALTER TABLE {$modx->getTableName('msOrderAddress')} ADD `order_id`  INTEGER UNSIGNED NOT NULL  FIRST";
                    $modx->exec($sql);
                    $sql = "CREATE INDEX `order_id` ON {$modx->getTableName('msOrderAddress')} (`order_id`)";
                    $modx->exec($sql);
                }
            }

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            break;
    }
}

return true;
