;;;; Author: Oliver Steele, steele@cs.brandeis.edu
;;;; Source: http://osteele.com/sources/clos-koza.lisp
;;;; Version: 1.0
;;;; Date: 10/22/98
;;;;
;;;; Copyright 1998 by Oliver Steele.
;;;; You have my permission to use this freely, as long as you keep the attribution. -- Oliver Steele
;;;;
;;;; A CLOS wrapper for KOZA, John Koza's genetic programming package (koza_gp.cl).
;;;; KOZA is available from the CMU AI Repository, at http://www.cs.cmu.edu/Groups/AI/html/repository.html.

(in-package cl-user)

(defclass problem ()
  ((number-of-fitness-cases :initarg :number-of-fitness-cases :type integer :initform 10)
   (max-depth-for-new-individuals :initarg :max-depth-for-new-individuals :type integer :initform 6)
   (max-depth-for-individuals-after-crossover :initarg :max-depth-for-individuals-after-crossover :type integer :initform 17)
   (fitness-proportionate-reproduction-fraction :initarg :fitness-proportionate-reproduction-fraction :type number :initform 0.1)
   (crossover-at-any-point-fraction :initarg :crossover-at-any-point-fraction :type number :initform 0.2)
   (crossover-at-function-point-fraction :initarg :crossover-at-function-point-fraction :type number :initform 0.2)
   (max-depth-for-new-subtrees-in-mutants :initarg :max-depth-for-new-subtrees-in-mutants :type integer :initform 4)
   (method-of-selection :initarg :method-of-selection :initform :fitness-proportionate)
   (method-of-generation :initarg :method-of-generation :initform :ramped-half-and-half)))

(defmethod fitness-cases ((problem problem))
  :undefined)

