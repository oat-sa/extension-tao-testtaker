<?php
use oat\tao\helpers\Template;

Template::inc('form_context.tpl', 'tao');
?>
<header class="flex-container-full">
    <h3><?=__('Beware, all passwords will be reset if you proceed')?></h3>
</header>

<div class="main-container flex-container-main-form" data-hash-result="<?=get_data('resultUrl')?>">
    <?=get_data('form')?>
</div>

<?php
Template::inc('footer.tpl', 'tao');
?>
