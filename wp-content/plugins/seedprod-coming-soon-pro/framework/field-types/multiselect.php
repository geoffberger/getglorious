<?php
// {$setting_id}[$id] - Contains the setting id, this is what it will be stored in the db as.
// $class - optional class value
// $id - setting id
// $options[$id] value from the db

echo "<select multiple='multiple' id='$id' class='" . ( empty( $class ) ? 'all-options' : $class ) . "' name='{$setting_id}[$id][]'>";
foreach ( $option_values as $k => $v ) {

    echo "<option value='$k' " . ( in_array( $k, ( empty( $options[ $id ] ) ? array( ) : $options[ $id ] ) ) ? 'selected' : '' )  . ">$v</option>";
}
echo "</select><br>";