import Link from "next/link";
import { HomeIcon } from "@heroicons/react/24/outline";

interface PageLayoutProps {
  title?: string;
  children: React.ReactNode;
}

export function PageLayout({ title, children }: PageLayoutProps) {
  return (
    <main className="min-h-screen">
      {title && (
        <div className="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800">
          <div className="max-w-5xl mx-auto px-4 py-4">
            <div className="flex items-center gap-2 text-gray-600 dark:text-gray-400">
              <Link
                href="/"
                className="flex items-center gap-1 hover:text-gray-900 dark:hover:text-gray-100"
              >
                <HomeIcon className="w-4 h-4" />
                <span>Home</span>
              </Link>
              <span>/</span>
              <h1 className="text-gray-900 dark:text-gray-100 font-semibold">
                {title}
              </h1>
            </div>
          </div>
        </div>
      )}
      {children}
    </main>
  );
}
