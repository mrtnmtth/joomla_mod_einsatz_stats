<?php
defined('_JEXEC') or die('Restricted Access'); ?>

<?php if ($mode==0) : ?>
<?php echo 'Der nächste Einsatz für die Feuerwehr Rethen ist am '.date('d.m.Y', $next).' um '.date('H:i', $next).' Uhr.'; ?>
<br /><br /><span style="font-style:italic;">Das Orakel übernimmt für diese Angaben keine Gewähr.</span>
<?php endif; ?>

<?php if ($mode==1) : ?>
<canvas id="einsatzChart" width="<?php echo $pie_size; ?>" height="<?php echo $pie_size; ?>"></canvas>
<?php endif; ?>
