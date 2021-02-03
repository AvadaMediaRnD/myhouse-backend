<?php

use yii\db\Migration;

/**
 * Class m190314_085545_create_table_website_tariff
 */
class m190314_085545_create_table_website_tariff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "
CREATE TABLE `website_tariff` (
  `id` int(11) NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(11) DEFAULT '0'
);

ALTER TABLE `website_tariff`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `website_tariff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
        ";
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('website_tariff');
    }
}
