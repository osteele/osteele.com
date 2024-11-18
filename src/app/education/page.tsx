import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import Link from "next/link";
import { PageLayout } from "@/components/page-layout";

interface EducationPageProps {
  tab?: "teaching" | "materials";
}

export default function EducationPage({
  tab = "teaching",
}: EducationPageProps) {
  return (
    <PageLayout title="Teaching">
      <div className="relative mb-12">
        <div className="absolute inset-0 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-950/30 dark:to-purple-950/30 -z-10" />
        <div className="max-w-4xl mx-auto py-12">
          <h1 className="text-5xl md:text-6xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">
            Teaching & Education
          </h1>
          <p className="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-2xl leading-relaxed">
            Exploring the intersection of technology, design, and learning
            through hands-on courses and educational resources.
          </p>

          <div className="absolute right-0 top-0 w-64 h-64 opacity-10 dark:opacity-5">
            <svg viewBox="0 0 200 200" className="w-full h-full text-blue-600">
              <path
                fill="currentColor"
                opacity="0.3"
                d="M45.3,-59.1C58.9,-51.1,70.3,-37.7,75.2,-22.1C80.1,-6.5,78.5,11.2,71.3,26.3C64.1,41.4,51.3,53.8,36.5,61.5C21.7,69.2,4.9,72.1,-11.1,69.7C-27.1,67.3,-42.3,59.5,-54.1,47.7C-65.9,35.9,-74.3,20,-76.1,3C-77.9,-14,-73.1,-31.1,-62.3,-43.6C-51.5,-56.1,-34.7,-64,-18.1,-67.7C-1.5,-71.3,14.9,-70.7,29.8,-67.1C44.8,-63.5,58.3,-56.9,45.3,-59.1Z"
                transform="translate(100 100)"
              />

              <path
                fill="currentColor"
                d="M100 40l-50 25 50 25 50-25-50-25zM60 80v20l40 20 40-20v-20l-40 20-40-20z"
              />

              <path
                fill="currentColor"
                d="M50 120h30c5.5 0 10 4.5 10 10v20H50v-30z"
              />
              <path
                fill="currentColor"
                d="M150 120h-30c-5.5 0-10 4.5-10 10v20h40v-30z"
              />
              <path
                fill="none"
                stroke="currentColor"
                strokeWidth="4"
                d="M90 130c0-5.5 4.5-10 10-10s10 4.5 10 10"
              />

              <path
                fill="currentColor"
                transform="rotate(45, 140, 60)"
                d="M130 50h20l5 5-25 25-5-5z"
              />

              <path
                d="M40 70l-10 10 10 10M60 70l10 10-10 10"
                stroke="currentColor"
                strokeWidth="4"
                strokeLinecap="round"
                strokeLinejoin="round"
                fill="none"
              />
            </svg>
          </div>
        </div>
      </div>

      <Tabs defaultValue={tab} className="w-full">
        <TabsList className="grid w-full grid-cols-2 mb-8 p-1 bg-gray-100/50 dark:bg-gray-800/50 rounded-lg">
          <TabsTrigger
            asChild
            value="teaching"
            className="data-[state=active]:bg-white dark:data-[state=active]:bg-gray-700 data-[state=active]:shadow-sm transition-all"
          >
            <Link href="/education" className="px-8 py-3">
              Courses
            </Link>
          </TabsTrigger>
          <TabsTrigger
            asChild
            value="materials"
            className="data-[state=active]:bg-white dark:data-[state=active]:bg-gray-700 data-[state=active]:shadow-sm transition-all"
          >
            <Link href="/teaching-materials" className="px-8 py-3">
              Educational Materials
            </Link>
          </TabsTrigger>
        </TabsList>

        <TabsContent value="teaching" className="space-y-8">
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
                  href="#"
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
                  href="#"
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
                  href="#"
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
        </TabsContent>

        <TabsContent value="materials" className="space-y-8">
          <section>
            <h2 className="text-3xl font-semibold mb-6">Learning Resources</h2>
            <div className="p-6 rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-500/5">
              <h3 className="text-xl font-semibold mb-4">p5.js Resources</h3>
              <p className="text-gray-600 dark:text-gray-300 mb-4">
                Comprehensive guides for learning p5.js and JavaScript,
                including tutorials, examples, and setup instructions.
              </p>
              <a
                href="https://notes.osteele.com/p5js"
                className="text-blue-700 dark:text-blue-300 hover:underline"
              >
                View p5.js Learning Resources →
              </a>
            </div>
          </section>

          <section>
            <h2 className="text-3xl font-semibold mb-6">
              Interactive Learning Tools
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="p-6 rounded-lg bg-gradient-to-br from-amber-500/20 to-amber-500/5">
                <h3 className="text-xl font-semibold mb-4">
                  Programming Concepts
                </h3>
                <ul className="space-y-3">
                  <li>
                    <a
                      href="https://osteele.github.io/map-explorer/"
                      className="text-amber-700 dark:text-amber-300 hover:underline"
                    >
                      Map Explorer
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Interactive visualization of the map function in Arduino,
                      Processing, and p5.js
                    </p>
                  </li>
                  <li>
                    <a
                      href="https://osteele.github.io/pwm-explorer/"
                      className="text-amber-700 dark:text-amber-300 hover:underline"
                    >
                      PWM Explorer
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Interactive visualization of Pulse Width Modulation with
                      adjustable frequency and duty cycle
                    </p>
                  </li>
                </ul>
              </div>

              <div className="p-6 rounded-lg bg-gradient-to-br from-orange-500/20 to-orange-500/5">
                <h3 className="text-xl font-semibold mb-4">
                  Development Tools
                </h3>
                <ul className="space-y-3">
                  <li>
                    <a
                      href="https://github.com/osteele/p5-server"
                      className="text-orange-700 dark:text-orange-300 hover:underline"
                    >
                      p5 server
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Development server with live reload, automatic library
                      inclusion, and sketch template creation
                    </p>
                  </li>
                  <li>
                    <a
                      href="https://marketplace.visualstudio.com/items?itemName=osteele.p5-server"
                      className="text-orange-700 dark:text-orange-300 hover:underline"
                    >
                      P5 Server VSCode Extension
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Integrated p5.js development environment with browser
                      preview and sketch explorer
                    </p>
                  </li>
                </ul>
              </div>
            </div>
          </section>

          <section>
            <h2 className="text-3xl font-semibold mb-6">
              Libraries & Examples
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="p-6 rounded-lg bg-gradient-to-br from-purple-500/20 to-purple-500/5">
                <h3 className="text-xl font-semibold mb-4">p5.js Libraries</h3>
                <ul className="space-y-3">
                  <li>
                    <a
                      href="https://github.com/osteele/p5.layers"
                      className="text-purple-700 dark:text-purple-300 hover:underline"
                    >
                      p5.layers
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Simplifies graphics layers management and createGraphics
                      workflow
                    </p>
                  </li>
                  <li>
                    <a
                      href="https://github.com/osteele/p5.rotate-about"
                      className="text-purple-700 dark:text-purple-300 hover:underline"
                    >
                      p5.rotate-about
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Adds rotateAbout() and scaleAbout() for point-centric
                      transformations
                    </p>
                  </li>
                  <li>
                    <a
                      href="https://github.com/osteele/p5.vector-arguments"
                      className="text-purple-700 dark:text-purple-300 hover:underline"
                    >
                      p5.vector-arguments
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Enables p5.Vector arguments in shape functions
                    </p>
                  </li>
                </ul>
              </div>

              <div className="p-6 rounded-lg bg-gradient-to-br from-indigo-500/20 to-indigo-500/5">
                <h3 className="text-xl font-semibold mb-4">
                  Motion & Interaction
                </h3>
                <ul className="space-y-3">
                  <li>
                    <a
                      href="https://github.com/osteele/p5pose"
                      className="text-indigo-700 dark:text-indigo-300 hover:underline"
                    >
                      p5pose
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Simplified starter template for p5.js + ml5.js PoseNet
                      projects
                    </p>
                  </li>
                  <li>
                    <a
                      href="https://github.com/osteele/imu-tools"
                      className="text-indigo-700 dark:text-indigo-300 hover:underline"
                    >
                      IMU Tools
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Tools for wireless IMU data transmission and visualization
                    </p>
                  </li>
                </ul>
              </div>
            </div>
          </section>

          <section>
            <h2 className="text-3xl font-semibold mb-6">Teaching Tools</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="p-6 rounded-lg bg-gradient-to-br from-emerald-500/20 to-emerald-500/5">
                <h3 className="text-xl font-semibold mb-4">Classroom Tools</h3>
                <ul className="space-y-3">
                  <li>
                    <a
                      href="https://github.com/osteele/section-wheel"
                      className="text-emerald-700 dark:text-emerald-300 hover:underline"
                    >
                      Section Wheel
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Animated wheel for selecting student presentation order
                    </p>
                  </li>
                  <li>
                    <a
                      href="https://github.com/osteele/callgraph"
                      className="text-emerald-700 dark:text-emerald-300 hover:underline"
                    >
                      Callgraph
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Jupyter notebook extension for visualizing function call
                      graphs
                    </p>
                  </li>
                </ul>
              </div>

              <div className="p-6 rounded-lg bg-gradient-to-br from-teal-500/20 to-teal-500/5">
                <h3 className="text-xl font-semibold mb-4">
                  Assignment Management
                </h3>
                <ul className="space-y-3">
                  <li>
                    <a
                      href="https://github.com/osteele/multiclone"
                      className="text-teal-700 dark:text-teal-300 hover:underline"
                    >
                      multiclone
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Fast bulk cloning of GitHub classroom assignments
                    </p>
                  </li>
                  <li>
                    <a
                      href="https://github.com/osteele/nbcollate"
                      className="text-teal-700 dark:text-teal-300 hover:underline"
                    >
                      nbcollate
                    </a>
                    <p className="text-sm text-gray-600 dark:text-gray-300">
                      Combines student Jupyter notebooks for efficient review
                    </p>
                  </li>
                </ul>
              </div>
            </div>
          </section>
        </TabsContent>
      </Tabs>
    </PageLayout>
  );
}
