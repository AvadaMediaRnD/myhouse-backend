<?php

use yii\db\Migration;

/**
 * Class m180606_122600_pay_company_data
 */
class m180606_122600_pay_company_data extends Migration
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

TRUNCATE `pay_company_service`;
TRUNCATE `pay_company`;

INSERT INTO `pay_company` (`id`, `name`) VALUES 
(1, 'ГорСвет'),
(2, 'Одессаводоканал'),
(3, 'КомпанияБезУслуг');

INSERT INTO `pay_company_service` (`id`, `service_id`, `pay_company_id`) VALUES 
(1, 1, 1),
(2, 2, 2),
(3, 4, 2);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
        ";
        
        $this->execute($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $sql = "
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

TRUNCATE `pay_company_service`;
TRUNCATE `pay_company`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
        ";
        
        $this->execute($sql);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180606_122600_pay_company_data cannot be reverted.\n";

        return false;
    }
    */
}
