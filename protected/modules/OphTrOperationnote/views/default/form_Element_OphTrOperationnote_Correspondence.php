<?php
    $correspondence_element = new ElementLetter();
    if ($this->event->id) {
        $correspondence_element->event = $this->event;
    }

?>

<div class="element-fields">
	<div class="row">
        <div class="large-12 column label">
            Create correspondence for the following:
        </div>
    <div class="large-12 column" id="correspondencerow">
		<div class="large-5 column">
            <?php echo $form->dropDownList($element, 'address_target', $correspondence_element->address_targets,  array('empty' => '- Recipient -', 'label' => 'Recipient'))?>
        </div>
        <div class="large-6 column">
            <?php echo $form->dropDownList($element, 'macro_id', $correspondence_element->letter_macros, array('empty' => '- Macro -', 'label' => 'Macro'))?>
        </div>
        <div class="large-1 column">
            <a class="removeCorrespondence" href="#">Remove</a>
        </div>
    </div>
    <div class="row" id="add_correspondence">
        <div class="large-1 column">
        </div>
        <div class="large-11 column">
            <div id="add_correspondence_row" name="add-correspondence" class="button small secondary">Add</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#add_correspondence_row').on('click', function(){
          $('#add_correspondence').before($('#correspondencerow').clone());
        });

        $(this).delegate('.removeCorrespondence', 'click', function(e) {
            $(this).closest('div[class="row"]').remove();
            e.preventDefault();
        });
    });
</script>