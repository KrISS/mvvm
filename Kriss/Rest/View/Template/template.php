<!DOCTYPE html>
<html>
  <head>
    <title>Title Template PHP</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
  </head>
  <body>
<?php foreach($data['data'] as $key => $data): ?>
<?php   if (is_array($data)) : ?>
    <ul id="<?= $key ?>">
<?php     foreach($data as $id => $object): ?>
<?php   $string = []; foreach($object as $item) { $string[] = $item; } ?>
      <li>
        <span class='<?= strtolower(get_class($object)); ?>'><?= join(' ', $string); ?></span>
      </li>
<?php     endforeach; ?>
<?php   endif; ?>
    </ul>
<?php endforeach; ?>
  </body>
</html>
