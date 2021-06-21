<div class="col-sm-6 col-xs-12">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Telephone 1</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" name="contactPhone1" id="contactPhone1" value="<?php echo $contact->Tel1; ?>" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Telephone 2</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" name="contactPhone2" id="contactPhone2" value="<?php echo $contact->Tel2; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Mobile Phone</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="contactMobile" id="contactMobile" value="<?php echo $contact->Cellolar; ?>" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Fax</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="20" class="form-control input-sm" name="contactFax" id="contactFax" value="<?php echo $contact->Fax; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Email</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" name="contactEmail" id="contactEmail" value="<?php echo $contact->E_MailL; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Remarks 1</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" name="contactRemark1" id="contactRemark1" value="<?php echo $contact->Notes1; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Remarks 2</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" name="contactRemark2" id="contactRemark2" value="<?php echo $contact->Notes2; ?>"/>
      </div>
    </div>

    <?php $birthDate = empty($contact->BirthDate) ? "" : thai_date($contact->BirthDate, FALSE); ?>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Date Of Birth</label>
      <div class="col-sm-2 col-xs-12">
        <input type="text" maxlength="8" class="form-control input-sm" name="contactBirthDate" id="contactBirthDate" value="<?php echo $birthDate; ?>" />
      </div>
    </div>


  </div><!-- form -->
</div><!--- end right column -->
