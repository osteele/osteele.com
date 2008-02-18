Adds TrackBack support to Drupal. Sending and retrieval of TrackBacks are supported either using POST or GET.

About TrackBack:
  "In a nutshell, TrackBack was designed to provide a method of
   notification between websites: it is a method of person A saying to
   person B, "This is something you may be interested in." To do that,
   person A sends a TrackBack ping to person B."
  - Mena and Ben Trott (http://www.movabletype.org/trackback/beginners/)

Specification:
  http://www.movabletype.org/docs/mttrackback.html

Limitations:
  - Currently only supports sending a TrackBack to one site.
  - No feedback if TrackBack was successful or not.
  - Doesn't try to auto-discover TrackBack if sending fails.

  All of this will hopefully be fixed in a future version.