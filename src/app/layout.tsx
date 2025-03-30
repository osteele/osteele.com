import type { Metadata } from "next";
import { Inter } from "next/font/google";
import Link from "next/link";
import { FaFlickr, FaGithub, FaLinkedin } from "react-icons/fa";
import { SiBluesky } from "react-icons/si";
import "./globals.css";

const inter = Inter({ subsets: ["latin"] });

export const metadata: Metadata = {
  title: {
    default: "Oliver Steele",
    template: "%s | Oliver Steele",
  },
  description: "Making, teaching, writing, playing",
  openGraph: {
    title: "Oliver Steele",
    description: "Making, teaching, writing, playing",
    url: "https://osteele.com",
    siteName: "Oliver Steele",
  },
  icons: {
    icon: [
      { url: "/favicon.ico" },
      { url: "/favicon.svg", type: "image/svg+xml" },
    ],
    apple: [{ url: "/apple-touch-icon.png" }],
  },
  robots: {
    index: true,
    follow: true,
  },
  authors: [{ name: "Oliver Steele", url: "https://osteele.com" }],
};

function Header() {
  return (
    <header className="fixed top-0 w-full bg-white/80 dark:bg-black/80 backdrop-blur-sm z-[100] border-b border-gray-200 dark:border-gray-800">
      <nav className="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
        <Link href="/" className="text-xl font-semibold">
          Oliver Steele
        </Link>
        <div className="flex items-center gap-6">
          <Link href="/software" className="hover:text-blue-600 dark:hover:text-blue-400">Projects</Link>
          <Link href="/tools" className="hover:text-blue-600 dark:hover:text-blue-400">Tools</Link>
          <Link href="/language-learning" className="hover:text-blue-600 dark:hover:text-blue-400">Language</Link>
          <Link href="/photography" className="hover:text-blue-600 dark:hover:text-blue-400">Photos</Link>
          {/* <a href="#contact" className="hover:text-blue-600 dark:hover:text-blue-400">Contact</a> */}
        </div>
      </nav>
    </header>
  );
}

function Footer() {
  return (
    <footer className="mt-auto border-t border-gray-200 dark:border-gray-800">
      <div className="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        <div className="flex flex-col items-center gap-4">
          <div className="flex items-center gap-6">
            <Link
              href="https://github.com/osteele"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <FaGithub className="w-6 h-6" />
            </Link>
            <Link
              href="https://bsky.app/profile/osteele.com"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <SiBluesky className="w-6 h-6" />
            </Link>
            <Link
              href="https://www.linkedin.com/in/osteele"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <FaLinkedin className="w-6 h-6" />
            </Link>
            <Link
              href="https://www.flickr.com/photos/osteele"
              target="_blank"
              rel="noopener noreferrer"
              className="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
            >
              <FaFlickr className="w-6 h-6" />
            </Link>
          </div>
          <p className="text-sm text-gray-500 dark:text-gray-400">
            {new Date().getFullYear()} Oliver Steele. All rights reserved.
          </p>
        </div>
      </div>
    </footer>
  );
}

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en" className="h-full">
      <body className={`${inter.className} flex flex-col min-h-full`}>
        <Header />
        <main className="pt-16">
          {children}
        </main>
        <Footer />
      </body>
    </html>
  );
}
