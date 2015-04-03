<table class="matrix">
    <thead>
    <th><?=__('First Name')?></th>
    <th><?=__('Last Name')?></th>
    <th><?=__('Login')?></th>
    <th><?=__('Email')?></th>
    <th><?=__('Password')?></th>
    </thead>
    <tbody>
    <?php foreach (get_data('testTakers') as $r): ?>
    <tr>
        <td><?=current($r->getPropertyValues(PROPERTY_USER_FIRSTNAME))?></td>
        <td><?=current($r->getPropertyValues(PROPERTY_USER_LASTNAME))?></td>
        <td><?=current($r->getPropertyValues(PROPERTY_USER_MAIL))?></td>
        <td><?=current($r->getPropertyValues(PROPERTY_USER_LOGIN))?></td>
        <td><?=$r->password?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

