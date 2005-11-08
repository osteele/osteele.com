<?php
     $m = new SWFMovie();
     $m->setDimension(1200,60);
     $m->setBackground(0, 0x66, 0x66); // minty slashdot green!
     $m->setRate(24.0);

     $s = new SWFShape();
     $s->setLine(20, 0xff, 0, 0);
     $s->drawLineTo(400, 0);
     $s->drawLineTo(400, 400);
     $s->drawLineTo(0, 400);
     $s->drawLineTo(0, 0);

    $m->add($s);

     header("Content-type: application/x-shockwave-flash");
     $m->output();
?>