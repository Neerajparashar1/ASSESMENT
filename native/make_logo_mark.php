<?php
// =====================================================================
//  Crop the official ITM GOI logo (itmgoi.in/images/ITMGOILogo.png,
//  608x320) down to just the "ITM" bars mark - the tagline lines are
//  illegible at navbar size. Output: native/assets/itm-logo-mark.png
//  (transparent), then copied where Apache serves it.
//
//  Band analysis of the source (see scratchpad/analyze_logo.php):
//    y 0..159  = the ITM bars mark      (x ink ~80..524)
//    y 176..313 = 3 tagline lines + quote  <- dropped
// =====================================================================
$src = $argv[1] ?? (__DIR__ . '/assets/itm-logo.png');
$out = $argv[2] ?? (__DIR__ . '/assets/itm-logo-mark.png');

$im = imagecreatefrompng($src);
if (!$im) { fwrite(STDERR, "cannot read $src\n"); exit(1); }
$W = imagesx($im); $H = imagesy($im);

// crop box for the mark only (with a little breathing room)
$cx = 66;
$cy = 0;
$cw = min($W - $cx, 470);
$ch = 166;

$dst = imagecreatetruecolor($cw, $ch);
imagesavealpha($dst, true);
imagealphablending($dst, false);
imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
imagealphablending($dst, true);
imagecopy($dst, $im, 0, 0, $cx, $cy, $cw, $ch);

// tighten: trim fully-transparent rows/cols from the crop
$minx = $cw; $maxx = 0; $miny = $ch; $maxy = 0;
for ($y = 0; $y < $ch; $y++) {
    for ($x = 0; $x < $cw; $x++) {
        $a = (imagecolorat($dst, $x, $y) >> 24) & 0x7F;
        if ($a < 100) {
            if ($x < $minx) $minx = $x;
            if ($x > $maxx) $maxx = $x;
            if ($y < $miny) $miny = $y;
            if ($y > $maxy) $maxy = $y;
        }
    }
}
$pad = 6;
$minx = max(0, $minx - $pad); $miny = max(0, $miny - $pad);
$maxx = min($cw - 1, $maxx + $pad); $maxy = min($ch - 1, $maxy + $pad);
$tw = $maxx - $minx + 1; $th = $maxy - $miny + 1;

$final = imagecreatetruecolor($tw, $th);
imagesavealpha($final, true);
imagealphablending($final, false);
imagefill($final, 0, 0, imagecolorallocatealpha($final, 0, 0, 0, 127));
imagealphablending($final, true);
imagecopy($final, $dst, 0, 0, $minx, $miny, $tw, $th);

imagepng($final, $out, 6);
echo "wrote $out  ({$tw}x{$th}, cropped from {$W}x{$H})\n";
