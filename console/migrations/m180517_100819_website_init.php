<?php

use yii\db\Migration;
use common\models\Website;
use common\models\WebsiteAboutImage;
use common\models\WebsiteHomeFeature;
use common\models\WebsiteHomeSlide;
use common\models\WebsiteService;
use yii\helpers\Html;

/**
 * Class m180517_100819_website_init
 */
class m180517_100819_website_init extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `website` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `param` VARCHAR(255) NULL,
  `content` MEDIUMTEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `param_UNIQUE` (`param` ASC))
ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `website_home_slide` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `title` VARCHAR(255) NULL DEFAULT NULL,
  `sort` INT(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `website_home_feature` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `title` VARCHAR(255) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `sort` INT(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `website_service` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `title` VARCHAR(255) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `sort` INT(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = MyISAM;

CREATE TABLE IF NOT EXISTS `website_about_image` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `type` INT(11) NULL DEFAULT 1,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  `sort` INT(11) NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = MyISAM;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
            ";
        
        $this->execute($sql);
        
        // demo data
        $this->truncateTable('website_about_image');
        $this->truncateTable('website_service');
        $this->truncateTable('website_home_feature');
        $this->truncateTable('website_home_slide');
        $this->truncateTable('website');
        
        $this->batchInsert('website', ['id', 'param', 'content'], [
            [1, Website::PARAM_HOME_TITLE, Html::encode('Управляющая компания "'.Yii::$app->name.'"')],
            [2, Website::PARAM_HOME_DESCRIPTION, Html::encode('<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci architecto consequuntur dolores eius enim illo, modi, officiis perferendis praesentium quidem quis, 
                quo ratione tenetur ullam veniam vero! Adipisci aliquam consequatur culpa eos excepturi hic, illum libero, nostrum 
                officiis placeat ratione repellat repudiandae sunt ut vel? Eius maxime omnis quo.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci architecto consequuntur dolores eius enim illo, modi, officiis perferendis praesentium quidem quis, 
                quo ratione tenetur ullam veniam vero! Adipisci aliquam consequatur culpa eos excepturi hic, illum libero, nostrum 
                officiis placeat ratione repellat repudiandae sunt ut vel? Eius maxime omnis quo.</p>
                ')],
            [3, Website::PARAM_CONTACT_TITLE, Html::encode('Адрес УК "'.Yii::$app->name.'"')],
            [4, Website::PARAM_CONTACT_DESCRIPTION, Html::encode('<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci architecto consequuntur dolores eius enim illo, modi, officiis perferendis praesentium quidem quis, 
                quo ratione tenetur ullam veniam vero! Adipisci aliquam consequatur culpa eos excepturi hic, illum libero, nostrum 
                officiis placeat ratione repellat repudiandae sunt ut vel? Eius maxime omnis quo.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci architecto consequuntur dolores eius enim illo, modi, officiis perferendis praesentium quidem quis, 
                quo ratione tenetur ullam veniam vero! Adipisci aliquam consequatur culpa eos excepturi hic, illum libero, nostrum 
                officiis placeat ratione repellat repudiandae sunt ut vel? Eius maxime omnis quo.</p>
                ')],
            [5, Website::PARAM_CONTACT_FULLNAME, 'Александр Шевцов'],
            [6, Website::PARAM_CONTACT_LOCATION, 'ул. Космонавтов, 32'],
            [7, Website::PARAM_CONTACT_ADDRESS, 'приморский р-н, г. Одесса'],
            [8, Website::PARAM_CONTACT_PHONE, '+38 (099) 1234567'],
            [9, Website::PARAM_CONTACT_EMAIL, 'contact@example.com'],
            [10, Website::PARAM_CONTACT_MAP_EMBED_CODE, Html::encode('<div class="map">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2749.8611266486014!2d30.713265815873395!3d46.43163207638925!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40c633a651e3fd09%3A0x9331380952bbfd2c!2z0LLRg9C70LjRhtGPINCa0L7RgdC80L7QvdCw0LLRgtGW0LIsIDMyLCDQntC00LXRgdCwLCDQntC00LXRgdGM0LrQsCDQvtCx0LvQsNGB0YLRjCwgNjUwMDA!5e0!3m2!1suk!2sua!4v1524221669656" width="100%" height="800" frameborder="0" style="border:0" allowfullscreen></iframe>
</div>')],
            [11, Website::PARAM_ABOUT_TITLE, Html::encode('УК "'.Yii::$app->name.'"')],
            [12, Website::PARAM_ABOUT_DESCRIPTION, Html::encode('<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci architecto consequuntur dolores eius enim illo, modi, officiis perferendis praesentium quidem quis, 
                quo ratione tenetur ullam veniam vero! Adipisci aliquam consequatur culpa eos excepturi hic, illum libero, nostrum 
                officiis placeat ratione repellat repudiandae sunt ut vel? Eius maxime omnis quo.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci architecto consequuntur dolores eius enim illo, modi, officiis perferendis praesentium quidem quis, 
                quo ratione tenetur ullam veniam vero! Adipisci aliquam consequatur culpa eos excepturi hic, illum libero, nostrum 
                officiis placeat ratione repellat repudiandae sunt ut vel? Eius maxime omnis quo.</p>
                ')],
            [13, Website::PARAM_ABOUT_IMAGE, '/upload/placeholder.jpg'],
            [14, Website::PARAM_ABOUT_TITLE_2, Html::encode('Дополнительная информация')],
            [15, Website::PARAM_ABOUT_DESCRIPTION_2, Html::encode('<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci architecto consequuntur dolores eius enim illo, modi, officiis perferendis praesentium quidem quis, 
                quo ratione tenetur ullam veniam vero! Adipisci aliquam consequatur culpa eos excepturi hic, illum libero, nostrum 
                officiis placeat ratione repellat repudiandae sunt ut vel? Eius maxime omnis quo.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                Accusantium adipisci architecto consequuntur dolores eius enim illo, modi, officiis perferendis praesentium quidem quis, 
                quo ratione tenetur ullam veniam vero! Adipisci aliquam consequatur culpa eos excepturi hic, illum libero, nostrum 
                officiis placeat ratione repellat repudiandae sunt ut vel? Eius maxime omnis quo.</p>
                ')],
        ]);
        $this->batchInsert('website_home_slide', ['id', 'image', 'title', 'sort'], [
            [1, '/upload/placeholder.jpg', Html::encode('Управляющая компания "'.Yii::$app->name.'"'), 0],
            [2, '/upload/placeholder.jpg', Html::encode('Управляющая компания "'.Yii::$app->name.'"'), 0],
            [3, '/upload/placeholder.jpg', Html::encode('Управляющая компания "'.Yii::$app->name.'"'), 0],
        ]);
        $this->batchInsert('website_home_feature', ['id', 'image', 'title', 'description', 'sort'], [
            [1, '/upload/placeholder.jpg', 'Блок 1', 'Описание для Блока 1', 0],
            [2, '/upload/placeholder.jpg', 'Блок 2', 'Описание для Блока 2', 0],
            [3, '/upload/placeholder.jpg', 'Блок 3', 'Описание для Блока 3', 0],
            [4, '/upload/placeholder.jpg', 'Блок 4', 'Описание для Блока 4', 0],
            [5, '/upload/placeholder.jpg', 'Блок 5', 'Описание для Блока 5', 0],
            [6, '/upload/placeholder.jpg', 'Блок 6', 'Описание для Блока 6', 0],
        ]);
        $this->batchInsert('website_service', ['id', 'image', 'title', 'description', 'sort'], [
            [1, '/upload/placeholder.jpg', 'Услуга 1', 'Описание для Услуги 1', 0],
            [2, '/upload/placeholder.jpg', 'Услуга 2', 'Описание для Услуги 2', 0],
            [3, '/upload/placeholder.jpg', 'Услуга 3', 'Описание для Услуги 3', 0],
            [4, '/upload/placeholder.jpg', 'Услуга 4', 'Описание для Услуги 4', 0],
        ]);
        $this->batchInsert('website_about_image', ['id', 'type', 'image', 'sort'], [
            [1, 1, '/upload/placeholder.jpg', 0],
            [2, 1, '/upload/placeholder.jpg', 0],
            [3, 1, '/upload/placeholder.jpg', 0],
            [4, 1, '/upload/placeholder.jpg', 0],
            [5, 1, '/upload/placeholder.jpg', 0],
            [6, 1, '/upload/placeholder.jpg', 0],
            [7, 1, '/upload/placeholder.jpg', 0],
            [8, 2, '/upload/placeholder.jpg', 0],
            [9, 2, '/upload/placeholder.jpg', 0],
            [10, 2, '/upload/placeholder.jpg', 0],
            [11, 2, '/upload/placeholder.jpg', 0],
            [12, 2, '/upload/placeholder.jpg', 0],
            [13, 2, '/upload/placeholder.jpg', 0],
            [14, 2, '/upload/placeholder.jpg', 0],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('
DROP TABLE IF EXISTS `website_about_image`;
DROP TABLE IF EXISTS `website_service`;
DROP TABLE IF EXISTS `website_home_feature`;
DROP TABLE IF EXISTS `website_home_slide`;
DROP TABLE IF EXISTS `website`;
            ');
    }

}
