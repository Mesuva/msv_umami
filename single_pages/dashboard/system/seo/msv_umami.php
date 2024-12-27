<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<form method="post" class="ccm-dashboard-content-form" action="<?= $view->action('update_configuration')?>">
    <?= $token->output('msv_umami'); ?>

    <div class="form-group">
        <?= $form->label('website_id', t('Website ID')) ?>
        <?= $form->text('website_id', $website_id, ['required'=>'required'])?>
    </div>

    <div class="form-group">
        <?= $form->label('script_url', t('Umami Script URL')) ?>
        <?= $form->url('script_url', $script_url, ['placeholder'=>'https://cloud.umami.is/script.js'])?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right float-end btn btn-primary" type="submit" ><?= t('Save')?></button>
        </div>
    </div>
</form>


