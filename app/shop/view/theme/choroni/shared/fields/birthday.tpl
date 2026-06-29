<div class="entry-birthday form-entry">
    <label><?php echo $l('entry_birthday'); ?></label>
    <div class="row">
        <div class="large-3 medium-3 small-3 columns">
            <select name="bday" title="Selecciona el d&iacute;a de su nacimiento">
                <?php
                $day = 1;
                $toDay = 31;
                while ($toDay >= $day) { ?>
                    <?php $day = ($day < 10) ? '0'.$day : $day; ?>
                    <option value="<?php echo $day; ?>"<?php if (isset($bday) && $day == $bday) { ?> selected="selected"<?php } ?>><?php echo $day; ?></option>
                    <?php
                    $day++;
                }
                ?>
            </select>
        </div>

        <div class="large-3 medium-3 small-3 columns">
            <select name="bmonth" title="Selecciona el mes de su nacimiento">
            <?php
            $month = 1;
            $toMonth = 12;
            while ($toMonth >= $month) { ?>
                <?php $month = ($month < 10) ? '0'.$month : $month; ?>
                <option value="<?php echo $month; ?>"<?php if (isset($bmonth) && $month == $bmonth) { ?> selected="selected"<?php } ?>><?php echo $month; ?></option>
                <?php
                $month++;
            }
            ?>
            </select>
        </div>
        <div class="large-4 medium-4 small-4 columns">
            <select name="byear" title="Selecciona el a&ntilde;o de su nacimiento">
                <?php
                $currentYear = date('Y');
                $fromYear = $currentYear - 100;
                while ($fromYear < $currentYear) { ?>
                    <option value="<?php echo $currentYear; ?>"<?php if (isset($byear) && $currentYear == $byear) { ?> selected="selected"<?php } ?>><?php echo $currentYear; ?></option>
                    <?php
                    $currentYear--;
                }
                ?>
            </select>
        </div>
    </div>
</div>