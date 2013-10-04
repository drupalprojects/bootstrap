<?php
/**
 * @file
 * bootstrap-modal.tpl.php
 *
 * Markup for Bootstrap modals.
 *
 * Variables:
 * - $heading: Modal title.
 * - $body: The rendered body of the modal.
 * - $footer: The rendered footer of the modal.
 * - $attributes: Attributes for the outer modal div.
 */

?>
<div<?php print $attributes; ?>>
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3><?php print $heading; ?></h3>
         </div>
         <div class="modal-body"><?php print $body; ?></div>
         <div class="modal-footer"><?php print $footer; ?></div>
      </div>
   </div>
</div>
