<?php if ($exception->statusCode === 404): ?>
    <?= $this->render('_404'); ?>
<?php else: ?>
    <!-- other error goes here -->
<?php endif; ?>
