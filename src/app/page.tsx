import { PageLayout } from "@/components/page-layout";
import Link from "next/link";
import { FiBox } from "react-icons/fi";
import { IoGameControllerOutline } from "react-icons/io5";
import { LuBookOpen, LuWrench } from "react-icons/lu";
import { PiCamera, PiGraduationCap, PiPaintBrush, PiToolbox } from "react-icons/pi";
import { VscCode } from "react-icons/vsc";

type Category = {
  title: string;
  href: string;
  description: string;
  icon: React.ReactNode;
  colorClasses: {
    background: string;
    hover: string;
    icon: string;
  };
  external?: boolean;
};

const CATEGORIES = {
  Products: {
    title: "Product Portfolio",
    href: "/products",
    description:
      "Products and features shipped at Apple, Nest Labs, and innovative startups",
    colorClasses: {
      background:
        "from-blue-100 via-blue-50 to-white dark:from-blue-900/50 dark:via-blue-950/30 dark:to-transparent",
      hover:
        "hover:from-blue-200 hover:via-blue-100 hover:to-blue-50 dark:hover:from-blue-800/50 dark:hover:via-blue-900/30 dark:hover:to-blue-950/10",
      icon: "text-blue-600 dark:text-blue-400",
    },
    icon: <FiBox className="w-8 h-8" />,
  },
  Teaching: {
    title: "Teaching",
    href: "/teaching",
    description:
      "Computing and design courses taught at Olin College and NYU Shanghai",
    colorClasses: {
      background:
        "from-purple-100 via-purple-50 to-white dark:from-purple-900/50 dark:via-purple-950/30 dark:to-transparent",
      hover:
        "hover:from-purple-200 hover:via-purple-100 hover:to-purple-50 dark:hover:from-purple-800/50 dark:hover:via-purple-900/30 dark:hover:to-purple-950/10",
      icon: "text-purple-600 dark:text-purple-400",
    },
    icon: <PiGraduationCap className="w-8 h-8" />,
  },
  Photography: {
    title: "Photography",
    href: "https://osteele.notion.site/photography",
    description: "Capturing moments through travel and street photography",
    external: true,
    colorClasses: {
      background:
        "from-amber-100 via-amber-50 to-white dark:from-amber-900/50 dark:via-amber-950/30 dark:to-transparent",
      hover:
        "hover:from-amber-200 hover:via-amber-100 hover:to-amber-50 dark:hover:from-amber-800/50 dark:hover:via-amber-900/30 dark:hover:to-amber-950/10",
      icon: "text-amber-600 dark:text-amber-400",
    },
    icon: <PiCamera className="w-8 h-8" />,
  },
  Woodworking: {
    title: "Woodworking",
    href: "/woodworking",
    description: "Custom furniture and wooden objects crafted by hand",
    colorClasses: {
      background:
        "from-orange-100 via-orange-50 to-white dark:from-orange-900/50 dark:via-orange-950/30 dark:to-transparent",
      hover:
        "hover:from-orange-200 hover:via-orange-100 hover:to-orange-50 dark:hover:from-orange-800/50 dark:hover:via-orange-900/30 dark:hover:to-orange-950/10",
      icon: "text-orange-600 dark:text-orange-400",
    },
    icon: <PiToolbox className="w-8 h-8" />,
  },
  Software: {
    title: "Software",
    href: "/software",
    description: "Open-source projects and interactive code experiments",
    colorClasses: {
      background:
        "from-green-100 via-green-50 to-white dark:from-green-900/50 dark:via-green-950/30 dark:to-transparent",
      hover:
        "hover:from-green-200 hover:via-green-100 hover:to-green-50 dark:hover:from-green-800/50 dark:hover:via-green-900/30 dark:hover:to-green-950/10",
      icon: "text-green-600 dark:text-green-400",
    },
    icon: <VscCode className="w-8 h-8" />,
  },
  "Teaching Materials": {
    title: "Educational Materials",
    href: "/teaching-materials",
    description: "Interactive resources and visualizations for learning",
    colorClasses: {
      background:
        "from-teal-100 via-teal-50 to-white dark:from-teal-900/50 dark:via-teal-950/30 dark:to-transparent",
      hover:
        "hover:from-teal-200 hover:via-teal-100 hover:to-teal-50 dark:hover:from-teal-800/50 dark:hover:via-teal-900/30 dark:hover:to-teal-950/10",
      icon: "text-teal-600 dark:text-teal-400",
    },
    icon: <LuBookOpen className="w-8 h-8" />,
  },
  Tools: {
    title: "Tools",
    href: "/tools",
    description: "Utilities for developers, language learners, and makers",
    colorClasses: {
      background:
        "from-indigo-100 via-indigo-50 to-white dark:from-indigo-900/50 dark:via-indigo-950/30 dark:to-transparent",
      hover:
        "hover:from-indigo-200 hover:via-indigo-100 hover:to-indigo-50 dark:hover:from-indigo-800/50 dark:hover:via-indigo-900/30 dark:hover:to-indigo-950/10",
      icon: "text-indigo-600 dark:text-indigo-400",
    },
    icon: <LuWrench className="w-8 h-8" />,
  },
  Humor: {
    title: "Humor",
    href: "https://osteele.notion.site/humor",
    description: "Games, toys, and playful experiments",
    external: true,
    colorClasses: {
      background:
        "from-rose-100 via-rose-50 to-white dark:from-rose-900/50 dark:via-rose-950/30 dark:to-transparent",
      hover:
        "hover:from-rose-200 hover:via-rose-100 hover:to-rose-50 dark:hover:from-rose-800/50 dark:hover:via-rose-900/30 dark:hover:to-rose-950/10",
      icon: "text-rose-600 dark:text-rose-400",
    },
    icon: <IoGameControllerOutline className="w-8 h-8" />,
  },
  Art: {
    title: "Art",
    href: "https://osteele.notion.site/art",
    description: "Interactive, conceptual, and other digital art",
    external: true,
    colorClasses: {
      background:
        "from-pink-100 via-pink-50 to-white dark:from-pink-900/50 dark:via-pink-950/30 dark:to-transparent",
      hover:
        "hover:from-pink-200 hover:via-pink-100 hover:to-pink-50 dark:hover:from-pink-800/50 dark:hover:via-pink-900/30 dark:hover:to-pink-950/10",
      icon: "text-pink-600 dark:text-pink-400",
    },
    icon: <PiPaintBrush className="w-8 h-8" />,
  },
} as const satisfies Record<string, Category>;

