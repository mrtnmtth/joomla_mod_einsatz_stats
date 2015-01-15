<?php
defined('_JEXEC') or die('Restricted Access'); ?>

<?php if ($mode==0) : ?>
<?php echo 'Der nächste Einsatz für die Feuerwehr Rethen ist am '.date('d.m.Y', $next).' um '.date('H:i', $next).' Uhr.'; ?>
<br /><br /><span style="font-style:italic;">Das Orakel übernimmt für diese Angaben keine Gewähr.</span>
<?php endif; ?>

<?php if ($mode==1) : ?>
<canvas id="myChart" width="175" height="175"></canvas>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.1/Chart.min.js"></script>
<?php endif; ?>
