<?php
$this->layout = 'error';
$this->assign('title', $message);
?>
<h2><?= h($message) ?></h2>
<p class="error">
    <strong><?php echo __('Error') ?>: </strong>
    <?= sprintf(__('La dirección %s no se encuentra disponible.'), "<strong>'{$url}'</strong>") ?>
</p>
