<?php

use yii\db\Migration;

/**
 * Class m190829_190327_add_1cid_for_section
 */
class m190829_190327_add_1cid_for_section extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('section', 'id_1c', $this->string(40)->notNull());
        
        $this->createIndex('fk_section_id1c_idx', 'section', 'id_1c');
        $this->createIndex('fk_house_id1c_idx', 'house', 'id_1c');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190829_190327_add_1cid_for_section cannot be reverted.\n";

        $this->dropIndex('fk_house_id1c_idx','house');
        $this->dropIndex('fk_section_id1c_idx','section');
        $this->dropColumn('section', 'id_1c');
    }
}
