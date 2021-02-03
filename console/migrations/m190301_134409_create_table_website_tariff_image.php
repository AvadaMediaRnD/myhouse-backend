<?php

use yii\db\Migration;

/**
 * Class m190301_134409_create_table_website_tariff_image
 */
class m190301_134409_create_table_website_tariff_image extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
CREATE TABLE IF NOT EXISTS `website_tariff_image` (
  `id` int(11) NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `website_tariff_image`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `website_tariff_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP TABLE IF EXISTS `website_tariff_image`");
    }
}
