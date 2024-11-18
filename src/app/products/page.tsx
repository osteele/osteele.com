import Link from "next/link";
import { ArrowRightIcon } from "@heroicons/react/24/outline";

interface Product {
  title: string;
  description: string;
  href: string;
  year?: string;
}

export default function Products() {
  const products: Product[] = [
    {
      title: "Apple Dylan",
      description:
        "A programming language based on Common Lisp and Smalltalk, developed at Apple Cambridge.",
      href: "/products/apple-dylan",
      year: "1994-1995",
    },
    {
      title: "Nest Learning Thermostat",
      description:
        "A WiFi-connected thermostat with an LCD display, developed by ex-Apple people.",
      href: "/products/nest-learning-thermostat",
      year: "2010-2014",
    },
    {
      title: "QuickDraw GX",
      description:
        "An early-90s 2D graphics and geometry library for the Macintosh System 7.",
      href: "/products/quickdraw-gx",
      year: "1989-1994",
    },
    {
      title: "Laszlo Presentation Server",
      description:
        "An XML-based platform for writing Rich Internet Applications (2000-2010).",
      href: "/products/laszlo-presentation-server",
      year: "2002-2007",
    },
    {
      title: "Pogo Joe",
      description: "A Q*bert-inspired game for the Commodore 64.",
      href: "/products/pogo-joe",
      year: "1983",
    },
    {
      title: "BrowseGoods",
      description:
        "A visual Amazon catalog browser using novel display algorithms (2007).",
      href: "/products/browsegoods",
      year: "2007",
    },
    {
      title: "Stylecart",
      description:
        "A visual shopping cart with drag-and-drop paper doll arrangement (2007).",
      href: "/products/stylecart",
      year: "2007",
    },
    {
      title: "Laszlo Webtop Calendar",
      description:
        "An early web-based calendar with iCalendar/WebDAV integration (2008).",
      href: "/products/laszlo-webtop-calendar",
      year: "2008",
    },
  ];

  return (
    <div className="max-w-5xl mx-auto p-8">
      <div className="mb-12">
        <h1 className="text-4xl font-bold mb-4">Products</h1>
        <p className="text-xl text-gray-600 dark:text-gray-300">
          Commercial products and applications I&apos;ve worked on over the
          years
        </p>
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        {products.map((product) => (
          <Link
            key={product.title}
            href={product.href}
            className="group block p-6 rounded-lg border border-gray-200 hover:border-gray-300
              transition-all duration-200 hover:shadow-md bg-white dark:bg-gray-800
              dark:border-gray-700 dark:hover:border-gray-600"
          >
            <div className="flex justify-between items-start mb-2">
              <h2
                className="text-xl font-semibold group-hover:text-blue-600
                dark:group-hover:text-blue-400 transition-colors"
              >
                {product.title}
              </h2>
              <ArrowRightIcon
                className="h-5 w-5 text-gray-400 group-hover:text-blue-600
                dark:group-hover:text-blue-400 transition-colors"
              />
            </div>
            {product.year && (
              <div className="text-sm text-gray-500 dark:text-gray-400 mb-2">
                {product.year}
              </div>
            )}
            <p className="text-gray-600 dark:text-gray-300">
              {product.description}
            </p>
          </Link>
        ))}
      </div>
    </div>
  );
}