(defmethod program-parameters ((problem problem))
  '())

(defmethod program-local-functions ((problem problem))
  nil)

(defmethod function-set ((problem problem))
  nil)

(defmethod terminal-set ((problem problem))
  nil)

(defmethod compile-program ((problem problem) program)
  (compile nil
   `(lambda ,(program-parameters problem)
      (block program
        (labels ,(program-local-functions problem)
          ,program)))))

(defmethod solve-problem ((class class) &rest keys)
  (apply #'solve-problem (make-instance class) keys))

(defmethod solve-problem ((class-name symbol) &rest keys)
  (apply #'solve-problem (find-class class-name) keys))

(defmethod solve-problem ((problem problem) &key
                          (random-seed 1.0)
                          (maximum-generations 20)
                          (population-size 20)
                          (initial-population nil)
                          (return-population-p nil))
  (let ((*number-of-fitness-cases* (slot-value problem 'number-of-fitness-cases))
        (*max-depth-for-new-individuals* (slot-value problem 'max-depth-for-new-individuals))
        (*max-depth-for-individuals-after-crossover* (slot-value problem 'max-depth-for-individuals-after-crossover))
        (*fitness-proportionate-reproduction-fraction* (slot-value problem 'fitness-proportionate-reproduction-fraction))
        (*crossover-at-any-point-fraction* (slot-value problem 'crossover-at-any-point-fraction))
        (*crossover-at-function-point-fraction* (slot-value problem 'crossover-at-function-point-fraction))
        (*max-depth-for-new-subtrees-in-mutants* (slot-value problem 'max-depth-for-new-subtrees-in-mutants))
        (*method-of-selection* (slot-value problem 'method-of-selection))
        (*method-of-generation* (slot-value problem 'method-of-generation)))
    (labels ((adapt-function-set ()
               (let ((fns (function-set problem)))
                 (values (mapcar #'first fns) (mapcar #'second fns))))
             (adaptor ()
               (values #'adapt-function-set
                       (lambda () (terminal-set problem))
                       (lambda () (fitness-cases problem))
                       (lambda (program fitness-cases)
                         (multiple-value-bind (fitness hits)
                                              (evaluate-fitness problem program fitness-cases)
                           (values fitness hits)))
                       (lambda ())      ; parameter setter
                       (lambda (&rest args) (apply #'terminatep problem args)))))
      (let ((population (apply #'run-genetic-programming-system #'adaptor random-seed maximum-generations population-size initial-population)))
        (when return-population-p population)))))

(defmethod terminatep ((problem problem) current-generation maximum-generations
                                         best-standardized-fitness best-hits)
  (declare (ignore best-standardized-fitness))
  (or (>= current-generation maximum-generations)
      (>= best-hits *number-of-fitness-cases*)))

;;;
;;; Evaluation against fitness cases
;;;

(defstruct fitness-case
  arguments
  target)

(defmethod evaluate-program-against-fitness-case ((problem problem) function (fitness-case fitness-case))
  (let* ((target (fitness-case-target fitness-case))
         (value (apply function (fitness-case-arguments fitness-case)))
         (matchp (equal value target)))
    (values (if matchp 1 0) matchp value)))

(defmethod evaluate-fitness ((problem problem) program fitness-cases)
  (let ((total-fitness 0.0)
        (hits 0)
        (function (compile-program problem program)))
    (map nil
         (lambda (fitness-case)
           (multiple-value-bind (fitness hitp)
                                (evaluate-program-against-fitness-case problem function fitness-case)
             (incf total-fitness fitness)
             (when hitp (incf hits))))
         fitness-cases)
    (values (- (length fitness-cases) total-fitness) hits)))

(defstruct (numeric-fitness-case (:include fitness-case)))

(defmethod evaluate-program-against-fitness-case ((problem problem) function (fitness-case numeric-fitness-case))
  (let* ((target (fitness-case-target fitness-case))
         (value (apply function (fitness-case-arguments fitness-case)))
         (difference (abs (- target value))))
    (values difference (< difference 0.01) value)))

(defmethod display-evaluation-against-fitness-cases ((class-name symbol) program)
  (let* ((problem (make-instance class-name))
         (function (compile-program problem program))
         (total-fitness 0.0)
         (hits 0))
    (map nil (lambda (fitness-case)
               (multiple-value-bind (fitness hitp value)
                                    (evaluate-program-against-fitness-case problem function fitness-case)
                 (format t "~&~A -> ~A versus ~A: ~A (~:[miss~;hit~])"
                         (fitness-case-arguments fitness-case)
                         value
                         (fitness-case-target fitness-case)
                         fitness hitp)
                 (incf total-fitness fitness)
                 (when hitp (incf hits))))
         (let ((*number-of-fitness-cases* (slot-value problem 'number-of-fitness-cases)))
           (fitness-cases problem)))
    (format t "~&Total: ~A (~A hits)" total-fitness hits)))


#|
Example problems:
;;; Regression Problem for 0.5x**2

(defclass regression-problem (problem)
  ()
  (:default-initargs
   :number-of-fitness-cases 10
   :max-depth-for-new-individuals 6
   :max-depth-for-individuals-after-crossover 17
   :fitness-proportionate-reproduction-fraction 0.1
   :crossover-at-any-point-fraction 0.2
   :crossover-at-function-point-fraction 0.2
   :max-depth-for-new-subtrees-in-mutants 4
   :method-of-selection :fitness-proportionate
   :method-of-generation :ramped-half-and-half
   ))

(defmethod terminal-set ((problem regression-problem))
  '(x :floating-point-random-constant))

(defmethod function-set ((problem regression-problem))
  '((+ 2) (- 2) (* 2) (% 2)))

(defmethod program-parameters ((problem regression-problem))
  '(x))

(defmethod program-local-functions ((problem regression-problem))
  '((% (numerator denominator)
       (if (= 0 denominator) 1 (/ numerator denominator)))))

(defmethod fitness-cases ((problem regression-problem))
  (loop for index below *number-of-fitness-cases*
        for x = (/ index *number-of-fitness-cases*)
        collect (make-fitness-case
                 :arguments (list x)
                 :target (* 0.5 x x))))

;(solve-problem 'regression-problem)

;=========================================================================

;;; Boolean 3-Majority-on Problem

(defclass majority-on-problem (problem)
  ()
  (:default-initargs
    :number-of-fitness-cases 8
    :max-depth-for-new-individuals 6
    :max-depth-for-new-subtrees-in-mutants 4
    :max-depth-for-individuals-after-crossover 17
    :fitness-proportionate-reproduction-fraction 0.1
    :crossover-at-any-point-fraction 0.2
    :crossover-at-function-point-fraction 0.7
    :method-of-selection :fitness-proportionate
    :method-of-generation :ramped-half-and-half))

(defmethod terminal-set ((problem majority-on-problem))
  '(d0 d1 d2))

(defmethod function-set ((problem majority-on-problem))
  '((and 2) (and 3) (or 2) (not 1)))

(defmethod program-parameters ((problem majority-on-problem))
  '(d0 d1 d2))

(defmethod fitness-cases ((problem majority-on-problem))
  (let (fitness-cases)
    (dolist (d2 '(t nil))
      (dolist (d1 '(t nil))
        (dolist (d0 '(t nil))
          (push (make-fitness-case
                 :arguments (list d0 d1 d2)
                 :target
                 (>= (+ (if d0 1 0) (if d1 1 0) (if d2 1 0)) 2))
                fitness-cases))))
    fitness-cases))

;(solve-problem 'majority-on-problem)
;(solve-problem 'majority-on-problem :population-size 256)
|#