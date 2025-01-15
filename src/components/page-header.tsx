import Link from "next/link";
import { HomeIcon } from "@heroicons/react/24/outline";
import { ChevronRightIcon } from "@heroicons/react/20/solid";
import { FaGithub, FaLinkedin, FaFlickr } from "react-icons/fa";
import { SiBluesky } from "react-icons/si";

interface PageHeaderProps {
  title?: string;
}

export function PageHeader({ title }: PageHeaderProps) {
  return (
    <nav className="w-full border-b border-gray-200 dark:border-gray-800">
      <div className="max-w-5xl mx-auto px-4 sm:px-6 pb-3">
        <div className="flex flex-wrap items-center justify-between gap-2">
          <div className="flex items-center gap-2 overflow-hidden">
            <Link
              href="/"
              className="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 inline-flex items-center flex-shrink-0"
            >
              <HomeIcon className="w-3.5 h-3.5 mr-2" />
              Home
            </Link>
            {title && (
              <>
                <ChevronRightIcon className="w-4 h-4 text-gray-400 flex-shrink-0" />
                <h1 className="text-lg font-semibold truncate">{title}</h1>
              </>
            )}
          </div>

          <div className="flex items-center gap-4 flex-shrink-0">
            <Link
              href="https://github.com/osteele"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <FaGithub className="w-5 h-5" />
            </Link>
            <Link
              href="https://bsky.app/profile/osteele.com"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <SiBluesky className="w-5 h-5" />
            </Link>
            <Link
              href="https://www.linkedin.com/in/osteele"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <FaLinkedin className="w-5 h-5" />
            </Link>
            <Link
              href="https://www.flickr.com/photos/osteele"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <FaFlickr className="w-5 h-5" />
            </Link>
          </div>
        </div>
      </div>
    </nav>
  );
}
