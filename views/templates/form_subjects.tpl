<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>

<header class="flex-container-full">
    <h2><?=get_data('formTitle')?></h2>
    <?php if(has_data('updatedAt')) : ?>
        <p><?=__('Last updated on %2s', tao_helpers_Date::displayeDate(get_data('updatedAt')))?></p>
    <?php endif?>
</header>
<div class="main-container flex-container-main-form">
    <div class="form-content">
        <?=get_data('myForm')?>
    </div>
</div>
<?php foreach(get_data('additionalForms') as $additionalForm): ?>
    <div class="data-container-wrapper flex-container-remainder">
        <?= $additionalForm ?>
    </div>
<?php endforeach; ?>

<?php if(get_data('checkLogin')):?>
	<script>
	 require(['users'], function(user){
            user.checkLogin("<?=get_data('loginUri')?>", "<?=_url('checkLogin', 'Users', 'tao')?>");
	});
	</script>
<?php endif?>
<?php
Template::inc('footer.tpl', 'tao');
?>

