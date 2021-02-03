<?php

use yii\db\Migration;

/**
 * Class m180606_105513_counter_data_demo
 */
class m180606_105513_counter_data_demo extends Migration
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

TRUNCATE `counter_data`;

INSERT INTO `counter_data` (`id`, `uid`, `uid_date`, `amount`, `amount_total`, `created_at`, `updated_at`, `status`, `flat_id`, `user_admin_id`, `service_id`) VALUES 
(1, 'C0001', '2018-03-10', '10.0', '10.0', 1528280000, 1528280000, 10, 1, NULL, 1), 
(2, 'C0002', '2018-03-11', '50.0', '50.0', 1528280010, 1528280010, 10, 1, NULL, 2),
(3, 'C0003', '2018-03-12', '20.0', '20.0', 1528280020, 1528280020, 10, 1, NULL, 3),
(4, 'C0004', '2018-03-13', '70.0', '70.0', 1528280030, 1528280030, 10, 1, NULL, 4),

(5, 'C0005', '2018-05-10', '40.0', '40.0', 1528280040, 1528280040, 10, 1, NULL, 1), 
(6, 'C0006', '2018-05-11', '30.0', '30.0', 1528280050, 1528280050, 10, 1, NULL, 2),
(7, 'C0007', '2018-05-12', '80.0', '80.0', 1528280060, 1528280060, 10, 1, NULL, 3),
(8, 'C0008', '2018-05-13', '70.0', '70.0', 1528280070, 1528280070, 10, 1, NULL, 4),

(9, 'C0009', '2018-06-01', '50.0', '50.0', 1528280080, 1528280080, 10, 1, NULL, 1), 
(10, 'C0010', '2018-06-02', '110.0', '110.0', 1528280090, 1528280090, 10, 1, NULL, 2),
(11, 'C0011', '2018-06-03', '60.0', '60.0', 1528280100, 1528280100, 10, 1, NULL, 3),
(12, 'C0012', '2018-06-04', '90.0', '90.0', 1528280110, 1528280110, 10, 1, NULL, 4),

(13, 'C0013', '2018-06-01', '80.0', '80.0', 1528280120, 1528280120, 10, 7, NULL, 1), 
(14, 'C0014', '2018-06-02', '130.0', '130.0', 1528280130, 1528280130, 10, 7, NULL, 2),
(15, 'C0015', '2018-06-03', '20.0', '20.0', 1528280140, 1528280140, 0, 7, NULL, 3),
(16, 'C0016', '2018-06-04', '40.0', '40.0', 1528280150, 1528280150, 0, 7, NULL, 4)
;

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

TRUNCATE `counter_data`;

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
        echo "m180606_105513_counter_data_demo cannot be reverted.\n";

        return false;
    }
    */
}
