import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";

export default async function EducationPage({
  searchParams,
}: {
  searchParams: Promise<{ tab?: string }>;
}) {
  const tab = (await searchParams).tab || "teaching";

  return (
    <div className="flex flex-col min-h-screen">
      <main
        className="flex-1 flex flex-col items-center gap-8 max-w-5xl mx-auto p-8 sm:p-20
        bg-gradient-to-b from-gray-50 to-white dark:from-gray-950 dark:to-gray-900"
      >
        <h1 className="text-5xl font-bold mt-16 mb-4">Teaching</h1>

        <Tabs defaultValue={tab} className="w-full">
          <TabsList className="grid w-full grid-cols-2 mb-8">
            <TabsTrigger value="teaching">Teaching</TabsTrigger>
            <TabsTrigger value="materials">Educational Materials</TabsTrigger>
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
                    A project-based course teaching the basics of programming,
                    web technologies, and computational thinking, with a focus
                    on artistic and business applications.
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
                    <h3 className="text-xl font-semibold">
                      Hacking the Library
                    </h3>
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
                    <h3 className="text-lg font-semibold">
                      Hacking the Library
                    </h3>
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
              <h2 className="text-3xl font-semibold mb-6">
                Learning Resources
              </h2>
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
                        Interactive visualization of the map function in
                        Arduino, Processing, and p5.js
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
                  <h3 className="text-xl font-semibold mb-4">
                    p5.js Libraries
                  </h3>
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
                        Tools for wireless IMU data transmission and
                        visualization
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
                  <h3 className="text-xl font-semibold mb-4">
                    Classroom Tools
                  </h3>
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
      </main>
    </div>
  );
}
