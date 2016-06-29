<?php if ((!empty($ordered_episodes) || !empty($legacyepisodes) || !empty($supportserviceepisodes)) && $this->checkAccess('OprnCreateEpisode')) {?>
    <button class="secondary tiny add-episode" type="button" id="add-episode">
        <span class="icon-button-small-plus-sign"></span>
        Episode
    </button>
    <button
        class="secondary tiny add-event addEvent"
        type="button"
        id="add-event"
        data-attr-subspecialty-id="<?= $this->firm->getSubspecialtyID();?>">
        <span class="icon-button-small-plus-sign"></span>
        Event
    </button>
<?php }?>


<?php
// flatten the data structure to include legacy events into the core navigation. Note here we are
// simply assuming that the first entry will be Ophthalmology specialty (for the purposes of this PoC
// we don't anticipate events from any other specialty)
if (count($legacyepisodes)) {
    if (!is_array($ordered_episodes) || empty($ordered_episodes)) {
        $ordered_episodes = array(
            array('specialty' => 'Ophthalmology',
                'episodes' => array()
            )
        );
    }
    foreach ($legacyepisodes as $le) {
        $ordered_episodes[0]['episodes'][] = $le;
    }
}
//$this->renderPartial('//patient/_legacy_events',array('legacyepisodes'=>$legacyepisodes))
?>

<?php
$subspecialty_labels = array();

if (is_array($ordered_episodes)) {
    foreach ($ordered_episodes as $specialty_episodes) { ?>
        <div class="panel specialty" id="specialty-panel-<?=$specialty_episodes['specialty']?>">
            <h3 class="specialty-title"><?php echo $specialty_episodes['specialty'] ?></h3>
            <section class="panel">
            <ol class="subspecialties">
                <?php foreach ($specialty_episodes['episodes'] as $i => $episode) {
                    // TODO deal with support services possibly?
                    $id = $episode->getSubspecialtyID();
                    $subspecialty_name = $episode->getSubspecialtyText();
                    if (!$id) {
                        $id = "Le";
                        $tag = $id;
                    }
                    else {
                        $tag = $episode->subspecialty ? $episode->subspecialty->ref_spec : 'Ss';
                    }

                    if (!array_key_exists($id, $subspecialty_labels)) {
                        $subspecialty_labels[$id] = $subspecialty_name; ?>

                        <li class="subspecialty <?= $current_episode && $current_episode->getSubspecialtyID() == $id ? "selected" : ""; ?>"
                            data-subspecialty-id="<?= $id ?>"><?= CHtml::link($subspecialty_name, array('/patient/episode/' . $episode->id)) ?>
                            <span class="tag"><?= $tag ?></span></li>

                    <?php }
                } ?>
            </ol>
            <ol class="events">
                <?php foreach ($specialty_episodes['episodes'] as $i => $episode) { ?>
                    <!-- Episode events -->

                    <?php foreach ($episode->events as $event) {
                        $highlight = false;
                        $lowlight = true;

                        if (isset($this->event) && $this->event->id == $event->id) {
                            $highlight = TRUE;
                        }
                        if ($current_episode && $current_episode->getSubspecialtyID() == $event->episode->getSubspecialtyID())
                            $lowlight = false;


                        $event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/';
                        ?>
                        <li id="eventLi<?php echo $event->id ?>"
                            class="<?php if ($highlight) { ?> selected<?php }?><?php if ($lowlight) { echo "lowlight"; }?>"
                            data-event-date="<?= $event->event_date ?>" data-created-date="<?= $event->created_date ?>"
                            data-event-date-display="<?= $event->NHSDate('event_date') ?>"
                            data-event-type="<?= $event->eventType->name ?>"
                            data-subspecialty="<?= $subspecialty_name ?>">

                            <!-- Quicklook tooltip -->
                            <div class="tooltip quicklook" style="display: none; ">
                                <div class="event-name"><?php echo $event->eventType->name ?></div>
                                <div class="event-info"><?php echo str_replace("\n", "<br/>", $event->info) ?></div>
                                <?php if ($event->hasIssue()) { ?>
                                    <div class="event-issue"><?php echo $event->getIssueText() ?></div>
                                <?php } ?>
                            </div>

                            <a href="<?php echo $event_path . $event->id ?>" data-id="<?php echo $event->id ?>">
                                    <span class="event-type<?php if ($event->hasIssue()) { ?> alert<?php } ?>">
                                        <?php
                                        if (file_exists(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets'))) {
                                            $assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets')) . '/';
                                        } else {
                                            $assetpath = '/assets/';
                                        }
                                        ?>
                                        <img src="<?php echo $assetpath . 'img/small.png' ?>" alt="op"
                                             width="19" height="19"/>
                                    </span>
                                <span
                                    class="event-date <?php echo ($event->isEventDateDifferentFromCreated()) ? ' ev_date' : '' ?>"> <?php echo $event->event_date ? $event->NHSDateAsHTML('event_date') : $event->NHSDateAsHTML('created_date'); ?></span>
                                <span class="tag"><?= $tag ?></span>
                            </a>

                        </li>
                    <?php } ?>

                <?php } ?>
            </ol>
            </section>
        </div>
    <?php }
}?>

<script type="text/html" id="add-new-event-template">
    <?php $this->renderPartial('//patient/add_new_event',array(
        'episode' => "{{episode}}",
        'subspecialty' => "{{subspecialty}}",
        'patient' => $this->patient,
        'eventTypes' => EventType::model()->getEventTypeModules(),
    ));?>
</script>

<?php
    $subspecialty_label_list = array();
    foreach ($subspecialty_labels as $id => $label)
        $subspecialty_label_list[] = "{$id}: '{$label}'";
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('div.specialty').each(function() {
            new OpenEyes.UI.EpisodeSidebar(this, {
                subspecialty: "<?= $episode->firm ? $episode->firm->getSubspecialtyID() : ''?>",
                subspecialty_labels: {
                    <?= implode(",", $subspecialty_label_list); ?>
                }
            });
        });

        $('.sidebar.episodes-and-events .quicklook').each(function() {
            var quick = $(this);
            var iconHover = $(this).parent().find('.event-type');
            iconHover.hover(function(e) {
                quick.fadeIn('fast');
            }, function(e) {
                quick.hide();
            });
        });
    });
</script>