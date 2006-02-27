<!--
  Author: Oliver Steele
  Copyright: Copyright 2006 Oliver Steele.  All rights reserved.
  Homepage: http://osteele.com/tools/rematch
  License: Artistic License.
-->

<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
   <script type="text/javascript" src="/javascripts/fvlogger/logger.js"></script>
   <script type="text/javascript" src="bezier.js"></script>
   <script type="text/javascript" src="path.js"></script>
   <script type="text/javascript" src="bezier-demo.js"></script>
 </head>
 <body>

   <a href="#" onclick="animateBezier(); return false">Animate</a><br/>
   
   <canvas id="canvas" width="310" height="240">
   </canvas>
   
   <script type="text/javascript">
     var canvas = document.getElementById('canvas');
     var ctx = canvas.getContext('2d');
     drawBeziers(ctx);
     
     // please don't mistake me for a real animation system!
     var animation = {timer: null, value: null, step: null};
     
     function stepAnimation() {
       // background
       ctx.clearRect(0, 0, canvas.width, canvas.height);
       drawBeziers(ctx);
       // foreground
       var t = animation.value += animation.step;
       if (t >= 1.0) {
	 clearInterval(animation.timer);
	 return;
       }
       drawBezierSamples(ctx, t);
     }
     
     function animateBezier() {
       var interval = 1;
       var seconds = 5;
       var framerate = 30;
       animation.value = 0;
       animation.step = interval / (seconds * framerate);
       animation.timer = animation.timer || setInterval(stepAnimation, 1000/framerate);
     }
   </script>
   
 </body>
</html>