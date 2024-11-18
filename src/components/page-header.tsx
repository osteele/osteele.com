import Link from "next/link";
import { HomeIcon } from "@heroicons/react/24/outline";
import { ChevronRightIcon } from "@heroicons/react/20/solid";

interface PageHeaderProps {
  title?: string;
}

export function PageHeader({ title }: PageHeaderProps) {
  return (
    <nav className="w-full border-b border-gray-200 dark:border-gray-800">
      <div className="max-w-5xl mx-auto px-4 sm:px-6 py-3 flex items-center gap-2">
        <Link
          href="/"
          className="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 inline-flex items-center"
        >
          <HomeIcon className="w-3.5 h-3.5 mr-2" />
          Home
        </Link>
        {title && (
          <>
            <ChevronRightIcon className="w-4 h-4 text-gray-400" />
            <span className="text-lg font-semibold">{title}</span>
          </>
        )}
      </div>
    </nav>
  );
}
