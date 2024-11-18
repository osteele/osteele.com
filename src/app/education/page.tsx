import {
  AcademicCapIcon,
  BeakerIcon,
  BookOpenIcon,
  CommandLineIcon,
  CodeBracketIcon,
  ComputerDesktopIcon,
  CubeIcon,
  LinkIcon,
  WrenchScrewdriverIcon,
} from "@heroicons/react/24/outline";
import Link from "next/link";

interface Course {
  name: string;
  description: string;
  institution: "Olin College" | "NYU Shanghai";
  developed?: boolean;
  materials?: boolean;
  icon: React.ForwardRefExoticComponent<any>;
  color: string;
  materialUrl?: string;
}

export default function Education() {
  const courses: Course[] = [
    {
      name: "Woodworking for Art and Design",
      description:
        "An artistic approach to woodworking, focusing on design principles, joinery techniques, and practical shop skills.",
      institution: "NYU Shanghai",
      developed: true,
      materials: true,
      icon: WrenchScrewdriverIcon,
      color: "text-orange-600 dark:text-orange-400",
      materialUrl: "https://woodshop.olin.edu",
    },
    {
      name: "Creative Coding",
      description:
        "A project-based course teaching the basics of programming, web technologies, and computational thinking, with a focus on artistic and business applications, including elements of color, sound, and animation theory.",
      institution: "NYU Shanghai",
      developed: true,
      materials: true,
      icon: CodeBracketIcon,
      color: "text-purple-600 dark:text-purple-400",
      materialUrl: "https://github.com/osteele/creative-coding",
    },
    {
      name: "Interaction Lab",
      description:
        "A course on physical computing and interactive design, fulfilling university core Algorithmic Thinking credit requirements.",
      institution: "NYU Shanghai",
      developed: false,
      materials: true,
      icon: CommandLineIcon,
      color: "text-blue-600 dark:text-blue-400",
      materialUrl: "https://interactionlab.olin.edu",
    },
    {
      name: "Movement Practices and Computing",
      description:
        "An exploration of alternative human-computer interactions, focusing on body and gesture-based interfaces, with fundamentals in electronics and programming to enhance the expressive capabilities of computation.",
      institution: "NYU Shanghai",
      developed: true,
      materials: true,
      icon: CubeIcon,
      color: "text-green-600 dark:text-green-400",
      materialUrl: "https://github.com/osteele/movement-computing",
    },
    {
      name: "Hacking the Library",
      description:
        "Students develop software and hardware solutions to enhance library relevance, focusing on large-scale project development and practical applications in a collegiate environment.",
      institution: "Olin College",
      developed: true,
      materials: false,
      icon: BeakerIcon,
      color: "text-amber-600 dark:text-amber-400",
    },
    {
      name: "Software Design",
      description:
        "An introductory course in software design, covering Python programming, computational thinking, and the lifecycle management of software projects.",
      institution: "Olin College",
      developed: false,
      materials: true,
      icon: ComputerDesktopIcon,
      color: "text-indigo-600 dark:text-indigo-400",
      materialUrl: "https://sd2020.olin.edu",
    },
    {
      name: "Foundations of Computer Science",
      description:
        "A foundational course covering key areas such as automata theory, data structures, algorithms, programming languages, computability, and complexity theory.",
      institution: "Olin College",
      developed: false,
      materials: true,
      icon: AcademicCapIcon,
      color: "text-rose-600 dark:text-rose-400",
      materialUrl: "https://github.com/osteele/focs",
    },
  ];

  const developedCourses = courses.filter((course) => course.developed);
  const coursesWithMaterials = courses.filter((course) => course.materials);
  const taughtCourses = courses;

  const CourseCard = ({ course }: { course: Course }) => {
    const colorName = course.color.split("-")[1];

    return (
      <li
        className={`p-6 rounded-lg transition-all duration-200
          bg-gradient-to-br from-${colorName}-50 to-${colorName}-100/50
          dark:from-${colorName}-950/30 dark:to-${colorName}-900/20
          shadow-md shadow-${colorName}-200/50 dark:shadow-${colorName}-900/30
          hover:shadow-lg hover:shadow-${colorName}-200/60 dark:hover:shadow-${colorName}-900/40`}
      >
        <div className="flex justify-between items-start mb-2">
          <div className="flex items-center gap-3">
            <course.icon className={`h-6 w-6 ${course.color}`} />
            <h3 className="text-lg font-semibold">{course.name}</h3>
          </div>
          <span
            className={`text-xs font-medium px-2 py-0.5 rounded-full whitespace-nowrap
              bg-${colorName}-100/80 text-${colorName}-800
              dark:bg-${colorName}-900/50 dark:text-${colorName}-200
              border border-${colorName}-200 dark:border-${colorName}-800/30`}
          >
            {course.institution}
          </span>
        </div>
        <p className="text-gray-600 dark:text-gray-300 text-sm mb-3">
          {course.description}
        </p>
        {course.materialUrl && (
          <Link
            href={course.materialUrl}
            className={`inline-flex items-center gap-1 text-sm ${course.color}
              hover:text-${colorName}-800 dark:hover:text-${colorName}-300`}
          >
            <LinkIcon className="h-4 w-4" />
            Course Materials
          </Link>
        )}
      </li>
    );
  };

  return (
    <div className="max-w-4xl mx-auto p-8">
      <div className="mb-12">
        <h1 className="text-4xl font-bold mb-4">Education</h1>
        <p className="text-xl text-gray-600 dark:text-gray-300">
          Teaching experiences at Olin College and NYU Shanghai, and educational
          resources
        </p>
      </div>

      <div className="space-y-16">
        <section>
          <div className="flex items-center gap-3 mb-6">
            <BeakerIcon className="h-6 w-6 text-blue-600" />
            <h2 className="text-2xl font-semibold">Courses Developed</h2>
          </div>
          <ul className="grid gap-6 md:grid-cols-2">
            {developedCourses.map((course) => (
              <CourseCard key={course.name} course={course} />
            ))}
          </ul>
        </section>

        <section>
          <div className="flex items-center gap-3 mb-6">
            <AcademicCapIcon className="h-6 w-6 text-green-600" />
            <h2 className="text-2xl font-semibold">Courses Taught</h2>
          </div>
          <ul className="grid gap-6 md:grid-cols-2">
            {taughtCourses.map((course) => (
              <CourseCard key={course.name} course={course} />
            ))}
          </ul>
        </section>

        <section>
          <div className="flex items-center gap-3 mb-6">
            <BookOpenIcon className="h-6 w-6 text-purple-600" />
            <h2 className="text-2xl font-semibold">Course Materials</h2>
          </div>
          <ul className="grid gap-6 md:grid-cols-2">
            {coursesWithMaterials.map((course) => (
              <CourseCard key={course.name} course={course} />
            ))}
          </ul>
        </section>
      </div>
    </div>
  );
}
