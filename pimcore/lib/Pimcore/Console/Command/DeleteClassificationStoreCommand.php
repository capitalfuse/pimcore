<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) 2009-2016 pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Console\Command;

use Pimcore\Cache;
use Pimcore\Console\AbstractCommand;
use Pimcore\Db;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteClassificationStoreCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('classificationstore:delete-store')
            ->setDescription('Delete Classification Store')
            ->addOption(
                'storeId', null,
                InputOption::VALUE_REQUIRED,
                "the store ID"
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storeId = $input->getOption("storeId");

        var_dump($storeId);
        if (is_null($storeId)) {
            throw new \Exception("store ID must be set");
        }

        $db = Db::get();

        $tableList = $db->fetchAll("show tables like 'object_classificationstore_data_%'");
        foreach ($tableList as $table) {
            $theTable = current($table);
            $sql = "delete from " . $theTable . " where keyId In (select id from classificationstore_keys where storeId = " . $db->quote($storeId) . ")";
            echo($sql . "\n");
            $db->query($sql);
        }

        $tableList = $db->fetchAll("show tables like 'object_classificationstore_groups_%'");
        foreach ($tableList as $table) {
            $theTable = current($table);
            $sql = "delete from " . $theTable . " where groupId In (select id from classificationstore_groups where storeId = " . $db->quote($storeId) . ")";
            echo($sql . "\n");
            $db->query($sql);
        }

        $sql = "delete from classificationstore_keys where storeId = " . $db->quote($storeId);
        echo($sql . "\n");
        $db->query($sql);

        $sql = "delete from classificationstore_groups where storeId = " . $db->quote($storeId);
        echo($sql . "\n");
        $db->query($sql);

        $sql = "delete from classificationstore_collections where storeId = " . $db->quote($storeId);
        echo($sql . "\n");
        $db->query($sql);

        $sql = "delete from classificationstore_stores where id = " . $db->quote($storeId);
        echo($sql . "\n");
        $db->query($sql);







        Cache::clearAll();
    }
}
