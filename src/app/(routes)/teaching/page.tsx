import { PageLayout } from "@/components/page-layout";
import { TeachingBanner } from "@/components/teaching-banner";
import Link from "next/link";

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
                <svg
                  className="w-8 h-8"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                >
                  <path d="M12 3L1 9l11 6l11-6l-11-6zM1 9v6l11 6l11-6V9L12 15L1 9z" />
                </svg>
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
                  <svg
                    className="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" />
                  </svg>
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
                  <svg
                    className="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" />
                  </svg>
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
                  <svg
                    className="w-4 h-4"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" />
                  </svg>
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
                <svg
                  className="w-8 h-8"
                  viewBox="0 0 24 24"
                  fill="currentColor"
                >
                  <path d="M20 12h-3V3h-2v9h-3v3h3v6h2v-6h3v-3zM4 9h3V3h2v6h3v3h-3v6H7v-6H4V9z" />
                </svg>
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
