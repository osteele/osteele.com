import {
  AcademicCapIcon,
  BeakerIcon,
  CameraIcon,
  CubeIcon,
  PuzzlePieceIcon,
  WrenchScrewdriverIcon,
} from "@heroicons/react/24/outline";
import { EnvelopeIcon, PhotoIcon } from "@heroicons/react/24/solid";
import { RefAttributes } from "react";

export default function Home() {
  const cards = [
    {
      title: "Products",
      icon: CubeIcon,
      description: "Commercial products and applications",
      color:
        "from-blue-500/20 to-blue-500/5 hover:from-blue-500/30 hover:to-blue-500/10",
      href: "/products",
    },
    {
      title: "Software",
      icon: BeakerIcon,
      description: "Open source projects and code experiments",
      color:
        "from-purple-500/20 to-purple-500/5 hover:from-purple-500/30 hover:to-purple-500/10",
      href: "https://code.osteele.com",
    },
    {
      title: "Photography",
      icon: CameraIcon,
      description: "Visual storytelling through the lens",
      color:
        "from-amber-500/20 to-amber-500/5 hover:from-amber-500/30 hover:to-amber-500/10",
      href: "https://flickr.com/photos/osteele/",
    },
    {
      title: "Woodworking",
      icon: WrenchScrewdriverIcon,
      description: "Handcrafted furniture and wooden objects",
      color:
        "from-orange-500/20 to-orange-500/5 hover:from-orange-500/30 hover:to-orange-500/10",
    },
    {
      title: "Education",
      icon: AcademicCapIcon,
      description: "Teaching materials and learning resources",
      color:
        "from-green-500/20 to-green-500/5 hover:from-green-500/30 hover:to-green-500/10",
      href: "/education",
    },
    {
      title: "Toys",
      icon: PuzzlePieceIcon,
      description: "Humorous and experimental projects",
      color:
        "from-pink-500/20 to-pink-500/5 hover:from-pink-500/30 hover:to-pink-500/10",
    },
  ];

  const socialLinks = [
    {
      name: "Email",
      icon: EnvelopeIcon,
      href: "mailto:steele@osteele.com",
      label: "Email Oliver Steele",
    },
    {
      name: "GitHub",
      icon: (props: RefAttributes<SVGSVGElement>) => (
        <svg fill="currentColor" viewBox="0 0 24 24" {...props}>
          <path
            fillRule="evenodd"
            d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
            clipRule="evenodd"
          />
        </svg>
      ),
      href: "https://github.com/osteele",
      label: "GitHub profile",
    },
    {
      name: "Flickr",
      icon: PhotoIcon,
      href: "https://flickr.com/photos/osteele",
      label: "Flickr photos",
    },
    {
      name: "BlueSky",
      icon: (props: RefAttributes<SVGSVGElement>) => (
        <svg fill="currentColor" viewBox="0 0 24 24" {...props}>
          <path d="M12.002 2c5.523 0 10 4.477 10 10s-4.477 10-10 10-10-4.477-10-10 4.477-10 10-10zm0 1.5c-4.694 0-8.5 3.806-8.5 8.5s3.806 8.5 8.5 8.5 8.5-3.806 8.5-8.5-3.806-8.5-8.5-8.5zm.503 5.75l3.839 3.551c.467.432.564 1.159.186 1.657l-.09.116c-.446.482-1.205.518-1.674.086l-2.264-2.095v2.515c0 .885-.714 1.6-1.6 1.6-.885 0-1.6-.715-1.6-1.6v-2.515l-2.264 2.095c-.469.432-1.228.396-1.674-.086l-.09-.116c-.378-.498-.281-1.225.186-1.657l3.839-3.551c.466-.432 1.228-.396 1.674.086l.09.116c.378.498.281 1.225-.186 1.657l-1.826 1.689 1.826-1.689c.467-.432 1.205-.518 1.674-.086l.09.116c.378.498.281 1.225-.186 1.657l-1.826 1.689 1.826-1.689z" />
        </svg>
      ),
      href: "https://bsky.app/profile/osteele.com",
      label: "BlueSky profile",
    },
    {
      name: "LinkedIn",
      icon: (props: RefAttributes<SVGSVGElement>) => (
        <svg fill="currentColor" viewBox="0 0 24 24" {...props}>
          <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
        </svg>
      ),
      href: "https://www.linkedin.com/in/osteele",
      label: "LinkedIn profile",
    },
  ];

  return (
    <div className="flex flex-col min-h-screen">
      <main className="flex-1 flex flex-col items-center gap-8 max-w-5xl mx-auto p-8 sm:p-20">
        <h1 className="text-5xl font-bold mt-16 mb-4">Oliver Steele</h1>
        <p className="text-xl text-gray-600 dark:text-gray-300 mb-12">
          Making, teaching, writing, playing
        </p>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 w-full">
          {cards.map((card) => (
            <a
              key={card.title}
              href={card.href}
              className={`p-6 rounded-lg bg-gradient-to-br ${card.color}
                transition-all duration-300 backdrop-blur-sm
                dark:text-white/90`}
            >
              <div className="flex items-center gap-3 mb-4">
                <card.icon className="h-6 w-6" />
                <h2 className="text-xl font-semibold">{card.title}</h2>
              </div>
              <p className="text-gray-600 dark:text-gray-300">
                {card.description}
              </p>
            </a>
          ))}
        </div>
      </main>

      <footer className="mt-auto py-8 px-4">
        <div className="max-w-5xl mx-auto flex justify-center space-x-6">
          {socialLinks.map((item) => (
            <a
              key={item.name}
              href={item.href}
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300"
              aria-label={item.label}
            >
              <item.icon className="h-6 w-6" />
            </a>
          ))}
        </div>
      </footer>
    </div>
  );
}
