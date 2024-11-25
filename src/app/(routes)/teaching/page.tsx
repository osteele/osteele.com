import { PageLayout } from "@/components/page-layout";
import { TeachingBanner } from "@/components/teaching-banner";
import Link from "next/link";
import { FaGraduationCap, FaChalkboardTeacher } from "react-icons/fa";
import { PiBooks } from "react-icons/pi";

export default function TeachingPage() {
  return (
    <PageLayout title="Teaching">
      <TeachingBanner />

      <div className="container">
        <div className="grid w-full grid-cols-2 mb-8 p-1 bg-gray-100/50 dark:bg-gray-800/50 rounded-lg">
          <Link
            href="/teaching"
            className="px-8 py-3 bg-white dark:bg-gray-700 shadow-sm transition-all text-center"
          >
            Courses
          </Link>
          <Link
            href="/teaching-materials"
            className="px-8 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-center"
          >
            Teaching Materials
          </Link>
        </div>

        <div className="space-y-8">
          <section>
            <h2 className="text-3xl font-semibold mb-6 flex items-center gap-2">
              <span className="text-blue-500">
                <FaGraduationCap className="w-8 h-8" />
              </span>
              Courses Developed
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="p-6 rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-500/5">
                <div className="flex items-center justify-between mb-4">
                  <h3 className="text-xl font-semibold">
                    Woodworking for Art and Design
                  </h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    NYU Shanghai
                  </span>
                </div>
                <p className="text-gray-600 dark:text-gray-300 mb-4">
                  An artistic approach to woodworking, focusing on design
                  principles, joinery techniques, and practical shop skills.
                </p>
                <a
                  href="https://notes.osteele.com/courses"
                  className="text-orange-500 hover:text-orange-600 flex items-center gap-1"
                >
                  <PiBooks className="w-4 h-4" />
                  Course Materials
                </a>
              </div>

              <div className="p-6 rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-500/5">
                <div className="flex items-center justify-between mb-4">
                  <h3 className="text-xl font-semibold">Creative Coding</h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    NYU Shanghai
                  </span>
                </div>
                <p className="text-gray-600 dark:text-gray-300 mb-4">
                  A project-based course teaching the basics of programming, web
                  technologies, and computational thinking, with a focus on
                  artistic and business applications.
                </p>
                <a
                  href="https://notes.osteele.com/p5js"
                  className="text-purple-500 hover:text-purple-600 flex items-center gap-1"
                >
                  <PiBooks className="w-4 h-4" />
                  Course Materials
                </a>
              </div>

              <div className="p-6 rounded-lg bg-gradient-to-br from-green-500/20 to-green-500/5">
                <div className="flex items-center justify-between mb-4">
                  <h3 className="text-xl font-semibold">
                    Movement Practices and Computing
                  </h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    NYU Shanghai
                  </span>
                </div>
                <p className="text-gray-600 dark:text-gray-300 mb-4">
                  An exploration of alternative human-computer interactions,
                  focusing on body and gesture-based interfaces.
                </p>
                <a
                  href="https://notes.osteele.com/posenet"
                  className="text-green-500 hover:text-green-600 flex items-center gap-1"
                >
                  <PiBooks className="w-4 h-4" />
                  Course Materials
                </a>
              </div>

              <div className="p-6 rounded-lg bg-gradient-to-br from-amber-500/20 to-amber-500/5">
                <div className="flex items-center justify-between mb-4">
                  <h3 className="text-xl font-semibold">Hacking the Library</h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    Olin College
                  </span>
                </div>
                <p className="text-gray-600 dark:text-gray-300 mb-4">
                  Students develop software and hardware solutions to enhance
                  library relevance and practical applications.
                </p>
              </div>
            </div>
          </section>

          <section>
            <h2 className="text-3xl font-semibold mb-6 flex items-center gap-2">
              <span className="text-green-500">
                <FaChalkboardTeacher className="w-8 h-8" />
              </span>
              Courses Taught
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="p-4 rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-500/5">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-semibold">
                    Woodworking for Art and Design
                  </h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    NYU Shanghai
                  </span>
                </div>
              </div>

              <div className="p-4 rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-500/5">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-semibold">Creative Coding</h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    NYU Shanghai
                  </span>
                </div>
              </div>

              <div className="p-4 rounded-lg bg-gradient-to-br from-green-500/20 to-green-500/5">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-semibold">
                    Movement Practices and Computing
                  </h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    NYU Shanghai
                  </span>
                </div>
              </div>

              <div className="p-4 rounded-lg bg-gradient-to-br from-amber-500/20 to-amber-500/5">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-semibold">Hacking the Library</h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    Olin College
                  </span>
                </div>
              </div>

              <div className="p-4 rounded-lg bg-gradient-to-br from-indigo-500/20 to-indigo-500/5">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-semibold">Software Design</h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    Olin College
                  </span>
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-300">
                  Python programming, computational thinking, and software
                  lifecycle management
                </p>
              </div>

              <div className="p-4 rounded-lg bg-gradient-to-br from-violet-500/20 to-violet-500/5">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-semibold">
                    Foundations of Computer Science
                  </h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    Olin College
                  </span>
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-300">
                  Automata theory, data structures, algorithms, and complexity
                  theory
                </p>
              </div>

              <div className="p-4 rounded-lg bg-gradient-to-br from-rose-500/20 to-rose-500/5">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-lg font-semibold">Application Lab</h3>
                  <span className="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">
                    NYU Shanghai
                  </span>
                </div>
                <p className="text-sm text-gray-600 dark:text-gray-300">
                  Product design and web development through iterative
                  prototyping and market validation
                </p>
              </div>
            </div>
          </section>
        </div>
      </div>
    </PageLayout>
  );
}
