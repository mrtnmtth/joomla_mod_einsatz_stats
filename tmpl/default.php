<?php
defined('_JEXEC') or die('Restricted Access'); ?>

<?php if ($mode==0) : ?>
  <p>Der nächste Einsatz für die Feuerwehr Rethen ist am <?= date('d.m.Y', $next); ?> um <?= date('H:i', $next); ?> Uhr.</p>
  <small><i>Das Orakel übernimmt für diese Angaben keine Gewähr.</i></small>
<?php endif; ?>

<?php if ($mode==1) : ?>
  <canvas id="einsatzChart" width="<?php echo $pie_size; ?>" height="<?php echo $pie_size; ?>"></canvas>
<?php endif; ?>
