<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_einsatz_stats
 *
 * @copyright   Copyright (C) 2014 - 2018 Martin Matthaei
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted Access'); ?>

<?php if ($mode==0) : ?>
  <p>Der nächste Einsatz für die Feuerwehr Rethen ist am <?= date('d.m.Y', $next); ?> um <?= date('H:i', $next); ?> Uhr.</p>
  <small><i>Das Orakel übernimmt für diese Angaben keine Gewähr.</i></small>
<?php endif; ?>

<?php if ($mode==1) : ?>
  <canvas id="einsatzChart" width="<?php echo $pie_size; ?>" height="<?php echo $pie_size; ?>"></canvas>
  <?php if ($all_stats) : ?>
    <p class="text-right">
      <small>
        <a id="einsatzModalToggle" href="#einsatzModal" data-toggle="modal"><i class="icon-signal"></i> Gesamtstatistik</a>
      </small>
    </p>
    <?php echo JHtmlBootstrap::renderModal('einsatzModal', array('title' => $module->title, 'bodyHeight' => '80', 'modalWidth' => '60'), '<canvas id="einsatzModalChart"></canvas>'); ?>
  <?php endif; ?>
<?php endif; ?>
