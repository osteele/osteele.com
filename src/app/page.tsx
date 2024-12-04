import Link from "next/link";
import { PageLayout } from "@/components/page-layout";
import { FiBox } from "react-icons/fi";
import { PiGraduationCap, PiCamera, PiToolbox, PiPaintBrush } from "react-icons/pi";
import { VscCode } from "react-icons/vsc";
import { LuBookOpen, LuWrench } from "react-icons/lu";
import { IoGameControllerOutline } from "react-icons/io5";
import { PageHeader } from "@/components/page-header";

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
      "I've shipped some products at Apple Computer, Nest Labs, and startups",
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
      "Computing, design, and physical computing courses at Olin College and NYU Shanghai",
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
    description: "Travel and street photography",
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
    description: "Handcrafted furniture and wooden objects",
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
    description: "Open source projects and code experiments",
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
    description: "Learning resources and visualizations",
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
  Fun: {
    title: "Fun",
    href: "https://osteele.notion.site/fun",
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
  "Fun",
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

function DecorativeBackground() {
  return (
    <div className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 opacity-10 dark:opacity-5">
      <svg viewBox="0 0 200 200" className="w-full h-full text-blue-600">
        {/* Background shape */}
        <path
          fill="currentColor"
          opacity="0.3"
          d="M45.3,-59.1C58.9,-51.1,70.3,-37.7,75.2,-22.1C80.1,-6.5,78.5,11.2,71.3,26.3C64.1,41.4,51.3,53.8,36.5,61.5C21.7,69.2,4.9,72.1,-11.1,69.7C-27.1,67.3,-42.3,59.5,-54.1,47.7C-65.9,35.9,-74.3,20,-76.1,3C-77.9,-14,-73.1,-31.1,-62.3,-43.6C-51.5,-56.1,-34.7,-64,-18.1,-67.7C-1.5,-71.3,14.9,-70.7,29.8,-67.1C44.8,-63.5,58.3,-56.9,45.3,-59.1Z"
          transform="translate(100 100)"
        />

        {/* Code brackets - representing software */}
        <path
          d="M60 70l-25 25 25 25M140 70l25 25-25 25"
          stroke="currentColor"
          strokeWidth="8"
          strokeLinecap="round"
          strokeLinejoin="round"
          fill="none"
        />

        {/* Pencil - representing teaching/writing */}
        <path
          fill="currentColor"
          d="M90 50l20-20 20 20-20 20zM85 55l-15 45 15-5 15 5z"
        />

        {/* Camera aperture - representing photography */}
        <path
          fill="currentColor"
          d="M100 140a15 15 0 1 1 0-30 15 15 0 0 1 0 30z"
        />
        <path
          fill="none"
          stroke="currentColor"
          strokeWidth="4"
          d="M85 125l30 0M100 110l0 30"
          strokeLinecap="round"
        />

        {/* Woodworking tools */}
        <path
          fill="currentColor"
          d="M40 40l15-15 5 5-15 15zM35 45l-10 10 5 5 10-10z"
          transform="rotate(-15, 40, 40)"
        />
      </svg>
    </div>
  );
}

export default function HomePage() {
  return (
    <PageLayout>
      <PageHeader />
      <div className="max-w-5xl mx-auto px-6 py-12">
        {/* Hero Section with new typography */}
        <div className="relative mb-24 max-w-5xl mx-auto">
          <div className="absolute inset-0 bg-gradient-to-r from-[#FF6B4A]/10 to-[#FF8A6B]/10 dark:from-[#FF6B4A]/5 dark:to-[#FF8A6B]/5 -z-10" />
          <div className="max-w-4xl mx-auto py-32 min-h-[24rem] relative flex flex-col items-center text-center">
            <h1 className="font-serif text-7xl md:text-8xl font-bold mb-8 tracking-tight text-gray-900 dark:text-gray-100">
              Oliver Steele
            </h1>
            <p className="text-2xl md:text-3xl text-[#FF6B4A] dark:text-[#FF8A6B] max-w-2xl leading-relaxed font-light">
              Making, teaching, writing, playing
            </p>
            <DecorativeBackground />
          </div>
        </div>

        {/* Section Title with serif typography */}
        <div className="mb-16 text-center">
          <h2 className="font-serif text-4xl md:text-5xl text-gray-900 dark:text-gray-100 mb-6">
            Projects & Interests
          </h2>
        </div>

        {/* Category Grid */}
        <CategoryGrid />
      </div>
    </PageLayout>
  );
}
