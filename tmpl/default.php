<?php
defined('_JEXEC') or die('Restricted Access'); ?>

<?php if ($mode==0) : ?>
<?php echo 'Der nächste Einsatz für die Feuerwehr Rethen ist am '.date('d.m.Y', $next).' um '.date('H:i', $next).' Uhr.'; ?>
<br /><br /><span style="font-style:italic;">Das Orakel übernimmt für diese Angaben keine Gewähr.</span>
<?php endif; ?>

<?php
/* Define colors */
$color0 = '#e74c3c';
$color1 = '#1abc9c';
$color2 = '#3498db';
$color3 = '#e67e22';
$color4 = '#e67e22';
$color5 = '#e67e22';

?>
<?php if ($mode==1) : ?>
<canvas id="myChart" width="175" height="175"></canvas>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.1/Chart.min.js"></script>
<script>
var data = [
<?php for ($i=0; $i<count($statsByType); $i++) : ?>
    {
        value: <?php echo $statsByType[$i]->count; ?>,
        color:"<?php echo ${'color'.$i}; ?>",
        highlight: "#e74c3c",
        label: "<?php echo $statsByType[$i]->data1; ?>"
    },
<?php endfor; ?>
];

var ctx = document.getElementById('myChart').getContext('2d');
var myPieChart = new Chart(ctx).Pie(data);
</script>
<?php endif; ?>
