<?php foreach (get_data('testTakers') as $k=>$r): ?>
<table class="user-block matrix">
    <tbody>
    <tr>
        <td><?=__('First Name')?></td>
        <td><?=current($r->getPropertyValues(PROPERTY_USER_FIRSTNAME))?></td>
    </tr>
    <tr>
        <td><?=__('Last Name')?></td>
        <td><?=current($r->getPropertyValues(PROPERTY_USER_LASTNAME))?></td>
    </tr>
    <tr>
        <td><?=__('Email')?></td>
        <td><?=current($r->getPropertyValues(PROPERTY_USER_MAIL))?></td>
    </tr>
    <tr>
        <td><?=__('Login')?></td>
        <td><?=current($r->getPropertyValues(PROPERTY_USER_LOGIN))?></td>
    </tr>
    <tr>
        <td><?=__('Password')?></td>
        <td> <?=$r->password?></td>
    </tr>
    </tbody>
</table>

<?php endforeach; ?>
<style>
    .user-block {
        page-break-after: always;
        padding: 30px;
    }
</style>