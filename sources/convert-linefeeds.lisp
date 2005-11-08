;;;; Author: Oliver Steele
;;;; Source: http://osteele.com/sources/convert-linefeeds.lisp
;;;; Version: 1.0
;;;; Date: 10/22/98
;;;;
;;;; Copyright 1998 by Oliver Steele.
;;;; You have my permission to use this freely, as long as you keep the attribution. -- Oliver Steele
;;;;
;;;; This file patches Macintosh Common Lisp to add two pieces of functionality:
;;;; - If *FRED-SELECTS-OLD-WINDOW* is true, invoking Fred on a file that is already
;;;;   displayed will select the window that displays it.
;;;; - If *CONVERT-UNIX-LINEFEEDS* is true, invoking Fred on a file that contains UNIX-style
;;;;    linefeeds will ask whether into convert them to MacOS carriage returns.

(in-package ccl)

(defvar *fred-selects-old-window* t
  "If true, invoking Fred on a file that is already displayed will select the window
   that displays it.  Otherwise a dialog box will ask how to proceed.  (This is MCL's
   distribution behavior.)")

(defvar *convert-unix-linefeeds* t
  "If true, invoking Fred on a file that contains UNIX-style linefeeds will ask whether
   to convert them into MacOS carriage returns.")

(advise fred
        (destructuring-bind (&optional pathname new-window) arglist
          (let ((window (or (and pathname
                                 *fred-selects-old-window*
                                 (not new-window)
                                 (pathname-to-window pathname))
                            (:do-it))))
            (let ((buffer (fred-buffer window)))
              (when (and *convert-unix-linefeeds*
                         buffer
                         (print (buffer-char-pos buffer #\lf))
                         (y-or-n-dialog (format nil "~A contains UNIX linefeeds.  Convert them to carriage returns?" pathname)
                                        :yes-text "Convert" :cancel-text nil))
                (buffer-replace buffer #\lf #\cr)
                (fred-update window)))
            window))
        :when :around :name convert-linefeeds)

(defun buffer-replace (buffer search replace &optional (start 0))
  (let ((pos (buffer-char-pos buffer search :start start)))
    (when pos
      (buffer-char-replace buffer replace pos)
      (buffer-replace buffer search replace pos))))
