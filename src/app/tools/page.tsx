import { PageLayout } from "@/components/page-layout";
import Link from "next/link";

export default function ToolsPage() {
  return (
    <PageLayout>
      <div className="max-w-5xl mx-auto">
        <h1 className="text-4xl font-bold mb-8">Tools</h1>

        {/* p5.js Tools */}
        <section className="mb-12">
          <h2 className="text-2xl font-semibold mb-4">
            p5.js Tools & Libraries
          </h2>
          <div className="grid gap-6">
            <div className="p-6 rounded-lg bg-white dark:bg-gray-800">
              <h3 className="text-xl font-semibold mb-2">Development Tools</h3>
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://github.com/osteele/p5-server"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    p5 Server
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Command-line tool that runs p5.js sketches with live reload
                    and automatic library inclusion.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://marketplace.visualstudio.com/items?itemName=osteele.p5server-vscode"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    P5 Server VSCode Extension
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Create and run p5.js sketches within Visual Studio Code with
                    integrated development server and browser.
                  </p>
                </div>
              </div>

              <h3 className="text-xl font-semibold mb-2 mt-6">Libraries</h3>
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://github.com/osteele/p5.layers"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    p5.layers
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Simplifies use of createGraphics and p5.js Renders objects
                    for drawing layers.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/p5.rotate-about"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    p5.rotate-about
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Adds rotateAbout() and scaleAbout() functions for rotating
                    and scaling around a point.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/p5.vector-arguments"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    p5.vector-arguments
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Modifies p5.js Shape functions to accept p5.Vector instances
                    as arguments.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Physical Computing */}
        <section className="mb-12">
          <h2 className="text-2xl font-semibold mb-4">Physical Computing</h2>
          <div className="grid gap-6">
            <div className="p-6 rounded-lg bg-white dark:bg-gray-800">
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://github.com/osteele/imu-tools"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    IMU Tools
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Tools for sending IMU data from ESP32 and receiving it via
                    MQTT or Bluetooth.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/Arduino-BLE-IMU"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Arduino-BLE-IMU
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    ESP32 firmware for relaying BNO055 data wirelessly via MQTT
                    and Bluetooth.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/imu-client-examples"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    IMU Client Examples
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Examples using wireless IMU data to animate 3D models and
                    create data visualizations.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Education Tools */}
        <section className="mb-12">
          <h2 className="text-2xl font-semibold mb-4">Education Tools</h2>
          <div className="grid gap-6">
            <div className="p-6 rounded-lg bg-white dark:bg-gray-800">
              <h3 className="text-xl font-semibold mb-2">For Students</h3>
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://osteele.github.io/map-explorer/"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Map Explorer
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Interactive visualization of the map function in Arduino,
                    Processing, and p5.js.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://osteele.github.io/pwm-explorer/"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    PWM Explorer
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Interactive visualization of Pulse Width Modulation (PWM).
                  </p>
                </div>
              </div>

              <h3 className="text-xl font-semibold mb-2 mt-6">For Educators</h3>
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://github.com/osteele/callgraph"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Callgraph
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Jupyter notebook extension that adds call graphs to
                    functions.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/section-wheel"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Section Wheel
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Interactive wheel for selecting student presentation order.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/multiclone"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Multiclone
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Fast tool for cloning all forks of a repository or GitHub
                    Classroom assignments.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/nbcollate"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    nbcollate
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Combines multiple student Jupyter notebooks into a single
                    organized notebook.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/assignment-dashboard"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Assignment Dashboard
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Dashboard for tracking student Jupyter notebook submissions
                    on GitHub.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Software Development */}
        <section className="mb-12">
          <h2 className="text-2xl font-semibold mb-4">Software Development</h2>
          <div className="grid gap-6">
            <div className="p-6 rounded-lg bg-white dark:bg-gray-800">
              <h3 className="text-xl font-semibold mb-2">Web Publishing</h3>
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://github.com/osteele/liquid"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Liquid Template Engine
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    A pure Go implementation of Shopify Liquid templates.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/gojekyll"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Gojekyll
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    A fast Go implementation of the Jekyll blogging engine.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/scrollshot2pdf"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Scrollshot2PDF
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Convert tall screenshots into multi-page PDFs with
                    intelligent page breaks.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Language Learning */}
        <section className="mb-12">
          <h2 className="text-2xl font-semibold mb-4">Language Learning</h2>
          <div className="grid gap-6">
            <div className="p-6 rounded-lg bg-white dark:bg-gray-800">
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://github.com/osteele/labelingo"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Labelingo
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Annotate UI screenshots with translations for language
                    learning.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/kana-game"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Kana Game
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    An interactive game for learning Japanese kana characters.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Visualizations */}
        <section className="mb-12">
          <h2 className="text-2xl font-semibold mb-4">Visualizations</h2>
          <div className="grid gap-6">
            <div className="p-6 rounded-lg bg-white dark:bg-gray-800">
              <div>
                <Link
                  href="https://github.com/osteele/tone-curve-viz"
                  className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                >
                  Tone Curve Visualizer
                </Link>
                <p className="text-gray-600 dark:text-gray-300">
                  A tool for exploring photo editing controls and their effects
                  on tone curves.
                </p>
              </div>
            </div>
          </div>
        </section>

        {/* Machine Embroidery */}
        <section className="mb-12">
          <h2 className="text-2xl font-semibold mb-4">Machine Embroidery</h2>
          <div className="grid gap-6">
            <div className="p-6 rounded-lg bg-white dark:bg-gray-800">
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://github.com/osteele/stitch-sync"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Stitch Sync
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Watch and convert embroidery files to machine-compatible
                    formats.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/pyembroidery-convert"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Pyembroidery Convert
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    CLI tool for converting between embroidery file formats.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* LLMs */}
        <section className="mb-12">
          <h2 className="text-2xl font-semibold mb-4">LLMs</h2>
          <div className="grid gap-6">
            <div className="p-6 rounded-lg bg-white dark:bg-gray-800">
              <div className="space-y-4">
                <div>
                  <Link
                    href="https://github.com/osteele/claude-chat-viewer"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Claude Chat Viewer
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    View Claude chat conversations from exported JSON files.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/claude-artifact-unpacker"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Claude Artifact Unpacker
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Unpack and organize multi-file projects from Claude&apos;s
                    Artifacts.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/prompt-matrix.js"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Prompt Matrix (JS)
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    JavaScript library for expanding prompt matrix strings.
                  </p>
                </div>
                <div>
                  <Link
                    href="https://github.com/osteele/prompt-matrix.py"
                    className="text-blue-600 dark:text-blue-400 hover:underline font-medium"
                  >
                    Prompt Matrix (Python)
                  </Link>
                  <p className="text-gray-600 dark:text-gray-300">
                    Python package for expanding prompt matrix strings.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </PageLayout>
  );
}
