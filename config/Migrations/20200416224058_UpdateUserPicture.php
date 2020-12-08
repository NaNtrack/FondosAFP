<?php
use Migrations\AbstractMigration;

class UpdateUserPicture extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('users');
        $table->changeColumn('picture', 'string', [
            'limit' => '255',
            'null' => true
        ]);
        $table->save();
    }
}
