<?php
/* This PHP file will be enqueued (indirectly) as if it were a regular javascript file.
It contains JS arrays/objects that change as the user updates their settings in Microthemer.
These include: media queries, default CSS units, suggested values, design packs...
*/

$data = '';

// Only run performance functions if dev_mode is enabled
$dev_mode = TVR_DEV_MODE ? 'true' : 'false';
$data.= 'TVR_DEV_MODE = ' . $dev_mode . ';' . "\n\n";

// add the design pack directories to the TvrCombo object already defined in the static version JS file
$directories = array();
foreach ($this->file_structure as $dir => $array) {
    if (!empty($dir)) {
        $directories[] = $this->readable_name($dir);
    }
}
$data.= 'TvrCombo.directories = ' . json_encode($directories) . ';' . "\n\n";

// user's current media queries (combined with All Devices)
$data.= 'var TvrMQsCombined = ' . json_encode($this->combined_devices()) . ';' . "\n\n";

// full preferences (can replace the above)
$data.= 'var TvrPref = ' . json_encode($this->preferences) . ';' . "\n\n";

// output JS
echo $data;