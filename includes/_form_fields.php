<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="card form-card">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <h2 class="form-section">Personal Information</h2>
    <div class="form-grid">
        <div class="form-group">
            <label for="mentee_name">Mentee Name <span class="required">*</span></label>
            <input type="text" id="mentee_name" name="mentee_name" value="<?= e($mentee['mentee_name'] ?? '') ?>" maxlength="150" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php foreach (STATUSES as $option): ?>
                    <option value="<?= e($option) ?>" <?= ($mentee['status'] ?? 'Active') === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="birthday">Birthday</label>
            <input type="date" id="birthday" name="birthday" max="<?= date('Y-m-d') ?>" value="<?= e($mentee['birthday'] ?? '') ?>">
            <small id="age_hint" class="field-hint"></small>
        </div>
        <div class="form-group">
            <label for="contact_number">Contact Number</label>
            <input type="tel" id="contact_number" name="contact_number" inputmode="numeric" pattern="09[0-9]{9}" title="Must be 11 numbers starting with 09" placeholder="09XXXXXXXXX" value="<?= e($mentee['contact_number'] ?? '') ?>" maxlength="11">
        </div>
        <div class="form-group form-full">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?= e($mentee['address'] ?? '') ?>" maxlength="255">
        </div>
    </div>

    <h2 class="form-section">Mentoring &amp; Trainings</h2>
    <div class="form-grid">
        <div class="form-group">
            <label for="module_lesson">Module / Lesson</label>
            <select id="module_lesson" name="module_lesson">
                <?php $currentModuleLesson = $mentee['module_lesson'] ?? ''; ?>
                <option value="">Not yet started</option>
                <?php if ($currentModuleLesson !== '' && !in_array($currentModuleLesson, module_lesson_values(), true)): ?>
                    <option value="<?= e($currentModuleLesson) ?>" selected><?= e($currentModuleLesson) ?> (current)</option>
                <?php endif; ?>
                <?php foreach (module_lesson_options() as $module => $lessons): ?>
                    <optgroup label="<?= e($module) ?>">
                        <?php foreach ($lessons as $lesson): ?>
                            <option value="<?= e($lesson) ?>" <?= $currentModuleLesson === $lesson ? 'selected' : '' ?>><?= e($lesson) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </div>
        <?php foreach (TRAINING_FIELDS as $field): ?>
            <div class="form-group">
                <label for="<?= e($field) ?>"><?= e(training_label($field)) ?></label>
                <select id="<?= e($field) ?>" name="<?= e($field) ?>">
                    <?php $allowed = training_statuses($field); ?>
                    <?php foreach ($allowed as $option): ?>
                        <option value="<?= e($option) ?>" <?= ($mentee[$field] ?? $allowed[0]) === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>
        <div class="form-group">
            <label for="potential_mentor">Potential Mentor</label>
            <select id="potential_mentor" name="potential_mentor">
                <?php foreach (POTENTIAL_MENTOR_OPTIONS as $option): ?>
                    <option value="<?= e($option) ?>" <?= ($mentee['potential_mentor'] ?? 'No') === $option ? 'selected' : '' ?>><?= e($option) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group form-full">
            <label for="other_trainings">Other Trainings</label>
            <textarea id="other_trainings" name="other_trainings" rows="2"><?= e($mentee['other_trainings'] ?? '') ?></textarea>
        </div>
        <div class="form-group form-full">
            <label for="remarks">Remarks</label>
            <textarea id="remarks" name="remarks" rows="3"><?= e($mentee['remarks'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= isset($editing) ? 'Save Changes' : 'Add Mentee' ?></button>
        <a href="<?= isset($editing) ? 'view.php?id=' . (int)$mentee['id'] : 'index.php' ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
function computeAge(birthdayValue) {
    if (!birthdayValue) {
        return null;
    }
    var birth = new Date(birthdayValue);
    var today = new Date();
    var age = today.getFullYear() - birth.getFullYear();
    var m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age >= 0 ? age : null;
}

var birthdayInput = document.getElementById('birthday');
var ageHint = document.getElementById('age_hint');

function updateAgeHint() {
    var age = computeAge(birthdayInput.value);
    ageHint.textContent = age === null ? '' : 'Age: ' + age;
}

updateAgeHint();
birthdayInput.addEventListener('change', updateAgeHint);
birthdayInput.addEventListener('input', updateAgeHint);

var contactInput = document.getElementById('contact_number');

function cleanContactNumber() {
    var cleaned = contactInput.value.replace(/[^0-9]/g, '');
    if (cleaned !== contactInput.value) {
        contactInput.value = cleaned;
    }
}

contactInput.addEventListener('input', cleanContactNumber);
contactInput.addEventListener('change', cleanContactNumber);
contactInput.addEventListener('paste', function () {
    setTimeout(cleanContactNumber, 0);
});
</script>
