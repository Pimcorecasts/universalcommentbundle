<?php
/**
 * @var \Pimcore\Templating\PhpEngine $this
 * @var \Pimcore\Templating\PhpEngine $view
 * @var \Pimcore\Templating\GlobalVariables $app
 */

$this->jsConfig()->add('summernote', true); ?>

<form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">
    <?php if ($commentId != ''){ ?>
        <input type="hidden" name="commendId" value="<?= $commentId ?>">
    <?php } ?>
    <div class="summernote"></div>
    <button class="btn btn-primary btn-shadow float-right" type="submit" name="save"><?= $this->t('ucb.form.Submit') ?></button>
</form>