const CATEGORY_ORDER = [
  "Software",
  "Tools",
  "Products",
  "Teaching",
  "Teaching Materials",
  "Photography",
  "Art",
  "Woodworking",
  "Humor",
] as const;

function CategoryCard({ category }: { category: Category }) {
  const linkProps = category.external
    ? { target: "_blank", rel: "noopener noreferrer" }
    : {};

  return (
    <Link
      href={category.href}
      className={`group p-8 rounded-xl bg-[size:150%] bg-gradient-to-br ${category.colorClasses.background} ${category.colorClasses.hover}
        transition-all duration-300 ease-out
        hover:scale-[1.02] hover:shadow-lg dark:hover:shadow-black/30 hover:bg-right-bottom
        flex flex-col h-full overflow-hidden`}
      {...linkProps}
    >
      <div className="flex items-center gap-4 h-16 mb-4">
        <span
          className={`${category.colorClasses.icon} transition-all duration-300 group-hover:scale-110 group-hover:rotate-6`}
        >
          {category.icon}
        </span>
        <h2 className="text-2xl font-semibold break-words">{category.title}</h2>
      </div>
      <p className="text-gray-600 dark:text-gray-300 min-h-[3rem]">
        {category.description}
      </p>
    </Link>
  );
}

function CategoryGrid() {
  return (
    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
      {CATEGORY_ORDER.map((key) => (
        <div key={CATEGORIES[key].href} className="w-full">
          <CategoryCard category={CATEGORIES[key]} />
        </div>
      ))}
    </div>
  );
}

export default function HomePage() {
  return (
    <PageLayout>
      <div className="relative isolate">
        <div className="mx-auto max-w-5xl px-6 py-24 sm:py-32 lg:px-8">
          <div className="mx-auto max-w-2xl text-center">
            <h1 className="text-4xl font-bold tracking-tight sm:text-6xl">
              Oliver Steele
            </h1>
            <p className="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
              Software engineer, educator, and maker passionate about building tools that empower creativity and learning.
              With experience at Apple, Nest Labs, and various startups, I combine technical expertise with a
              love for teaching and creative exploration.
            </p>
            <div className="mt-10 flex items-center justify-center gap-x-6">
              <Link
                href="/software"
                className="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
              >
                View Projects
              </Link>
              <Link href="/photography" className="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100">
                See some of my photos <span aria-hidden="true">→</span>
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div className="mx-auto max-w-5xl px-6 pb-24">
        <h2 className="text-2xl font-bold mb-8">Featured Work</h2>
        <CategoryGrid />
      </div>
    </PageLayout>
  );
}
