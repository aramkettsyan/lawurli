<?php 

    $this->title = 'Error' ;

?>
<?php if ($exception->statusCode === 404): ?>
    <?= $this->render('_404'); ?>
<?php else: ?>
    
<?php endif; ?>
