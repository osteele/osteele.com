import Link from "next/link";
import { PageLayout } from "@/components/page-layout";

type CategoryCardProps = {
  href: string;
  title: string;
  description: string;
  icon: React.ReactNode;
  colorClasses: {
    background: string;
    hover: string;
    icon: string;
  };
  external?: boolean;
};

function CategoryCard({
  href,
  title,
  description,
  icon,
  colorClasses,
  external,
}: CategoryCardProps) {
  const linkProps = external
    ? { target: "_blank", rel: "noopener noreferrer" }
    : {};

  return (
    <Link
      href={href}
      className={`group p-8 rounded-xl bg-gradient-to-br ${colorClasses.background} ${colorClasses.hover} transition-colors`}
      {...linkProps}
    >
      <div className="flex items-center gap-4 mb-4">
        <span className={colorClasses.icon}>{icon}</span>
        <h2 className="text-2xl font-semibold">{title}</h2>
      </div>
      <p className="text-gray-600 dark:text-gray-300">{description}</p>
    </Link>
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
  const categoryData = {
    Products: {
      title: "Products",
      href: "/products",
      description: "Commercial products and applications",
      colorClasses: {
        background:
          "from-blue-50 to-blue-100 dark:from-blue-950/50 dark:to-blue-900/50",
        hover:
          "hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-900/50 dark:hover:to-blue-800/50",
        icon: "text-blue-600 dark:text-blue-400",
      },
      icon: (
        <svg className="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
        </svg>
      ),
    },
    Teaching: {
      title: "Teaching",
      href: "/teaching",
      description: "Computing, design, and physical computing courses",
      colorClasses: {
        background:
          "from-purple-50 to-purple-100 dark:from-purple-950/50 dark:to-purple-900/50",
        hover:
          "hover:from-purple-100 hover:to-purple-200 dark:hover:from-purple-900/50 dark:hover:to-purple-800/50",
        icon: "text-purple-600 dark:text-purple-400",
      },
      icon: (
        <svg className="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3L1 9l11 6l11-6l-11-6zM1 9v6l11 6l11-6V9L12 15L1 9z" />
        </svg>
      ),
    },
    Photography: {
      title: "Photography",
      href: "https://osteele.notion.site/photography",
      description: "Visual storytelling through the lens",
      external: true,
      colorClasses: {
        background:
          "from-amber-50 to-amber-100 dark:from-amber-950/50 dark:to-amber-900/50",
        hover:
          "hover:from-amber-100 hover:to-amber-200 dark:hover:from-amber-900/50 dark:hover:to-amber-800/50",
        icon: "text-amber-600 dark:text-amber-400",
      },
      icon: (
        <svg className="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M20 4h-3.17L15 2H9L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 11.5V13H9v2.5L5.5 12 9 8.5V11h6V8.5l3.5 3.5-3.5 3.5z" />
        </svg>
      ),
    },
    Woodworking: {
      title: "Woodworking",
      href: "/woodworking",
      description: "Handcrafted furniture and wooden objects",
      colorClasses: {
        background:
          "from-orange-50 to-orange-100 dark:from-orange-950/50 dark:to-orange-900/50",
        hover:
          "hover:from-orange-100 hover:to-orange-200 dark:hover:from-orange-900/50 dark:hover:to-orange-800/50",
        icon: "text-orange-600 dark:text-orange-400",
      },
      icon: (
        <svg className="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M13 6v2h6v3l-4 4H9l-4-4V8h6V6h2M3 13h2v8h2v-8h2v-2H3v2m14 0h2v8h2v-8h2v-2h-6v2z" />
        </svg>
      ),
    },
    Software: {
      title: "Software",
      href: "/software",
      description: "Open source projects and code experiments",
      colorClasses: {
        background:
          "from-green-50 to-green-100 dark:from-green-950/50 dark:to-green-900/50",
        hover:
          "hover:from-green-100 hover:to-green-200 dark:hover:from-green-900/50 dark:hover:to-green-800/50",
        icon: "text-green-600 dark:text-green-400",
      },
      icon: (
        <svg className="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M14.6 16.6l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4m-5.2 0L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4z" />
        </svg>
      ),
    },
    "Teaching Materials": {
      title: "Educational Materials",
      href: "/teaching-materials",
      description: "Learning resources and visualizations",
      colorClasses: {
        background:
          "from-teal-50 to-teal-100 dark:from-teal-950/50 dark:to-teal-900/50",
        hover:
          "hover:from-teal-100 hover:to-teal-200 dark:hover:from-teal-900/50 dark:hover:to-teal-800/50",
        icon: "text-teal-600 dark:text-teal-400",
      },
      icon: (
        <svg className="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 3L1 9l11 6l11-6l-11-6zM1 9v6l11 6l11-6V9L12 15L1 9z" />
        </svg>
      ),
    },
    Tools: {
      title: "Tools",
      href: "/tools",
      description: "Utilities for developers, creators, and makers",
      colorClasses: {
        background:
          "from-indigo-50 to-indigo-100 dark:from-indigo-950/50 dark:to-indigo-900/50",
        hover:
          "hover:from-indigo-100 hover:to-indigo-200 dark:hover:from-indigo-900/50 dark:hover:to-indigo-800/50",
        icon: "text-indigo-600 dark:text-indigo-400",
      },
      icon: (
        <svg className="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
        </svg>
      ),
    },
    Fun: {
      title: "Fun",
      href: "https://osteele.notion.site/fun",
      description: "Games, toys, and playful experiments",
      external: true,
      colorClasses: {
        background:
          "from-rose-50 to-rose-100 dark:from-rose-950/50 dark:to-rose-900/50",
        hover:
          "hover:from-rose-100 hover:to-rose-200 dark:hover:from-rose-900/50 dark:hover:to-rose-800/50",
        icon: "text-rose-600 dark:text-rose-400",
      },
      icon: (
        <svg className="w-8 h-8" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5.5-2.5l7.51-3.49L17.5 6.5 9.99 9.99 6.5 17.5zm5.5-6.6c.61 0 1.1.49 1.1 1.1s-.49 1.1-1.1 1.1-1.1-.49-1.1-1.1.49-1.1 1.1-1.1z" />
        </svg>
      ),
    },
  } as const;

  // Define the order of categories
  const categoryOrder = [
    "Software",
    "Tools",
    "Products",
    "Teaching",
    "Teaching Materials",
    "Photography",
    "Woodworking",
    "Fun",
  ] as const;

  return (
    <PageLayout>
      <div className="max-w-5xl mx-auto px-4 py-8">
        {/* Hero Section - centered content */}
        <div className="relative mb-16 max-w-5xl mx-auto">
          <div className="absolute inset-0 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-950/30 dark:to-purple-950/30 -z-10" />
          <div className="max-w-4xl mx-auto py-24 min-h-[24rem] relative flex flex-col items-center text-center">
            <h1 className="text-6xl md:text-7xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400">
              Oliver Steele
            </h1>
            <p className="text-2xl md:text-3xl text-gray-600 dark:text-gray-300 max-w-2xl leading-relaxed">
              Making, teaching, writing, playing
            </p>

            {/* Decorative element - centered behind text */}
            <DecorativeBackground />
          </div>
        </div>

        {/* Category Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
          {categoryOrder.map((key) => (
            <CategoryCard key={categoryData[key].href} {...categoryData[key]} />
          ))}
        </div>
      </div>
    </PageLayout>
  );
}
