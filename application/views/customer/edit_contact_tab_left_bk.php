<div class="col-sm-6 col-xs-12">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Contact ID</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="contactName" id="contactName" value="<?php echo $contact->Name; ?>"placeholder="Contact Name (Required)"/>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">First Name</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="contactFname" id="contactFname" value="<?php echo $contact->FirstName; ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Middle Name</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="contactMname" id="contactMname" value="<?php echo $contact->MiddleName; ?>" />
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Last Name</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="50" class="form-control input-sm" name="contactLname" id="contactLname" value="<?php echo $contact->LastName; ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Title</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="10" class="form-control input-sm" name="contactTitle" id="contactTitle" value="<?php echo $contact->Title; ?>" />
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Position</label>
      <div class="col-sm-6 col-xs-4">
        <input type="text" maxlength="90" class="form-control input-sm" name="contactPosition" id="contactPosition" value="<?php echo $contact->Position; ?>"/>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-3 col-xs-12">Address</label>
      <div class="col-sm-6 col-xs-12">
        <input type="text" maxlength="100" class="form-control input-sm" id="contactAddress" name="contactAddress" value="<?php echo $contact->Address; ?>"/>
      </div>
    </div>

  </div><!-- form -->
</div><!--- end right column -->
